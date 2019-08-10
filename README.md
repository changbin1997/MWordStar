# MWordStar

基于 Bootstrap4 开发的响应式两栏 Typecho 主题。

我之前使用的主题是 [handsome](https://www.ihewro.com/archives/489/) ，handsome 也是一款不错的 Typecho 主题。但是因为 handsome 使用的人太多，导致很多博客的外观都差不多，所以我准备开发一套自用的主题。

看了一下 Typecho 的主题开发文档， 发现不是太难。于是花了几天时间开发了这个主题。

这是我第一次开发 [Typecho](http://typecho.org/) 的主题，可能还有很多地方还不够完善，后续还会有更新。

主题演示地址：[https://www.misterma.com](https://www.misterma.com) 

主题下载地址：[https://github.com/changbin1997/MWordStar/releases](https://github.com/changbin1997/MWordStar/releases)

下面是主题首页截图：

![主题首页截图](https://www.misterma.com/img/%E4%B8%BB%E9%A2%98%E9%A6%96%E9%A1%B5%E6%88%AA%E5%9B%BE.jpeg)

主题支持响应式，下面是一些移动设备的截图：

![主题iPhone截图](https://www.misterma.com/img/%E4%B8%BB%E9%A2%98iPhone%E6%88%AA%E5%9B%BE.jpg)

![主题iPad截图](https://www.misterma.com/img/%E4%B8%BB%E9%A2%98iPad%E6%88%AA%E5%9B%BE.jpg)

## 主题介绍

### 外观设计

因为本人不擅长设计，所以外观还是传统的两栏布局。有些地方的设计参考了 WordPress 的一款名为：[Wordstar](https://wordpress.org/themes/wordstar/) 的主题的设计，Wordstar 也是我在用 WordPress 时比较喜欢的一款主题。

### 主题依赖

主题主要用到了一下几个库和框架：

- [Bootstrap](https://getbootstrap.com/)  外观和布局
- [jQuery](https://jquery.com/)  DOM 操作
- [highlight.js](https://highlightjs.org/)  代码高亮
- [IcoMoon](https://icomoon.io/)  字体图标

其中 IcoMoon 的字体图标是可定制的，所以这里只包含少量的常用图标。

### 主题文件结构

```shell
.
├── 404.php
├── archive.php
├── comments.php
├── css
│   ├── bootstrap.min.css
│   ├── icon.css
│   ├── style.css
│   └── vs2015.css
├── fonts
│   ├── icomoon.eot
│   ├── icomoon.svg
│   ├── icomoon.ttf
│   └── icomoon.woff
├── footer.php
├── functions.php
├── header.php
├── index.php
├── js
│   ├── app.js
│   ├── bootstrap.min.js
│   ├── highlight.pack.js
│   └── jquery-3.4.1.min.js
├── LICENSE
├── page-archive.php
├── page-links.php
├── page.php
├── post.php
├── screenshot.jpg
└── sidebar.php
```

注意！上面的文件结构是已经打包压缩的文件结构，在 [Releases](https://github.com/changbin1997/MWordStar/releases) 下载的 `zip` 包就是经过打包压缩的。

如果要下载未压缩的源代码可以直接克隆项目，未压缩的源代码中还包含 `style.scss` 文件。

### 主题安装

把主题上传到 Typecho 的 `usr/themes/` 目录，然后解压，你也可以先解压在上传。

解压后需要保证 `themes` 目录下 有一个 `MWordStar` 目录。

登录 Typecho 的后台管理，进入 `控制台` -> `外观`，如果看到 **MWordStar** 就点击 `启用`。

### 代码高亮

代码高亮的样式使用的是 VS2015 的暗色主题，和 Visual Studio Code 的默认主题差不多。

下面是支持代码高亮的语言：

- Apache
- AppleScript
- Bash
- C#
- C++
- CSS
- CoffeeScript
- Diff
- D
- Delphi
- Dockerfile
- Excel
- GO
- HTML/XML
- HTTP
- Ini/TOML
- JSON
- Java
- JavaScript
- Kotlin
- Less
- Makefile
- Markdown
- Nginx
- Objective-C
- PHP
- Python
- Perl
- Properties
- PowerShell
- Rust
- Ruby
- SQL
- Shell Session
- Swift
- SCSS
- TypeScript
- VB.NET
- YAML

如需更改代码高亮的样式可以上 [https://github.com/highlightjs/highlight.js/tree/master/src/styles](https://github.com/highlightjs/highlight.js/tree/master/src/styles) 下载 `css` 样式，下载完成后复制到主题的 `css` 文件夹，重命名为 `vs2015.css` 替换原来的文件。

### 文章归档

文章归档有两种显示方式，一种是在独立页面显示，如下：

![主题文章归档页截图.jpeg](https://www.misterma.com/img/%E4%B8%BB%E9%A2%98%E6%96%87%E7%AB%A0%E5%BD%92%E6%A1%A3%E9%A1%B5%E6%88%AA%E5%9B%BE.jpeg)

另一种就是侧边栏的按月份的文章归档，如下图：

![主题侧边栏文章归档截图](https://www.misterma.com/img/%E4%B8%BB%E9%A2%98%E4%BE%A7%E8%BE%B9%E6%A0%8F%E6%96%87%E7%AB%A0%E5%BD%92%E6%A1%A3%E6%88%AA%E5%9B%BE.jpeg)

默认使用的是侧边栏的文章归档。

如需使用独立页面的文章归档 可以建立一个独立页面，把 `自定义模板` 设置为 `文章归档`。

### 显示社交信息

社交信息会显示在侧边栏的最上方，默认是关闭的。如需开启显示社交信息可以在 `设置外观` 中选中 `显示社交信息`，然后在 `社交信息` 的输入框中输入 `JSON` 格式的信息。

社交信息 `JSON` 如下：

```json
[
    {
        "name": "facebook",
        "url": "https://www.facebook.com"
    },
    {
        "name": "twitter",
        "url": "https://twitter.com"
    },
    {
        "name": "weibo",
        "url": "https://weibo.com"
    }
]
```

其中 `name` 就是社交网站的名称，`url` 就是社交网站中你的主页的 `url`，社交网站名称需要用英文小写。

下面是支持的社交网站和英文小写名称：

| 网站名称  | 英文小写  |
| --------- | --------- |
| Facebook  | facebook  |
| Twitter   | twitter   |
| 微博      | weibo     |
| Instagram | instagram |
| Github    | github    |
| Telegram  | telegram  |
| Linkedin  | linkedin  |
| Steam     | steam     |

侧边栏社交信息效果如下：

![主题社交信息显示](https://www.misterma.com/img/%E4%B8%BB%E9%A2%98%E7%A4%BE%E4%BA%A4%E4%BF%A1%E6%81%AF%E6%98%BE%E7%A4%BA.jpeg)

图标的排列顺序取决于你 `JSON` 中的顺序。

### 友情链接

友情链接分为 `全站链接` 和 `内页链接` 。`全站链接` 会在每个页面的底部显示，`内页链接` 只会在单独的页面显示。

#### 设置全站链接

在主题外观设置中添加 `JSON` ，如下：

```json
[
    {
        "url": "https://www.baidu.com",
        "name": "百度",
        "title": "百度一下，你就知道。"
    },
    {
        "url": "https://www.misterma.com",
        "name": "Mr Ma`s Blog",
        "title": "我的编程学习笔记和一些计算机的实用教程"
    }
]
```

其中 `url` 和 `name` 为必填项。

效果如下：

![主题底部友链截图](https://www.misterma.com/img/%E4%B8%BB%E9%A2%98%E5%BA%95%E9%83%A8%E5%8F%8B%E9%93%BE%E6%88%AA%E5%9B%BE.jpeg)

#### 设置内页链接

新建一个独立页面，把 `自定义模板` 设置为 `友情链接` 。

在主题 `外观设置` 中添加 `JSON` ，如下：

```json
[
    {
        "url": "https://www.baidu.com",
        "name": "百度",
        "title": "百度一下，你就知道。",
        "logoUrl": "https://tupp.xyz/2019/08/05/15650063025d4819debebc6.jpg"
    },
    {
        "url": "https://www.misterma.com",
        "name": "Mr Ma`s Blog",
        "title": "我的编程学习笔记和一些计算机的实用教程",
        "logoUrl": "https://www.misterma.com/img/%E5%8D%9A%E5%AE%A2Logo.png"
    }
]
```

其中 `url` 和 `name` 是必填项。

效果如下：

![主题独立页友链截图](https://www.misterma.com/img/%E4%B8%BB%E9%A2%98%E7%8B%AC%E7%AB%8B%E9%A1%B5%E5%8F%8B%E9%93%BE%E6%88%AA%E5%9B%BE.jpeg)

### 文章头图

如果需要显示文章头图的话，在编辑文章的时候在下方的 `文章头图` 字段中输入图片的 `url` 。图片尺寸比例建议使用 8 比 3 的图片。

### 文章版权声明

文章版权声明会显示在文章的底部，在文章的编辑页面可以选择 `显示` 或 `不显示`，默认为 `显示`。

下面是文章版权声明的截图：

![主题文章版权声明的截图](https://www.misterma.com/img/%E4%B8%BB%E9%A2%98%E6%96%87%E7%AB%A0%E7%89%88%E6%9D%83%E5%A3%B0%E6%98%8E%E7%9A%84%E6%88%AA%E5%9B%BE.jpeg)

### 无障碍

上网对于大多数人来说是一件再简单不过的事，但是对于一些身体有缺陷的残障人士来说却是一件非常困难的事。

目前国内的很多网站都只注重外观，忽略了残障人士的可访问性。但是想要做好网站的用户体验，[无障碍](https://www.misterma.com/archives/264/) 适配肯定是少不了的。

本主题已在 [NVDA](http://www.nvda-project.org/) 和 [VoiceOver](https://www.apple.com/cn/accessibility/iphone/vision/) 这两款屏幕阅读器上做过测试，无论是 PC 还是 移动设备 都能完美朗读。主题颜色对比度也符合标准。

小提示：如果您是屏幕阅读器用户，为了您的浏览体验，不建议使用 IE 浏览器。

### 兼容性

因为本主题使用了 HTML5 和 CSS3，需要 IE10 及以上浏览器才能完美兼容。IE9 及以下浏览器显示可能会出现一些问题。

### 其它

我的个人博客：[https://www.misterma.com](https://www.misterma.com) 。

后续可能会把我的博客作为主题的演示站点。

主题 Github：[https://github.com/changbin1997/MWordStar](https://github.com/changbin1997/MWordStar)

本主题使用 [MIT License](https://github.com/changbin1997/MWordStar/blob/master/LICENSE) 开源。

如果您在使用本主题时遇到 Bug 或有任何问题和建议都可以在 [博客评论区](https://www.misterma.com/archives/812/#comments) 留言，也可以在 Github 的 [issues](https://github.com/changbin1997/MWordStar/issues) 反馈。

一般情况下在博客留言可以很快得到回复。