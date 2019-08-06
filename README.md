# MWordStar
基于 Bootstrap4 开发的 Typecho 主题。

这是我第一次开发 [Typecho](http://typecho.org/) 的主题，可能还有很多地方还不够完善，后续还会有更新。

下面是主题首页截图：

![首页截图](https://tupp.xyz/2019/08/05/15650002795d48025799710.jpeg)

主题支持响应式，下面是一些移动设备的截图：

![iphone个尺寸截图](https://tupp.xyz/2019/08/05/15649999615d480119b1881.png)

![ipad](https://tupp.xyz/2019/08/05/15650005205d4803487085d.jpeg)

## 主题介绍

### 外观设计

因为本人不擅长设计，所以外观还是传统的两栏布局。有些地方的设计参考了 WordPress 的一款名为：[Wordstar](https://wordpress.org/themes/wordstar/) 的主题的设计，Wordstar 也是我在用 WordPress 时比较喜欢的一款主题。

### 主题依赖

主题主要用到了一下几个库和框架：

* [Bootstrap](https://getbootstrap.com/)  外观和布局
* [jQuery](https://jquery.com/)  DOM 操作
* [highlight.js](https://highlightjs.org/)  代码高亮
* [IcoMoon](https://icomoon.io/)  字体图标

其中 IcoMoon 的字体图标是可定制的，所以这里只包含少量的常用图标。

### 代码高亮

代码高亮的主题使用的是 VS2015 的暗色主题，和 Visual Studio Code 的默认主题差不多。

下面是支持代码高亮的语言：

* Apache
* AppleScript
* Bash
* C#
* C++
* CSS
* CoffeeScript
* Diff
* D
* Delphi
* Dockerfile
* Excel
* GO
* HTML/XML
* HTTP
* Ini/TOML
* JSON
*  Java
* JavaScript
* Kotlin
* Less
* Makefile
* Markdown
* Nginx
* Objective-C
* PHP
* Python
* Perl
* Properties
* PowerShell
* Rust
* Ruby
* SQL
* Shell Session
* Swift
* SCSS
* TypeScript
* VB.NET
* YAML

如需更改代码高亮的主题可以上 [https://github.com/highlightjs/highlight.js/tree/master/src/styles](https://github.com/highlightjs/highlight.js/tree/master/src/styles) 下载 `css` 主题，下载完成后复制到主题的 `css` 文件夹，重命名为 `vs2015.css` 替换原来的文件。

### 文章归档

文章归档有两种显示方式，一种是在独立页面显示，如下：

![独立页面文章归档截图](https://tupp.xyz/2019/08/05/15650045345d4812f6e3bb3.jpeg)

另一种就是侧边栏的按月份的文章归档，如下图：

![侧边栏的文章归档截图](https://tupp.xyz/2019/08/05/15650046715d48137f00ae9.jpeg)

默认使用的是侧边栏的文章归档。

如需使用独立页面的文章归档 可以建立一个独立页面，把 `自定义模板` 设置为 `文章归档`。

### 友情链接

友情链接分为 `全站链接` 和 `内页链接` 。`全站链接` 会在每个页面的底部显示，`内页链接` 只会在单独的页面显示。

#### 设置全站链接

全站链接需要使用 `JSON` 格式数据，如下：

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

![全站链接的效果截图](https://tupp.xyz/2019/08/05/15650059295d48186900fa6.jpeg)

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

![友情链接页的截图](https://tupp.xyz/2019/08/05/15650066115d481b134425e.jpeg)

### 文章头图

如果需要显示文章头图的话，在编辑文章的时候在下方的 `文章头图` 字段中输入图片的 `url` 。图片尺寸比例建议使用 8 比 3 的图片。

### 无障碍

对于大多数人来说，上网是一件很简单的事情。但是对于一些身体有缺陷的残障人士来说，上网却是一件非常困难的事。

想要做好网站的用户体验，[无障碍](https://www.misterma.com/archives/264/) 适配肯定是少不了的。

本主题已在 [NVDA](http://www.nvda-project.org/) 和 [VoiceOver](https://www.apple.com/cn/accessibility/iphone/vision/) 这两款屏幕阅读器上做过测试，无论是 PC 还是 移动设备 都能完美朗读。

### 兼容性

因为本主题使用了 HTML5 和 CSS3，需要 IE10 及以上浏览器才能完美兼容。IE9 及以下浏览器显示可能会出现一些问题。

### 其它

我的个人博客：[https://www.misterma.com](https://www.misterma.com) 。

后续可能会把我的博客作为主题的演示站点。