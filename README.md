这是我第一次开发 [Typecho](http://typecho.org/) 的主题，可能还有很多地方还不够完善，后续还会有更新。

主题演示地址：[https://www.misterma.com](https://www.misterma.com) 

主题下载地址：[https://github.com/changbin1997/MWordStar/releases](https://github.com/changbin1997/MWordStar/releases)

Releases 中打包的主题可能不是最新版本，如果您需要下载最新版本的主题可以直接克隆项目。

下面是主题首页截图：

![MWordStar主题首页截图1](https://www.misterma.com/img/MWordStar%E4%B8%BB%E9%A2%98%E9%A6%96%E9%A1%B5%E6%88%AA%E5%9B%BE1.jpeg)

![MWordStar主题首页截图2](https://www.misterma.com/img/MWordStar%E4%B8%BB%E9%A2%98%E9%A6%96%E9%A1%B5%E6%88%AA%E5%9B%BE2.jpeg)

## 主题特点

* 响应式设计
* 无障碍适配
* 代码高亮
* 长期维护
* 详细的 [使用说明](https://www.misterma.com/archives/819/)（必看）

## 主题介绍

### 外观设计

因为本人不擅长设计，所以外观还是传统的两栏布局。

### 主题依赖

主题主要用到了一下几个库和框架：

- [Bootstrap](https://getbootstrap.com/)  外观和布局
- [jQuery](https://jquery.com/)  DOM 操作
- [highlight.js](https://highlightjs.org/)  代码高亮
- [IcoMoon](https://icomoon.io/)  字体图标

其中 IcoMoon 的字体图标是可定制的，所以只包含了主题中出现的图标。

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

如果要下载未压缩的源代码可以直接克隆项目，未压缩的源代码的 style 文件是 `scss` 格式的，需要用 [Sass](https://sass-lang.com/) 编译为 `css` 才能使用。

### 主题安装

把主题上传到 Typecho 的 `usr/themes/` 目录，然后解压，你也可以先解压在上传。

解压后需要保证 `themes` 目录下 有一个 `MWordStar` 目录。

登录 Typecho 的后台管理，进入 `控制台` -> `外观`，如果看到 **MWordStar** 就点击 `启用`。

### 代码高亮

代码高亮的样式使用的是 VS2015 的暗色主题，和 Visual Studio Code 的默认主题差不多。主题支持 30 多种语言的代码高亮。详细的说明可以查看 [使用说明](https://www.misterma.com/archives/819/) 。

### 显示社交信息

社交信息会显示在巨幕站点副标题的下方，默认是关闭的。如需显示社交信息可以 [点此查看使用说明](https://www.misterma.com/archives/819/) 。

社交信息效果如下：

![MWordStar主题社交信息截图](https://www.misterma.com/img/MWordStar%E4%B8%BB%E9%A2%98%E7%A4%BE%E4%BA%A4%E4%BF%A1%E6%81%AF%E6%88%AA%E5%9B%BE.jpeg)

上图只包含一部分社交信息。

### 友情链接

友情链接分为 `全站友情链接`、`首页友情链接`、`内页友情链接`。`全站友情链接` 会在每个页面的侧边栏显示，`首页友情链接` 会在首页的侧边栏显示，`内页友情链接` 只会在 友情链接 的页面显示。

如需查看友情链接的设置说明可以访问：[使用说明](https://www.misterma.com/archives/819/) 。

### 无障碍

上网对于大多数人来说是一件再简单不过的事，但是对于一些身体有缺陷的残障人士来说却是一件非常困难的事。

目前国内的很多网站都只注重外观，忽略了残障人士的可访问性。但是想要做好网站的用户体验，[无障碍](https://www.misterma.com/archives/264/) 适配肯定是少不了的。

本主题专门为 屏幕阅读器 做了特别优化，并 在 [NVDA](http://www.nvda-project.org/) 和 [VoiceOver](https://www.apple.com/cn/accessibility/iphone/vision/) 这两款屏幕阅读器上做过测试，无论是 PC 还是 移动设备 都能完美朗读。主题颜色对比度也符合标准。

小提示：如果您是屏幕阅读器用户，为了您的浏览体验，不建议使用 IE 浏览器。

### Emoji 表情

主题评论区包含一个 Emoji 表情面板，您可以在后台启用或禁用。Emoji 表情面板包含 1466 个表情，这些表情都是按照分类动态加载的，您不用担心性能问题。

Emoji 表情面板也进行了无障碍适配，可支持键盘访问和屏幕阅读器朗读。

下面是 Emoji 表情面板的截图：

![emoji面板截图](https://www.misterma.com/img/emoji%E9%9D%A2%E6%9D%BF.jpeg)

### 兼容性

因为本主题使用了 HTML5 和 CSS3，需要 IE10 及以上浏览器才能完美兼容。IE9 及以下浏览器显示可能会出现一些问题。

### 插件适配

因为本人很少使用插件，所以目前适配的插件比较少。

下面是已适配的插件：

* [Sticky](https://plugins.typecho.me/plugins/sticky.html) 文章置顶插件

## 其它

主题 Github：[https://github.com/changbin1997/MWordStar](https://github.com/changbin1997/MWordStar)

码云仓库地址：[https://gitee.com/changbin1997/MWordStar](https://gitee.com/changbin1997/MWordStar)

本主题使用 [MIT License](https://github.com/changbin1997/MWordStar/blob/master/LICENSE) 开源。

如果您在使用本主题时遇到 Bug 或有任何问题和建议都可以在 [博客评论区](https://www.misterma.com/archives/812/#comments) 留言，也可以在 Github 的 [issues](https://github.com/changbin1997/MWordStar/issues) 反馈。

一般情况下在博客留言可以很快得到回复。