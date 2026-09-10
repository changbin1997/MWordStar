#!/usr/bin/env node
/**
 * 拷贝 typecho 主题打包文件到 MWordStar 目录
 * 用法: node pack-theme.js
 * 功能: 检查 dist、创建目录、拷贝文件、合并 css/js 引用、可选修改版本号
 */

'use strict';

const fs = require('fs');
const path = require('path');

const root = __dirname;
const distDir = path.join(root, 'dist');
const packDir = path.join(root, 'MWordStar');
const packAssetsDir = path.join(packDir, 'assets');
const packCssDir = path.join(packAssetsDir, 'css');
const packJsDir = path.join(packAssetsDir, 'js');
const packComponentsDir = path.join(packDir, 'components');
const packIncDir = path.join(packDir, 'inc');
const packLanguagesDir = path.join(packDir, 'languages');
const assetsJsDir = path.join(root, 'assets', 'js');
const assetsCssDir = path.join(root, 'assets', 'css');

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
  if (!fs.statSync(dir).isDirectory()) fail('目录创建失败: ' + rel(dir));
  console.log('[创建目录] ' + rel(dir));
}

function copyFile(src, dest) {
  if (!fs.existsSync(src)) fail('源文件不存在: ' + rel(src));
  fs.copyFileSync(src, dest);
  if (!fs.existsSync(dest) || fs.statSync(dest).size <= 0) {
    fail('拷贝失败: ' + rel(src) + ' -> ' + rel(dest));
  }
  console.log('[拷贝] ' + rel(src) + ' -> ' + rel(dest));
}

function listFiles(dir) {
  return fs.readdirSync(dir).filter(function (name) {
    return fs.statSync(path.join(dir, name)).isFile();
  });
}

function copyAllFiles(srcDir, destDir) {
  const files = listFiles(srcDir);
  if (files.length === 0) fail('目录为空，无文件可拷贝: ' + rel(srcDir));
  files.forEach(function (name) {
    copyFile(path.join(srcDir, name), path.join(destDir, name));
  });
}

function copyPhpFiles(srcDir, destDir) {
  const files = listFiles(srcDir).filter(function (name) {
    return name.endsWith('.php');
  });
  if (files.length === 0) fail('目录中没有 php 文件: ' + rel(srcDir));
  files.forEach(function (name) {
    copyFile(path.join(srcDir, name), path.join(destDir, name));
  });
}

function readText(file) {
  return fs.readFileSync(file, 'utf8');
}

function writeText(file, content) {
  fs.writeFileSync(file, content, 'utf8');
}

// 按行替换连续的代码块，保留第一行的缩进
function replaceBlock(file, blockMatches, newLine, desc) {
  const content = readText(file);
  const nl = content.includes('\r\n') ? '\r\n' : '\n';
  const lines = content.split(nl);
  const out = [];
  let replaced = false;
  for (let i = 0; i < lines.length; i++) {
    if (!replaced && i + blockMatches.length <= lines.length) {
      let matched = true;
      for (let k = 0; k < blockMatches.length; k++) {
        if (!lines[i + k].includes(blockMatches[k])) {
          matched = false;
          break;
        }
      }
      if (matched) {
        const indent = lines[i].match(/^\s*/)[0];
        out.push(indent + newLine);
        i += blockMatches.length - 1;
        replaced = true;
        continue;
      }
    }
    out.push(lines[i]);
  }
  if (!replaced) fail(desc + ' 未找到需要替换的内容');
  writeText(file, out.join(nl));
  console.log('[修改] ' + rel(file) + ': ' + desc);
}

// 字符串替换并验证
function replaceText(file, from, to, desc) {
  const content = readText(file);
  if (!content.includes(from)) fail(desc + ' 未找到目标文本');
  const output = content.split(from).join(to);
  if (output === content) fail(desc + ' 替换未生效');
  writeText(file, output);
  console.log('[修改] ' + rel(file) + ': ' + desc);
}

// 同步读取一行 stdin
function readLineSync() {
  const buffer = Buffer.alloc(4096);
  const bytes = fs.readSync(0, buffer, 0, buffer.length, null);
  return buffer.toString('utf8', 0, bytes).replace(/\r?\n$/, '');
}

// ============================================================
// 1. 检查 dist 目录
// ============================================================
console.log('== 1. 检查 dist 目录 ==');
if (!fs.existsSync(distDir) || !fs.statSync(distDir).isDirectory()) {
  fail('项目目录下未找到 dist 目录');
}
console.log('[检查] dist 目录存在');

// dist 中的文件名每次可能不一样，主要是数字不同
const styleCssFiles = fs.readdirSync(distDir).filter(function (name) {
  return /^style-\d+\.css$/.test(name);
});
if (styleCssFiles.length !== 1) {
  fail('dist 目录中 style-*.css 文件数量应为 1，实际为 ' + styleCssFiles.length);
}
const bundleJsFiles = fs.readdirSync(distDir).filter(function (name) {
  return /^bundle-\d+\.js$/.test(name);
});
if (bundleJsFiles.length !== 1) {
  fail('dist 目录中 bundle-*.js 文件数量应为 1，实际为 ' + bundleJsFiles.length);
}
const styleCssFile = styleCssFiles[0];
const bundleJsFile = bundleJsFiles[0];
console.log('[检查] 匹配到 ' + styleCssFile + ' 和 ' + bundleJsFile);

