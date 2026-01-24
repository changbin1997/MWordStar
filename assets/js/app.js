/*!
* HomePage: https://www.misterma.com
* GithubPage: https://github.com/changbin1997
* ProjectPage: https://github.com/changbin1997/MWordStar
* author: Changbin (changbin1997)
* Licensed under MIT
*/

import Lightbox from './modules/Lightbox.js';
import Emoji from './modules/Emoji.js';
import codeHighlightInit from './modules/codeHighlightInit.js';
import accessibilityInit from './modules/accessibilityInit.js';
import ColorAndLanguage from './modules/ColorAndLanguage.js';
import BootstrapStyle from './modules/BootstrapStyle.js';
import AvatarStyle from './modules/AvatarStyle.js';
import ArticleEngagement from './modules/ArticleEngagement.js';
import Directory from './modules/Directory.js';
import PJAX from './modules/PJAX.js';
import sidebarCoverImageInit from './modules/sidebarCoverImageInit.js';

$(function () {
  let inputFocus = false;  // 表单焦点状态

  // 图片灯箱初始化
  const lightbox = new Lightbox();
  lightbox.init();

  // Emoji面板初始化
  const emoji = new Emoji();
  emoji.init();

  // 语言和配色切换初始化
  const colorAndLanguage = new ColorAndLanguage();
  colorAndLanguage.init();

  // bootstrap样式初始化
  const bootstrapStyle = new BootstrapStyle();
  bootstrapStyle.init();

  // 头像样式初始化
  const avatarStyle = new AvatarStyle();
  avatarStyle.init();

  // 点赞初始化
  ArticleEngagement.likeInit();

  // 生成二维码
  ArticleEngagement.shareQrCode();

  // 目录初始化
  const directory = new Directory();
  directory.init();

  // 代码高亮初始化
  codeHighlightInit();

  // 一些可访问性相关的功能初始化
  accessibilityInit();

  // 侧边栏文章头图初始化
  sidebarCoverImageInit();

  // 图片懒加载
  lazyLoadImages();

  // 表单焦点事件初始化
  inputFocusInit();

  // pjax 初始化
  const pjax = new PJAX();
  pjax.init(() => {
    // PJAX 替换完成
    // 代码高亮初始化
    codeHighlightInit();

    // 一些可访问性相关的功能初始化
    accessibilityInit();

    // 侧边栏文章头图初始化
    sidebarCoverImageInit();

    // 图片懒加载
    lazyLoadImages();

    // 表单焦点事件初始化
    inputFocusInit();

    // 图片灯箱初始化
    lightbox.init();

    // Emoji初始化
    emoji.init();

    // 侧边栏的语言切换初始化
    colorAndLanguage.sidebarChangeLanguageInit();

    // bootstrap样式初始化
    bootstrapStyle.init();

    // 头像样式初始化
    avatarStyle.init();

    // 点赞初始化
    ArticleEngagement.likeInit();

    // 生成二维码
    ArticleEngagement.shareQrCode();

    // 移动设备目录开关按钮初始化
    directory.directoryBtnInit();
    // 目录初始化
    directory.init();
  });

  // 页面空白区域点击
  $('body').on('click', () => {
    // 如果表情面板处于开启状态就关闭表情面板
    if (emoji.isShow) $('#show-emoji-btn').click();
  });

  // 返回顶部按钮点击
  $('.to-top').on('click', () => {
    $('html').animate({
      scrollTop: 0
    }, 400);
    $('header .navbar-brand').get(0).focus();
    return false;
  });

  // 窗口尺寸改变
  window.addEventListener('resize', () => {
    // 调整侧边栏目录的尺寸
    directory.directorySize();
  });

  // 监听滚动条
  $(document).on('scroll', () => {
    // 返回顶部的按钮是否存在
    if ($('.to-top').length > 0) {
      // 如果滚动条高度 > 屏幕高度
      if ($(document).scrollTop() > window.innerHeight) {
        $('.to-top').removeClass('d-none');  // 显示返回顶部按钮
      } else {
        $('.to-top').addClass('d-none');  // 隐藏返回顶部按钮
      }
    }

    // 检测文章图片位置
    $('.load-img').each(function() {
      // 如果文章内的 img 进入可视区就加载图片
      if (
        $(this).offset().top < $(document).scrollTop() + window.innerHeight &&
        $(this).offset().top + $(this).height() > $(document).scrollTop()
      ) {
        if ($(this).attr('src') === undefined) {
          $(this).attr('src', $(this).attr('data-src'));
        }
      }
    });

    // 固定和取消固定侧边栏目录
    directory.directoryPosition();
  });

  // 全局快捷键
  $(document).on('keyup', ev => {
    // 右光标键
    if (ev.key === 'ArrowRight' || ev.keyCode === 39 && !inputFocus && !lightbox.isShow) {
      // 文章列表翻页
      if ($('.next .page-link').length) {
        location.href = $('.next .page-link').attr('href');
      }
      // 文章内容翻页
      if ($('.post-pagination .next-page').length) {
        location.href = $('.post-pagination .next-page').attr('href');
      }
    }

    // 左光标键
    if (ev.key === 'ArrowLeft' || ev.keyCode === 37 && !inputFocus && !lightbox.isShow) {
      // 文章列表翻页
      if ($('.prev .page-link').length) {
        location.href = $('.prev .page-link').attr('href');
      }
      // 文章内容翻页
      if ($('.post-pagination .previous-page').length) {
        location.href = $('.post-pagination .previous-page').attr('href');
      }
    }
  });

  // 评论内容输入框点击
  $('#textarea').on('click', () => {
    return false;
  });

  // 下面是一些用于样式和功能初始化的函数
  // 图片懒加载
  function lazyLoadImages() {
    // 如果页面加载完成时有图片在可视区就直接加载图片
    $('.load-img').each(function() {
      if ($(this).offset().top < window.innerHeight) {
        $(this).attr('src', $(this).attr('data-src'));
      }
    });

    // 文章图片加载完成后删除默认样式
    $('.load-img').on('load', ev => {
      $(ev.target).removeClass('load-img');
    });
  }

  // 表单焦点事件初始化
  function inputFocusInit() {
    // 输入框获取焦点
    $('input[type="search"], input[type="text"], input[type="email"], input[type="url"], textarea').on('focus', () => {
      inputFocus = true;
    });
    // 输入框失去焦点
    $('input[type="search"], input[type="text"], input[type="email"], input[type="url"], textarea').on('blur', () => {
      inputFocus = false;
    });
  }
});