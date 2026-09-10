#!/usr/bin/env node
/**
 * 拷贝前端文件到 src 目录
 * 用法: node copy-frontend.js
 * 功能: 创建目录、拷贝 css/scss、字体、js 模块、生成 app.js、为模块添加 import
 */

'use strict';

const fs = require('fs');
const path = require('path');

const root = __dirname;
const assetsDir = path.join(root, 'assets');
const srcDir = path.join(root, 'src');
const styleDir = path.join(srcDir, 'style');
const fontsDir = path.join(styleDir, 'fonts');
const jsDir = path.join(srcDir, 'js');
const modulesDir = path.join(jsDir, 'modules');

// 输出错误并停止执行
function fail(message) {
  console.error('');
  console.error('[错误] ' + message);
  console.error('[停止] 脚本执行失败，请检查后重新运行');
  process.exit(1);
}

function rel(p) {
  return path.relative(root, p);
}

function ensureDir(dir) {
  fs.mkdirSync(dir, { recursive: true });
  if (!fs.statSync(dir).isDirectory()) {
    fail('目录创建失败: ' + rel(dir));
  }
  console.log('[创建目录] ' + rel(dir));
}

function copyFile(src, dest) {
  if (!fs.existsSync(src)) {
    fail('源文件不存在: ' + rel(src));
  }
  fs.copyFileSync(src, dest);
  if (!fs.existsSync(dest) || fs.statSync(dest).size <= 0) {
    fail('拷贝失败: ' + rel(src) + ' -> ' + rel(dest));
  }
  console.log('[拷贝] ' + rel(src) + ' -> ' + rel(dest));
}

function copyDir(srcDirName, destDirName) {
  const entries = fs.readdirSync(srcDirName);
  if (entries.length === 0) {
    fail('目录为空，无文件可拷贝: ' + rel(srcDirName));
  }
  entries.forEach(function (entry) {
    const src = path.join(srcDirName, entry);
    if (fs.statSync(src).isFile()) {
      copyFile(src, path.join(destDirName, entry));
    }
  });
}

// ============================================================
// 1. 创建目录
// ============================================================
console.log('== 1. 创建目录结构 ==');
ensureDir(srcDir);
ensureDir(jsDir);
ensureDir(styleDir);
ensureDir(fontsDir);
ensureDir(modulesDir);

// ============================================================
// 2. 拷贝 css / scss 文件
// ============================================================
console.log('');
console.log('== 2. 拷贝 css / scss 文件 ==');
[
  'bootstrap.css',
  'danger-color.scss',
  'dark-color.scss',
  'github-dark.min.css',
  'icon.css',
  'info-color.scss',
  'light-color.scss',
  'navbar-color.scss',
  'primary-color.scss',
  'stackoverflow-light.min.css',
  'style.scss',
  'success-color.scss',
  'sunburst.min.css',
  'warning-color.scss'
].forEach(function (name) {
  copyFile(path.join(assetsDir, 'css', name), path.join(styleDir, name));
});

// ============================================================
// 3. 拷贝字体文件
// ============================================================
console.log('');
console.log('== 3. 拷贝字体文件 ==');
copyDir(path.join(assetsDir, 'fonts'), fontsDir);

// ============================================================
// 4. 修改 icon.css 字体路径
// ============================================================
console.log('');
console.log('== 4. 修改 icon.css 字体路径 ==');
const iconCssPath = path.join(styleDir, 'icon.css');
let iconCss = fs.readFileSync(iconCssPath, 'utf8');
if (iconCss.includes('../fonts/')) {
  const iconCssNew = iconCss.split('../fonts/').join('./fonts/');
  if (iconCssNew === iconCss) {
    fail('icon.css 替换未生效');
  }
  fs.writeFileSync(iconCssPath, iconCssNew, 'utf8');
  console.log('[修改] src/style/icon.css: ../fonts/ -> ./fonts/');
} else if (iconCss.includes('./fonts/')) {
  console.log('[跳过] src/style/icon.css 已处理过字体路径');
} else {
  fail('icon.css 中既未找到 ../fonts/ 也未找到 ./fonts/');
}

// ============================================================
// 5. 拷贝 js modules
// ============================================================
console.log('');
console.log('== 5. 拷贝 js modules ==');
copyDir(path.join(assetsDir, 'js', 'modules'), modulesDir);

// ============================================================
// 6. 生成 src/js/app.js
// ============================================================
console.log('');
console.log('== 6. 生成 src/js/app.js ==');
const appSrc = path.join(assetsDir, 'js', 'app.js');
const appDest = path.join(jsDir, 'app.js');
const appText = fs.readFileSync(appSrc, 'utf8');

const marker = '$(function () {';
const bodyStart = appText.indexOf(marker);
if (bodyStart < 0) {
  fail('assets/js/app.js 中未找到 ' + marker);
}
const bodyEnd = appText.lastIndexOf('});');
if (bodyEnd < 0 || bodyEnd <= bodyStart) {
  fail('assets/js/app.js 中未找到回调结束标记 });');
}

const header = appText.slice(0, bodyStart);
const body = appText.slice(bodyStart + marker.length, bodyEnd);

if (!header.includes('/*!')) {
  fail('app.js 头部未找到版权注释');
}
if (!header.includes('import ')) {
  fail('app.js 头部未找到 import 引入');
}
if (body.trim().length === 0) {
  fail('app.js 回调内容为空');
}

const nl = appText.includes('\r\n') ? '\r\n' : '\n';
const appOutput = header + 'export default () => {' + body + '}' + nl;
if (!appOutput.includes('export default () => {')) {
  fail('app.js 生成失败');
}
fs.writeFileSync(appDest, appOutput, 'utf8');
console.log('[生成] src/js/app.js (版权注释 + import + 回调代码)');

// ============================================================
// 7. 为指定模块添加 import
// ============================================================
console.log('');
console.log('== 7. 为指定模块添加 import ==');

function addImportAfterLicense(file, importLine, desc) {
  let content = fs.readFileSync(file, 'utf8');
  if (content.includes(importLine)) {
    console.log('[跳过] ' + rel(file) + ' 已包含 import');
    return;
  }
  const nl = content.includes('\r\n') ? '\r\n' : '\n';
  const lines = content.split(nl);
  const closeIdx = lines.findIndex(function (line) {
    return line.includes('*/');
  });
  if (closeIdx < 0) {
    fail(desc + ': 未找到版权注释结束 */');
  }
  lines.splice(closeIdx + 1, 0, '', importLine);
  const output = lines.join(nl);
  if (!output.includes(importLine)) {
    fail(desc + ': import 插入失败');
  }
  fs.writeFileSync(file, output, 'utf8');
  console.log('[修改] ' + rel(file) + ': 已添加 import');
}

addImportAfterLicense(
  path.join(modulesDir, 'ArticleEngagement.js'),
  "import './../jquery.qrcode.min.js';",
  'ArticleEngagement.js'
);
addImportAfterLicense(
  path.join(modulesDir, 'CodeAndMath.js'),
  "import ClipboardJS from 'clipboard';",
  'CodeAndMath.js'
);
addImportAfterLicense(
  path.join(modulesDir, 'PJAX.js'),
  "import './../jquery.pjax.js';",
  'PJAX.js'
);

console.log('');
console.log('[完成] 所有文件处理完毕');