// ============================================================
// 2. 创建目录
// ============================================================
console.log('');
console.log('== 2. 创建目录结构 ==');
ensureDir(packDir);
ensureDir(packAssetsDir);
ensureDir(packComponentsDir);
ensureDir(packIncDir);
ensureDir(packLanguagesDir);
ensureDir(packCssDir);
ensureDir(packJsDir);

// ============================================================
// 3. 拷贝根目录 php 文件
// ============================================================
console.log('');
console.log('== 3. 拷贝根目录 php 文件 ==');
copyPhpFiles(root, packDir);

// ============================================================
// 4. 拷贝 LICENSE / README.md / screenshot.jpg
// ============================================================
console.log('');
console.log('== 4. 拷贝 LICENSE / README.md / screenshot.jpg ==');
['LICENSE', 'README.md', 'screenshot.jpg'].forEach(function (name) {
  copyFile(path.join(root, name), path.join(packDir, name));
});

// ============================================================
// 5. 拷贝 assets js 文件
// ============================================================
console.log('');
console.log('== 5. 拷贝 assets/js 文件 ==');
['chart.js', 'highlight.pack.js', 'options-panel.js', 'sw.js'].forEach(function (name) {
  copyFile(path.join(assetsJsDir, name), path.join(packJsDir, name));
});

// ============================================================
// 6. 拷贝 css 文件
// ============================================================
console.log('');
console.log('== 6. 拷贝 css 文件 ==');
copyFile(path.join(assetsCssDir, 'options-panel.css'), path.join(packCssDir, 'options-panel.css'));
copyFile(path.join(distDir, styleCssFile), path.join(packCssDir, styleCssFile));

// ============================================================
// 7. 拷贝 dist js 打包文件
// ============================================================
console.log('');
console.log('== 7. 拷贝 dist js 打包文件 ==');
copyFile(path.join(distDir, bundleJsFile), path.join(packJsDir, bundleJsFile));

// ============================================================
// 8. 拷贝 components / inc / languages
// ============================================================
console.log('');
console.log('== 8. 拷贝 components / inc / languages ==');
copyPhpFiles(path.join(root, 'components'), packComponentsDir);
copyAllFiles(path.join(root, 'inc'), packIncDir);
copyAllFiles(path.join(root, 'languages'), packLanguagesDir);

// ============================================================
// 9. 修改 header.php / footer.php 引用
// ============================================================
console.log('');
console.log('== 9. 合并 css / js 引用 ==');

const headerPath = path.join(packComponentsDir, 'header.php');
const newCssLink = "<link rel=\"stylesheet\" href=\"<?php $this->options->themeUrl('assets/css/" + styleCssFile + "'); ?>\" type=\"text/css\">";
replaceBlock(
  headerPath,
  [
    "themeUrl('assets/css/bootstrap.css')",
    "themeUrl('assets/css/icon.css')",
    "themeUrl('assets/css/style.css')"
  ],
  newCssLink,
  'header.php 合并 css 引用'
);

const footerPath = path.join(packComponentsDir, 'footer.php');
const newJsScript = "<script type=\"text/javascript\" src=\"<?php $this->options->themeUrl('assets/js/" + bundleJsFile + "'); ?>\"></script>";
replaceBlock(
  footerPath,
  [
    "themeUrl('assets/js/jquery-3.4.1.min.js')",
    "themeUrl('assets/js/jquery.pjax.js')",
    "themeUrl('assets/js/bootstrap.bundle.min.js')",
    "themeUrl('assets/js/jquery.qrcode.min.js')",
    "themeUrl('assets/js/clipboard.min.js')",
    "themeUrl('assets/js/app.js')"
  ],
  newJsScript,
  'footer.php 合并 js 引用'
);

// ============================================================
// 10. 版本号处理（可跳过）
// ============================================================
console.log('');
console.log('== 10. 版本号处理 ==');
process.stdout.write('[输入] 请输入版本号（例如 1.0），留空直接回车则跳过: ');
const version = readLineSync().trim();
if (version === '') {
  console.log('');
  console.log('[跳过] 未输入版本号，跳过 index.php 和 theme-config.php 的版本修改');
} else {
  replaceText(
    path.join(packDir, 'index.php'),
    '@version 开发板（暂无版本号）',
    '@version ' + version,
    'index.php 更新版本号为 ' + version
  );
  replaceText(
    path.join(packIncDir, 'theme-config.php'),
    '您现在使用的是 MWordStar 的开发版，开发板暂无版本号。',
    '您现在使用的是 MWordStar ' + version + '。',
    'theme-config.php 更新版本说明为 ' + version
  );
}

console.log('');
console.log('[完成] 打包文件拷贝完成');