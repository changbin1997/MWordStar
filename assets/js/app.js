/*!
* HomePage: https://www.misterma.com
* GithubPage: https://github.com/changbin1997
* ProjectPage: https://github.com/changbin1997/MWordStar
* author: Mr. Ma
* Licensed under MIT
*/

$(function () {
  let maxImg = false;  // 大图的状态
  let imgDirection = 0;  // 图片方向
  let contentImgSize = null;  // 文章区域的图片尺寸
  const avatarColor = [];  // 存储文字头像颜色
  const avatarName = [];  // 存储文字头像名称
  let emojiList = null;  // Emoji 列表
  let showEmoji = false;  // Emoji 面板状态
  const codeLineNum = $('.post-content').attr('data-code-line-num');
  let directory = false;  // 移动设备章节目录状态
  let directoryTop = 0;

  // 设置切换主题配色按钮的图标
  changeColorBtnIcon();

  // 切换主题配色按钮点击
  $('#change-color-btn').on('click', function() {
    let color = '';  // 颜色
    if ($('.dark-color').length) {
      // 切换为浅色
      $('body').removeClass('dark-color');
      $('body').addClass($(this).attr('data-light'));
      $('body').attr('data-color', $(this).attr('data-light'));
      color = $(this).attr('data-light');
      // 更改代码块配色
      if ($('body').attr('data-code-theme') === 'auto') {
        $('body').removeClass('github-dark').addClass('stackoverflow-light');
      }
    }else {
      // 切换为深色
      $('body').removeClass($('body').attr('data-color'));
      $('body').addClass('dark-color');
      $('body').attr('data-color', 'dark-color');
      color = 'dark-color';
      // 更改代码块配色
      if ($('body').attr('data-code-theme') === 'auto') {
        $('body').removeClass('stackoverflow-light').addClass('github-dark');
      }
    }
    // 更改颜色切换按钮的图标和文字提示
    changeColorBtnIcon();
    $('#change-color-btn').tooltip('show');

    // 获取当前时间戳
    let time = Date.parse(new Date());
    // 在当前的时间戳上 + 180天
    time += 15552000000;
    time = new Date(time);
    // 写入 cookie
    document.cookie = `themeColor=${color};path=/;expires=Tue,${time}`;
  });

  // 切换主题配色按钮键盘按下回车
  $('#change-color-btn').on('keypress', ev => {
    if (ev.keyCode === 13) $('#change-color-btn').click();
  });

  // 给文章表格添加样式
  if ($('.post-content table').length) {
    for (let i = 0; i < $('.post-content table').length; i++) {
      // 生成 Bootstrap 的响应式表格
      const table = `
      <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover">
          ${$('.post-content table').eq(i).html()}   
        </table>
      </div>
      `;
      // 替换文章中的表格
      $('.post-content table').eq(i).replaceWith(table);
    }
  }

  // 给文章中的代码块添加拷贝按钮和拷贝事件
  if ($('.enable-highlight').length && $('pre').length) {
    for (let i = 0;i < $('pre').length;i ++) {
      // 是否是代码块
      if ($('pre').eq(i).children('code').length) {
        // 生成代码行号
        if (codeLineNum === 'show') {
          $('pre code').eq(i).css('padding-left', '54px');
          const lineNumbers = Math.floor($('pre code').eq(i).height() / 22.5);
          let lineNumbersEl = '';
          for (let j = 0;j < lineNumbers;j ++) {
            lineNumbersEl += `<div class="text-right">${Number(j + 1)}</div>`;
          }
          $('pre').eq(i).prepend(`<div class="line-box">${lineNumbersEl}</div>`);
        }

        // 创建和添加拷贝按钮
        const btnEl = document.createElement('button');
        btnEl.className = 'copy-code-btn btn btn-sm';
        // 根据代码块的配色设置拷贝按钮的颜色
        btnEl.setAttribute('type', 'button');
        btnEl.innerHTML = '<i class="icon-copy"></i>';
        btnEl.setAttribute('data-clipboard-target', `#code-${i}`);
        btnEl.setAttribute('title', '拷贝代码');
        btnEl.setAttribute('aria-label', '拷贝代码');
        btnEl.setAttribute('data-toggle', 'tooltip');
        btnEl.setAttribute('data-placement', 'left');
        btnEl.setAttribute('id', `copy-btn-${i}`);
        $('pre').eq(i).prepend(btnEl);
        // 给代码块添加一个 id 方便拷贝
        $('pre code').eq(i).attr('id', `code-${i}`);
      }
      // 初始化拷贝模块
      const clipboard = new ClipboardJS('.copy-code-btn');
      // 拷贝成功
      clipboard.on('success', function(ev) {
        // 把工具提示更改为拷贝成功
        $(ev.trigger).attr('title', '拷贝成功');
        $(ev.trigger).attr('data-original-title', '拷贝成功');
        $(ev.trigger).tooltip('update');
        $(ev.trigger).tooltip('show');
        // 延迟 1 秒后把工具提示更改为拷贝代码
        setTimeout(() => {
          $(ev.trigger).attr('title', '拷贝代码');
          $(ev.trigger).attr('data-original-title', '拷贝代码');
        }, 1000);
      });
      // 拷贝出错
      clipboard.on('error', ev => {
        $(ev.trigger).attr('title', '拷贝失败');
        $(ev.trigger).attr('data-original-title', '拷贝失败');
        $(ev.trigger).tooltip('hide');
        $(ev.trigger).tooltip('show');
        setTimeout(function() {
          $(ev.trigger).attr('title', '拷贝代码');
          $(ev.trigger).attr('data-original-title', '拷贝代码');
        }, 1000);
      });
    }
  }

  // 文章的图片点击
  $('.post-content img').on('click', function () {
    // 如果图片还没有加载出就直接返回
    if ($(this).attr('src') === undefined) return false;
    // 获取图片的真实尺寸
    const imgSize = {
      w: $(this).get(0).naturalWidth,
      h: $(this).get(0).naturalHeight
    };
    // 获取文章内的图片尺寸
    contentImgSize = {
      w: $(this).width(),
      h: $(this).height(),
      l: $(this).offset().left,
      t: $(this).offset().top
    };

    // 如果图片的真实尺寸超出屏幕尺寸就重新设置大图的尺寸
    if (imgSize.w > window.innerWidth) {
      imgSize.p = imgSize.h / imgSize.w * 100;
      imgSize.w = window.innerWidth;
      imgSize.h = imgSize.w * imgSize.p / 100;
    }
    if (imgSize.h > window.innerHeight) {
      imgSize.p = imgSize.w / imgSize.h * 100;
      imgSize.h = window.innerHeight;
      imgSize.w = imgSize.h * imgSize.p / 100;
    }

    // 图片灯箱的HTML
    const maxImgTemplate = `
    <div id="max-img" role="dialog">
      <div class="mask-layer"></div>
      <img src="" alt="" class="shadow-lg">
      <p id="img-info" class="text-light text-center"></p>
      <div class="btn-group" role="group" aria-label="图片工具栏" id="img-control">
        <button type="button" class="btn big" title="放大" aria-label="放大">
          <i class="icon-zoom-in"></i>
        </button>
        <button type="button" class="btn small" title="缩小" aria-label="缩小">
          <i class="icon-zoom-out"></i>
        </button>
        <button type="button" class="btn spin-left" title="左旋转90度" aria-label="左旋转90度">
          <i class="icon-undo"></i>
        </button>
        <button type="button" class="btn spin-right" title="右旋转90度" aria-label="右旋转90度">
          <i class="icon-redo"></i>
        </button>
        <button type="button" class="btn hide-img" title="关闭大图（ESC）" aria-label="关闭大图（ESC）">
          <i class="icon-cancel-circle"></i>
        </button>
      </div>
    </div>
    `;

    // 把图片灯箱插入到页面
    $('body').append(maxImgTemplate);

    // 显示遮罩层
    $('#max-img .mask-layer').fadeIn(250);
    // 设置大图的初始尺寸和位置
    $('#max-img img').css({
      display: 'inline',
      width: contentImgSize.w,
      height: contentImgSize.h,
      top: contentImgSize.t,
      left: contentImgSize.l
    });
    // 把大图移动到屏幕中心
    $('#max-img img').animate({
      width: imgSize.w,
      height: imgSize.h,
      left: window.innerWidth / 2 - imgSize.w / 2,
      top: $(document).scrollTop() + window.innerHeight / 2 - imgSize.h / 2
    }, 250, 'linear', function() {
      // 显示图片操作按钮
      $('#img-control').css('display', 'flex');
      // 让关闭图片的按钮获取焦点
      $('.hide-img').focus();
      // 显示和设置图片描述
      $('#img-info').show();
      $('#img-info').html($(this).attr('alt'));
      // 把图片灯箱的状态设置为开启
      maxImg = true;
    });

    // 设置大图的 src 和 alt
    $('#max-img img').attr({
      "src": $(this).attr('src'),
      "alt": $(this).attr('alt')
    });

    // 把图片角度设置为默认
    if (imgDirection !== 0) {
      imgDirection = 0;
      $('#max-img img').css('transform', `rotate(${imgDirection}deg)`);
    }

    // 禁止滚动
    $('html').addClass('stop-scrolling');

    // 给图片灯箱添加事件
    // 在图片灯箱开启的情况下滑动屏幕禁止页面滚动
    $('#max-img .mask-layer, #img-info, #img-control').on('touchmove', function(ev) {
      if (maxImg) {
        ev.preventDefault();
        return false;
      }
    });

    // 大图手指拖拽
    $('#max-img img').on('touchstart', ev => {
      const X = ev.touches[0].pageX - $(ev.target).get(0).offsetLeft;
      const Y = ev.touches[0].pageY - $(ev.target).get(0).offsetTop;

      $(document).on('touchmove', ev => {
        $('#max-img img').css({
          left: ev.touches[0].pageX - X,
          top: ev.touches[0].pageY - Y
        });
      });

      $(document).on('touchend', () => {
        $(document).off('touchmove');
      });
      return false;
    });

    // 大图拖拽
    $('#max-img img').on('mousedown', ev => {
      const X = ev.clientX - $(ev.target).get(0).offsetLeft;
      const Y = ev.clientY - $(ev.target).get(0).offsetTop;

      $(document).on('mousemove', ev => {
        $('#max-img img').css({
          left: ev.clientX - X,
          top: ev.clientY - Y
        });
      });

      $(document).on('mouseup', () => {
        $(document).off('mousemove');
      });
      return false;
    });

    // 图片左旋转
    $('#img-control .spin-left').on('click', () => {
      imgDirection -= 90;
      $('#max-img img').css('transition', '0.3s');
      $('#max-img img').css('transform', `rotate(${imgDirection}deg)`);
      setTimeout(() => {
        $('#max-img img').css('transition', '0s');
      }, 300);
    });

    // 图片右旋转
    $('#img-control .spin-right').on('click', () => {
      imgDirection += 90;
      $('#max-img img').css('transition', '0.3s');
      $('#max-img img').css('transform', `rotate(${imgDirection}deg)`);
      setTimeout(function () {
        $('#max-img img').css('transition', '0s');
      }, 300);
    });

    // 图片放大
    $('#img-control .big').on('click', () => {
      $('#max-img img').animate({
        width: $('#max-img img').width() + $('#max-img img').width() / 5,
        height: $('#max-img img').height() + $('#max-img img').height() / 5
      }, 250);
    });

    // 图片缩小
    $('#img-control .small').on('click', () => {
      // 如果图片的宽度或高度 < 40px 将不再缩小
      if ($('#max-img img').width() <= 80 || $('#max-img img').height() <= 80) return false;
      $('#max-img img').animate({
        width: $('#max-img img').width() - $('#max-img img').width() / 5,
        height: $('#max-img img').height() - $('#max-img img').height() / 5
      }, 250);
    });

    // 大图鼠标滚动
    $('#max-img img').on('mousewheel DOMMouseScroll', ev => {
      if (!maxImg) return false;
      if (ev.originalEvent.wheelDelta === undefined) return false;
      if (ev.originalEvent.wheelDelta >  0) {
        // 放大图片
        $('#img-control .big').click();
      }else {
        // 缩小图片
        $('#img-control .small').click();
      }
    });

    // 大图的关闭按钮点击
    $('#max-img .hide-img').on('click', () => {
      maxImg = false;
      // 隐藏半透明背景
      $('#max-img .mask-layer').fadeOut(250);
      // 隐藏图片描述
      $('#img-info').hide();
      // 隐藏图片功能区按钮
      $('#img-control').hide();
      $('html').removeClass('stop-scrolling');
      // 让图片回到文章图片的位置
      $('#max-img img').animate({
        width: contentImgSize.w,
        height: contentImgSize.h,
        top: contentImgSize.t,
        left: contentImgSize.l
      }, 250, 'linear', () => {
        $('#max-img img').hide();
        $('#max-img img').attr({
          src: '',
          alt: ''
        });
        $('#max-img').remove();
      });
    });

    // 关闭大图按钮按下 tab
    $('#max-img .hide-img').on('keydown', ev => {
      ev.preventDefault();
      if (ev.keyCode === 9) {
        $('#img-control .big').focus();  // 让放大图片按钮获取焦点
      }
      if (ev.keyCode === 13) {
        $('#max-img .hide-img').click();
      }
    });
  });

  // 给链接设置在新标签页打开
  if ($('.post-content').attr('data-target') === '_blank' && $('.post-content a').length) {
    // 给文章中的所有链接添加 target 属性，让文章中的链接在新标签页打开
    $('.post-content a').attr('target', '_blank');
  }

  // 章节目录跳转
  $('.directory-link').on('click', ev => {
    const titleSelect = `[data-title="${$(ev.target).attr('data-directory')}"]`;
    $('html').animate({
      scrollTop: $(titleSelect).offset().top - 60
    }, 400);
    return false;
  });

  // 给分页链接设置样式
  if ($('.pagination-nav ul li').length) {
    // 给分页链接添加符合 Bootstrap 样式标准的 class
    $('.pagination-nav ul li').addClass('page-item');
    $('.pagination-nav ul li a').addClass('page-link');
    // 给上一页和下一页加入文字提示
    $('.pagination-nav .prev a').attr({
      'aria-label': '上一页',
      'title': '上一页（左光标键）',
      'data-toggle': 'tooltip',
      'data-placement': 'top'
    });
    $('.pagination-nav .next a').attr({
      'aria-label': '下一页',
      'title': '下一页（右光标键）',
      'data-toggle': 'tooltip',
      'data-placement': 'top'
    });
    // 给当前页的链接加入一个标识
    $('.pagination-nav .active a').attr('aria-current', 'page');
  }

  // 给文章信息的分类链接添加 title
  if ($('.article-info .icon-folder-open').length) {
    $('.article-info .icon-folder-open').nextAll().attr({
      'title': '分类',
      'data-toggle': 'tooltip',
      'data-placement': 'top'
    });
    $('.icon-folder-open').nextAll().addClass($('.icon-folder-open').attr('data-color'));
  }

  // 给文章信息的标签链接添加 title
  if ($('.tags a').length) {
    $('.tags a').attr({
      'title': '标签',
      'data-toggle': 'tooltip',
      'data-placement': 'top'
    });
    $('.tags a').addClass($('.tags').attr('data-color'));
  }

  // 给评论区的链接添加 target
  if ($('.comment-info b a').length) {
    // 让评论区的链接在新标签页打开
    $('.comment-info b a').attr('target', '_blank');
  }


  // 给评论区的分页链接设置样式
  if ($('.comments-lists .page-navigator').length) {
    $('.comments-lists .page-navigator li').addClass('pagination');
    $('.comments-lists .page-navigator li a').addClass('page-link');
    const pageNav = `
    <nav aria-label="分页导航区">
      <ol class="pagination justify-content-center page-navigator">
        ${$('.comments-lists .page-navigator').html()}
      </ol>
    </nav>
    `;
    $('.comments-lists .page-navigator').replaceWith(pageNav);
  }

  // 回复按钮按下 tab
  $(document).on('keydown', '.comments-lists .respond .submit', ev => {
    ev.preventDefault();
    if (ev.keyCode === 9) {
      // 让取消回复的链接获取焦点
      $('#cancel-comment-reply-link').focus();
    }
  });

  // 评论区的回复链接鼠标移入和移出
  $('#comments .comment-reply a').hover(ev => {
    // 根据主题配色设置评论高亮颜色
    let color = '#E4E4E4';
    if ($('.dark-color').length) color = '#383838';
    // 获取回复评论的 cid
    const cid = $(ev.target).parent().attr('data-id');
    // 改变回复评论的内容样式
    $(`#c-${cid}`).css('background', color);
  }, ev => {
    const cid = $(ev.target).parent().attr('data-id');
    $(`#c-${cid}`).css('background', 'none');
  });

  // 给侧边栏近期文章的第一篇文章设置头图
  if ($('.latest-articles a').eq(0).children('.article-img').length) {
    $('.latest-articles a').eq(0).addClass('latest-articles-active');
  }

  // 最新文章列表鼠标移入
  $('.latest-articles li').on('mouseover', function (ev) {
    ev.preventDefault();
    ev.stopPropagation();
    if ($(this).children('a').children('.article-img').length > 0) {
      $('.latest-articles a').removeClass('latest-articles-active');
      $(this).children('a').addClass('latest-articles-active');
    }
  });

  // 最新文章区域鼠标移出
  $('.latest-articles').on('mouseout', function (ev) {
    const x = ev.clientX;
    const y = ev.clientY + $(document).scrollTop();
    if (
        x < $(this).offset().left ||
        x >= $(this).offset().left + $(this).width() ||
        y < $(this).offset().top ||
        y >= $(this).offset().top + $(this).height()
    ) {
      $('.latest-articles a').removeClass('latest-articles-active');
      if ($('.latest-articles a').eq(0).children('.article-img').length > 0) {
        $('.latest-articles a').eq(0).addClass('latest-articles-active');
      }
    }
  });

  // 文章是否有密码
  if ($('.post-content .protected').length) {
    $('.protected .word').attr('role', 'alert');  // 让读屏软件朗读输入密码的提示
    $('.protected .text').attr('placeholder', '请在此处输入文章密码');
    $('.protected .text').get(0).select();  // 让密码输入表单获取交点
  }

  // 返回顶部按钮点击
  $('.to-top').on('click', () => {
    $('html').animate({
      scrollTop: 0
    }, 400);
    $('header .navbar-brand').get(0).focus();
    return false;
  });

  // 调整侧边栏的章节目录尺寸
  function directorySize() {
    if ($('.sidebar .directory').length) {
      // 获取侧边栏章节目录的位置
      directoryTop = $('.sidebar .directory').offset().top;
      $('.sidebar .directory').css('width', $('.sidebar .reference-line').innerWidth());
      $('.sidebar .directory > .article-directory').css({
        'max-height': window.innerHeight - 180
      });
    }
  }

  // 页面加载完成后调整侧边栏的目录尺寸
  directorySize();

  // 窗口尺寸改变
  window.addEventListener('resize', () => {
    directorySize();
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

    // 固定侧边栏章节目录位置
    if ($('.sidebar .directory').length && window.innerWidth >= 992) {
      if ($(document).scrollTop() >= directoryTop) {
        $('.sidebar .directory').css({
          position: 'fixed',
          top: 80
        });
      }else {
        $('.sidebar .directory').css('position', 'static');
      }
    }
  });

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

  // 大图的关闭按钮按下回车
  $('.hide-img').on('keypress', ev => {
    if (ev.keyCode == 13) {
      $('#max-img .hide-img').click();
    }
  });

  // 全局快捷键
  $(document).on('keyup', ev => {
    // 如果按下的是 ESC 就关闭大图
    if (ev.keyCode === 27 && maxImg) {
      $('#max-img .hide-img').click();  // 关闭大图
    }

    // 如果按下的是 + 就放大图片
    if (ev.keyCode === 107 && maxImg) {
      $('#img-control .big').click();
    }

    // 如果按下的是 - 就缩小图片
    if (ev.keyCode === 109 && maxImg) {
      $('#img-control .small').click();
    }

    // 右光标键
    if (ev.keyCode === 39) {
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
    if (ev.keyCode === 37) {
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

  // 是否是文章页
  if ($('#qr').length) {
    // 生成文章二维码
    $('#qr').qrcode({
      width: 150,
      height: 150,
      text: $('#share-btn').attr('data-url')
    });

    // 给二维码图片添加 alt 属性
    $('#qr canvas').attr({
      role: 'img',
      'aria-label': '文章二维码'
    });
  }

  // 点赞
  $('#agree-btn').on('click', () => {
    $('#agree-btn').prop('disabled', true);
    $.ajax({
      type: 'post',
      url: $('#agree-btn').attr('data-url'),
      data: `agree=${$('#agree-btn').attr('data-cid')}`,
      async: true,
      timeout: 30000,
      cache: false,
      success: data => {
        const re = /\d/;
        if (re.test(data)) {
          $('#agree-btn .agree-num').html(data);
          $('.post-page').append('<span id="agree-p" role="alert">赞 + 1</span>');
          $('#agree-p').css({
            top: $('#agree-btn').offset().top - 25,
            left: $('#agree-btn').offset().left + $('#agree-btn').outerWidth() / 2 - $('#agree-p').outerWidth() / 2
          });

          $('#agree-p').animate({
            top: $('#agree-btn').offset().top - 70,
            opacity: 0
          }, 400, () => {
            $('#agree-p').remove();
          });
        }
      },
      error: () => {
        $('#agree-btn').prop('disabled', false);
      }
    });
  });

  // 初始化工具提示
  $('[data-toggle="tooltip"]').tooltip();

  // 把父评论的姓名加入到子评论中
  if ($('#comments .parent').length) {
    const linkColor = $('.post-content').attr('data-color');
    for (let i = 0; i < $('#comments .parent').length; i++) {
      const parentLink = `
      <a class="mr-1 ${linkColor}" href="${$('#comments .parent').eq(i).attr('href')}">
        ${$('#comments .parent').eq(i).html()}
      </a>
      `;
      $('#comments .parent').eq(i).next().prepend(parentLink);
    }
    $('#comments .parent').remove();
  }

  // 头像图片加载完成后删除图片背景
  $('.avatar').on('load', ev => {
    $(ev.target).css('background', 'none');
  });

  // 独立页友链 Logo 加载完成
  $('.link-box .link img').on('load', ev => {
    // 去除背景颜色
    $(ev.target).css('background', 'none');
  });

  // 独立页 Logo 加载出错
  $('.link-box .link img').on('error', ev => {
    // 默认站点 Logo
    const logoEl = '<i class="link-logo float-left icon-link icon-logo rounded-circle" aria-label="站点Logo" role="img"></i>';
    // 把默认 Logo 插入到页面
    $(ev.target).before(logoEl);
    // 移除加载失败的 Logo
    $(ev.target).remove();
  });

  // 给评论者头像添加错误事件
  for (let i = 0;i < $('.avatar').length;i ++) {
    // 检测是否是图片
    if ($('.avatar').eq(i)[0].tagName === 'IMG') {
      $('.avatar').eq(i).on('error', ev => {
        // 获取头像昵称
        const name = $(ev.target).attr('alt');
        // 创建文字头像元素
        const avatarEl = document.createElement('div');
        avatarEl.setAttribute('role', 'img');
        avatarEl.setAttribute('aria-label', name);
        // 设置文字头像的 class
        avatarEl.className = 'pingback avatar';
        // 把文字头像的内容设置为评论者昵称的第一个字
        avatarEl.innerHTML = name.substring(0, 1);

        // 检测是否重复出现
        const nameIndex = avatarName.indexOf(name);
        if (nameIndex === -1) {
          avatarName.push(name);
          // 生成随机颜色
          const bgColor = {r: rand(250, 1), g: rand(250, 1), b: rand(250, 1)};
          // 把颜色添加到数组，遇到同名的头像可以使用同一组颜色
          avatarColor.push(bgColor);
          // 设置文字头像的背景颜色
          avatarEl.style.background = `rgb(${bgColor.r}, ${bgColor.g}, ${bgColor.b})`;
        }else {
          // 设置文字头像的背景颜色
          avatarEl.style.background = `rgb(${avatarColor[nameIndex].r}, ${avatarColor[nameIndex].g}, ${avatarColor[nameIndex].b})`;
        }

        // 把文字头像插入到页面
        $(ev.target).before(avatarEl);
        // 移除加载失败的头像
        $(ev.target).remove();
      });
    }
  }

  // 加载 Emoji
  if ($('#emoji-panel').length) {
    $.ajax({
      type: 'post',
      url: $('#show-emoji-btn').attr('data-url'),
      data: 'emoji=emoji',
      timeout: 10000,
      global: false,
      success: data => {
        data = JSON.parse(data);
        // 检查是否加载正确
        if (data.smileys === undefined) {
          $('#emoji-panel').append('<div>未知错误！</div>');
          return false;
        }
        emojiList = data;
        // 调用面目表情按钮事件
        $('#emoji-classification button').eq(0).click();
      },
      error: (xhr, err, abnormal) => {
        $('#emoji-panel').append(`<div>服务器请求出错！错误代码 ${err}</div>`);
      }
    });
  }

  // Emoji 开关点击
  $('#show-emoji-btn').on('click', ev => {
    // 设置 Emoji 面板的显示和隐藏
    $('#emoji-panel').slideToggle(250);
    // 设置 Emoji 的显示和隐藏状态
    showEmoji = !showEmoji;
    // 设置用于屏幕阅读器的 Emoji 面板的显示和隐藏状态
    $(ev.target).attr('aria-expanded', showEmoji);
    return false;
  });

  // 页面空白区域点击
  $('body').on('click', () => {
    // 如果表情面板处于开启状态就关闭表情面板
    if (showEmoji) $('#show-emoji-btn').click();
  });

  // Emoji 表情面板的空白区域点击
  $('#emoji-panel').on('click', () => {
    return false;
  });

  // 评论内容输入框点击
  $('#textarea').on('click', () => {
    return false;
  });

  // 切换Emoji类型按钮点击
  $('#emoji-classification button').on('click', ev => {
    const emoji = emojiList[$(ev.target).attr('data-classification')];
    let emojiEl = '';
    // 获取主题配色
    const btnColor = $('#emoji-classification').attr('data-color');

    // 清除之前选中的按钮的选中状态
    $('#emoji-classification .selected').attr('aria-checked', false);
    $('#emoji-classification .selected').removeClass([btnColor, 'selected']);
    // 设置点击按钮的选中状态
    $(ev.target).attr('aria-checked', true);
    $(ev.target).addClass(['selected', btnColor]);

    // 生成 Emoji 元素
    emoji.forEach(e => {
      emojiEl += `<div class="emoji p-2" tabindex="0" role="listitem">${e}</div>`;
    });

    // 清除之前的 Emoji
    if ($('#emoji-list .emoji').length) {
      $('#emoji-list .emoji').remove();
    }

    // 把 Emoji 插入到页面
    $('#emoji-list').append(emojiEl);
    // 设置类型标题
    $('#emoji-title').html($(ev.target).attr('title'));
    // 设置用于屏幕阅读器的表情列表标题
    $('#emoji-list').attr('aria-label', `${$(ev.target).attr('title')}（按回车可以把表情添加到评论内容输入框）`);
  });

  // Emoji 表情点击
  $('#emoji-list').on('click', '.emoji', ev => {
    // 把表情添加到评论内容输入框
    $('#textarea').val($('#textarea').val() + $(ev.target).html());
  });

  // Emoji 表情按下回车键
  $('#emoji-list').on('keypress', '.emoji', ev => {
    if (ev.keyCode === 13) {
      // 把表情添加到评论内容输入框
      $('#textarea').val($('#textarea').val() + $(ev.target).html());
    }
  });

  // Emoji 表情面板按下 ESC
  $('#emoji-panel').on('keydown', ev =>{
    if (ev.keyCode === 27) {
      // 调用 Emoji 开关事件
      $('#show-emoji-btn').click();
      $('#textarea').focus();
    }
  });

  // 移动设备目录按钮点击
  $('#directory-btn').on('click', () => {
    if (!directory) {
      $('#directory-mobile').css('display', 'flex');
      $('#directory-mobile').animate({opacity: 1}, 250);
      directory = true;
    }else {
      $('#directory-mobile').animate({opacity: 0}, 250, () => {
        $('#directory-mobile').hide();
      });
      directory = false;
    }
    $('#directory-btn').attr('aria-expanded', directory);
  });

  // 移动设备的关闭目录按钮点击
  $('#directory-mobile .close-btn').on('click', () => {
    $('#directory-btn').click();
  });
});

// 如果开启了代码高亮就初始化代码高亮
if (document.querySelectorAll('.enable-highlight').length) {
  hljs.initHighlightingOnLoad();
}

// 根据当前使用的主题配色来设置主题配色切换按钮的图标
function changeColorBtnIcon() {
  if ($('.dark-color').length) {
    $('#change-color-btn i').removeClass('icon-sun');
    $('#change-color-btn i').addClass('icon-moon');
    $('#change-color-btn').attr('title', '当前为深色模式，点击可切换为浅色模式');
  }else {
    $('#change-color-btn i').removeClass('icon-moon');
    $('#change-color-btn i').addClass('icon-sun');
    $('#change-color-btn').attr('title', '当前为浅色模式，点击可切换为深色模式');
  }
  $('#change-color-btn').attr('data-original-title', $('#change-color-btn').attr('title'));
  $('#change-color-btn').tooltip('update');
}

// 生成随机数的函数
function rand(max, min) {
  const num = max - min;
  return Math.round(Math.random() * num + min);
}