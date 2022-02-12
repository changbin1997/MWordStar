$(function () {
  var maxImg = false;  //  大图的状态
  var imgDirection = 0;  //  图片方向
  var imgWH = '';  //  记录图片的宽高
  var avatarColor = [];  //  存储文字头像颜色
  var avatarName = [];  //  存储文字头像名称
  var emojiList = null;  //  Emoji 列表
  var showEmoji = false;  //  Emoji 面板状态

  //  给文章表格添加样式
  if ($('.post-content table').length) {
    for (var i = 0; i < $('.post-content table').length; i++) {
      //  生成 Bootstrap 的响应式表格
      var table = '<div class="table-responsive"><table class="table table-striped table-bordered table-hover">' + $('.post-content table').eq(i).html() + '</table></div>';
      $('.post-content table').eq(i).replaceWith(table);  //  替换文章中的表格
    }
  }

  //  文章的图片点击
  $('article .post-content img').on('click', function () {
    //  获取图片的真实尺寸
    var imgSize = {
      w: $(this).get(0).naturalWidth,
      h: $(this).get(0).naturalHeight
    };
    //  根据图片真是尺寸设置大图的尺寸
    if (imgSize.w > imgSize.h) {
      if (imgSize.w >= $(window).width()) {
        $('#max-img img').css('width', '90%');
      } else {
        $('#max-img img').css('width', imgSize.w);
      }
      $('#max-img img').css('height', 'auto');
      imgWH = 'width';
    } else {
      if (imgSize.h >= $(window).height()) {
        $('#max-img img').css('height', '90%');
      } else {
        $('#max-img img').css('height', imgSize.h);
      }
      $('#max-img img').css('width', 'auto');
      imgWH = 'height';
    }

    $('#max-img').fadeIn(250);  //  显示大图
    //  设置大图的 src 和 alt
    $('#max-img img').attr({
      "src": $(this).attr('src'),
      "alt": $(this).attr('alt')
    });

    //  调整图片方向
    if (imgDirection !== 0) {
      imgDirection = 0;
      $('#max-img img').css('transform', 'rotate(' + imgDirection + 'deg)');
    }

    //  让图片居中
    $('#max-img img').css({
      left: $(window).width() / 2 - $('#max-img img').width() / 2,
      top: $(window).height() / 2 - $('#max-img img').height() / 2
    });

    $('.hide-img').focus();  //  让关闭图片的按钮获取焦点
    maxImg = true;  //  大图状态设置为 true
    $('html').addClass('stop-scrolling');  //  禁止滚动
    return false;
  });

  //  大图手指拖拽
  $('#max-img img').on('touchstart', function (ev) {
    var X = ev.touches[0].pageX - $(ev.target).get(0).offsetLeft;
    var Y = ev.touches[0].pageY - $(ev.target).get(0).offsetTop;

    $(document).on('touchmove', function (ev) {
      $('#max-img img').css({
        left: ev.touches[0].pageX - X,
        top: ev.touches[0].pageY - Y
      });
    });

    $(document).on('touchend', function () {
      $(document).off('touchmove');
    });
    return false;
  });

  //  大图拖拽
  $('#max-img img').on('mousedown', function (ev) {
    var X = ev.clientX - $(ev.target).get(0).offsetLeft;
    var Y = ev.clientY - $(ev.target).get(0).offsetTop;

    $(document).on('mousemove', function (ev) {
      $('#max-img img').css({
        left: ev.clientX - X,
        top: ev.clientY - Y
      });
    });

    $(document).on('mouseup', function (ev) {
      $(document).off('mousemove');
    });
    return false;
  });

  //  图片左旋转
  $('#img-control .spin-left').on('click', function () {
    imgDirection -= 90;
    $('#max-img img').css('transition', '0.3s');
    $('#max-img img').css('transform', 'rotate(' + imgDirection + 'deg)');
    setTimeout(function () {
      $('#max-img img').css('transition', '0s');
    }, 300);
  });

  //  图片右旋转
  $('#img-control .spin-right').on('click', function () {
    imgDirection += 90;
    $('#max-img img').css('transition', '0.3s');
    $('#max-img img').css('transform', 'rotate(' + imgDirection + 'deg)');
    setTimeout(function () {
      $('#max-img img').css('transition', '0s');
    }, 300);
  });

  //  图片放大
  $('#img-control .big').on('click', function () {
    var size = imgWH === 'width' ? $('#max-img img').width() + 40 : $('#max-img img').height() + 40;
    $('#max-img img').css('transition', '0.2s');
    $('#max-img img').css(imgWH, size + 'px');
    setTimeout(function () {
      $('#max-img img').css('transition', '0s');
    }, 300);
  });

  //  图片缩小
  $('#img-control .small').on('click', function () {
    var size = imgWH === 'width' ? $('#max-img img').width() - 40 : $('#max-img img').height() - 40;
    //  如果图片的宽度或高度 < 40px 将不再缩小
    if ($('#max-img img').width() <= 40 || $('#max-img img').height() <= 40) {
      return false;
    }
    $('#max-img img').css('transition', '0.2s');
    $('#max-img img').css(imgWH, size + 'px');
    setTimeout(function () {
      $('#max-img img').css('transition', '0s');
    }, 300);
  });

  //  大图鼠标滚动
  $('#max-img img').on('mousewheel DOMMouseScroll', function (ev) {
    if (ev.originalEvent.wheelDelta === undefined) return false;
    if (ev.originalEvent.wheelDelta >  0) {
      //  放大图片
      $('#img-control .big').click();
    }else {
      //  缩小图片
      $('#img-control .small').click();
    }
  });

  //  大图的关闭按钮点击
  $('#max-img .hide-img').on('click', function () {
    $('#max-img').fadeOut(250, function () {
      $('#max-img img').attr('src', '');
    });
    maxImg = false;
    $('html').removeClass('stop-scrolling');
  });

  //  关闭大图按钮按下 tab
  $('#max-img .hide-img').on('keydown', function (ev) {
    ev.preventDefault();
    if (ev.keyCode === 9) {
      $('#img-control .big').focus();  //  让放大图片按钮获取焦点
    }
    if (ev.keyCode === 13) {
      $('#max-img .hide-img').click();
    }
  });

  //  给链接设置在新标签页打开
  if ($('.post-content').attr('data-target') === '_blank' && $('.post-content a').length) {
    //  给文章中的所有链接添加 target 属性，让文章中的链接在新标签页打开
    for (var i = 0; i < $('.post-content a').length; i++) {
      if ($('.post-content a').eq(i).attr('data-directory') === undefined) {
        $('.post-content a').eq(i).attr('target', '_blank');
      }
    }
  }

  //  章节目录跳转
  $('.directory-link').on('click', function () {
    var titleSelect = '[data-title="' + $(this).attr('data-directory') + '"]';
    $('html').animate({
      scrollTop: $(titleSelect).offset().top - 60
    }, 400);
    return false;
  });

  //  给文章中的链接添加颜色类
  if ($('.post-content a').length) {
    $('.post-content a').addClass($('.post-content').attr('data-color'));
  }

  //  给上一篇和下一篇文章的导航链接添加颜色类
  if ($('.post-navigation a').length) {
    $('.post-navigation a').addClass($('.post-content').attr('data-color'));
  }

  //  给评论区的评论者昵称添加颜色类
  if ($('.comment-info .author a').length) {
    $('.comment-info .author a').addClass($('.post-content').attr('data-color'));
  }

  //  给评论区的取消回复链接添加颜色类
  if ($('#cancel-comment-reply-link').length) {
    $('#cancel-comment-reply-link').addClass($('.post-content').attr('data-color'));
  }

  //  给翻页链接和评论区的回复按钮添加元素风格类
  if ($('body').attr('data-rounded') === 'rounded-0') {
    if ($('.pagination-nav .pagination a').length) {
      $('.pagination-nav .pagination a').addClass($('body').attr('data-rounded'));
    }
    if ($('.comment-author .comment-reply a').length) {
      $('.comment-author .comment-reply a').addClass($('body').attr('data-rounded'));
    }
  }

  //  给分页链接设置样式
  if ($('.pagination-nav ul li').length) {
    //  给分页链接添加符合 Bootstrap 样式标准的 class
    $('.pagination-nav ul li').addClass('page-item');
    $('.pagination-nav ul li a').addClass('page-link');
    //  给上一页和下一页加入 aria-label 属性，兼容屏幕阅读器
    $('.pagination-nav .prev a').attr('aria-label', '上一页');
    $('.pagination-nav .next a').attr('aria-label', '下一页');
  }

  //  给文章信息的分类链接添加 title
  if ($('.icon-folder-open').length) {
    $('.icon-folder-open').nextAll().attr({
      'title': '分类',
      'data-toggle': 'tooltip',
      'data-placement': 'top'
    });
    $('.icon-folder-open').nextAll().addClass($('.icon-folder-open').attr('data-color'));
  }

  //  给文章信息的标签链接添加 title
  if ($('.tags a').length) {
    $('.tags a').attr({
      'title': '标签',
      'data-toggle': 'tooltip',
      'data-placement': 'top'
    });
    $('.tags a').addClass($('.tags').attr('data-color'));
  }

  //  给评论区的链接添加 target
  if ($('.comment-info b a').length) {
    //  让评论区的链接在新标签页打开
    $('.comment-info b a').attr('target', '_blank');
  }


  //  给评论区的分页链接设置样式
  if ($('.comments-lists .page-navigator').length) {
    $('.comments-lists .page-navigator li').addClass('pagination');
    $('.comments-lists .page-navigator li a').addClass('page-link');
    var pageNav = '<nav aria-label="分页导航区"><ol class="pagination justify-content-center page-navigator">' + $('.comments-lists .page-navigator').html() + '</ol></nav>';
    $('.comments-lists .page-navigator').replaceWith(pageNav);
  }

  //  回复按钮按下 tab
  $(document).on('keydown', '.comments-lists .respond .submit', function (ev) {
    ev.preventDefault();
    if (ev.keyCode === 9) {
      $('#cancel-comment-reply-link').focus();  //  让取消回复的链接获取焦点
    }
  });

  //  评论区的回复链接鼠标移入和移出
  $('#comments .comment-reply a').hover(function (t) {
    var cid = $(this).parent().attr('data-id');
    $('#c-' + cid).css('background-color', '#EBF2FC');
  }, function (t) {
    var cid = $(this).parent().attr('data-id');
    $('#c-' + cid).css('background-color', '#FFFFFF');
  });

  //  给侧边栏近期文章的第一篇文章设置头图
  if ($('.latest-articles a').eq(0).children('.article-img').length) {
    $('.latest-articles a').eq(0).addClass('latest-articles-active');
  }

  //  最新文章列表鼠标移入
  $('.latest-articles li').on('mouseover', function (ev) {
    ev.preventDefault();
    ev.stopPropagation();
    if ($(this).children('a').children('.article-img').length > 0) {
      $('.latest-articles a').removeClass('latest-articles-active');
      $(this).children('a').addClass('latest-articles-active');
    }
  });

  //  最新文章区域鼠标移出
  $('.latest-articles').on('mouseout', function (ev) {
    var x = ev.clientX;
    var y = ev.clientY + $(document).scrollTop();
    if (x < $(this).offset().left || x >= $(this).offset().left + $(this).width() || y < $(this).offset().top || y >= $(this).offset().top + $(this).height()) {
      $('.latest-articles a').removeClass('latest-articles-active');
      if ($('.latest-articles a').eq(0).children('.article-img').length > 0) {
        $('.latest-articles a').eq(0).addClass('latest-articles-active');
      }
    }
  });

  //  文章是否有密码
  if ($('.post-content .protected').length) {
    $('.protected .word').attr('role', 'alert');  //  让读屏软件朗读输入密码的提示
    $('.protected .text').attr('placeholder', '请在此处输入文章密码');
    $('.protected .text').get(0).select();  //  让密码输入表单获取交点
  }

  //  返回顶部按钮点击
  $('.to-top').on('click', function () {
    $('html').animate({
      scrollTop: 0
    }, 400);
    $('header .navbar-brand').get(0).focus();
    return false;
  });

  //  监听滚动条
  $(document).on('scroll', function () {
    //  返回顶部的按钮是否存在
    if ($('.to-top').length > 0) {
      //  如果滚动条高度 > 屏幕高度
      if ($(document).scrollTop() > window.innerHeight) {
        $('.to-top').removeClass('d-none');  //  显示返回顶部按钮
      } else {
        $('.to-top').addClass('d-none');  //  隐藏返回顶部按钮
      }
    }
  });

  //  大图的关闭按钮按下回车
  $('.hide-img').on('keypress', function (ev) {
    if (ev.keyCode == 13) {
      $('#max-img .hide-img').click();
    }
  });

  //  全局快捷键
  $(document).on('keyup', function (ev) {
    //  如果按下的是 ESC 就关闭大图
    if (ev.keyCode === 27 && maxImg) {
      $('#max-img .hide-img').click();  //  关闭大图
    }
    //  如果按下的是 + 就放大图片
    if (ev.keyCode === 107 && maxImg) {
      $('#img-control .big').click();
    }
    //  如果按下的是 - 就缩小图片
    if (ev.keyCode === 109 && maxImg) {
      $('#img-control .small').click();
    }
    //  如果按下的是右方向键就跳转到下一页
    if (ev.keyCode === 39 && $('.next .page-link').length) {
      location.href = $('.next .page-link').attr('href');
    }
    //  如果按下的是左方向键就跳转到上一页
    if (ev.keyCode === 37 && $('.prev .page-link').length) {
      location.href = $('.prev .page-link').attr('href');
    }
  });

  //  是否是文章页
  if ($('#qrcode').length) {
    //  生成文章二维码
    $('#qrcode').qrcode({
      width: 235,
      height: 235,
      text: $('#share-btn').attr('data-url')
    });

    //  给二维码图片添加 alt 属性
    if ($('#qrcode img').length > 0) {
      $('#qrcode img').attr('alt', '文章二维码');
    }
  }

  //  点赞
  $('#agree-btn').on('click', function () {
    $('#agree-btn').get(0).disabled = true;
    $.ajax({
      type: 'post',
      url: $('#agree-btn').attr('data-url'),
      data: 'agree=' + $('#agree-btn').attr('data-cid'),
      async: true,
      timeout: 30000,
      cache: false,
      success: function (data) {
        var re = /\d/;
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
          }, 400, function () {
            $('#agree-p').remove();
          });
        }
      },
      error: function () {
        $('#agree-btn').get(0).disabled = false;
      }
    });
  });

  //  初始化工具提示
  $('[data-toggle="tooltip"]').tooltip();

  //  把父评论的姓名加入到子评论中
  if ($('#comments .parent').length) {
    var linkColor = $('.post-content').attr('data-color');
    for (var i = 0; i < $('#comments .parent').length; i++) {
      var parentLink = '<a class="mr-1 ' + linkColor + '" href="' + $('#comments .parent').eq(i).attr('href') + '">' + $('#comments .parent').eq(i).html() + '</a>';
      $('#comments .parent').eq(i).next().prepend(parentLink);
    }
    $('#comments .parent').remove();
  }

  //  头像图片加载完成后删除图片背景
  $('.avatar').on('load', function () {
    $(this).css('background', 'none');
  });

  //  独立页友链 Logo 加载完成
  $('.link-box .link img').on('load', function() {
    //  去除背景颜色
    $(this).css('background', 'none');
  });

  //  独立页 Logo 加载出错
  $('.link-box .link img').on('error', function () {
    //  默认站点 Logo
    var logoEl = '<i class="link-logo float-left icon-link icon-logo rounded-circle" aria-label="站点Logo" role="img"></i>';
    //  把默认 Logo 插入到页面
    $(this).before(logoEl);
    //  移除加载失败的 Logo
    $(this).remove();
  });

  //  给评论者头像添加错误事件
  for (var i = 0;i < $('.avatar').length;i ++) {
    // 检测是否是图片
    if ($('.avatar').eq(i)[0].tagName === 'IMG') {
      $('.avatar').eq(i).on('error', function(ev) {
        // 获取头像昵称
        var name = $(ev.target).attr('alt');
        // 创建文字头像元素
        var avatarEl = document.createElement('div');
        avatarEl.setAttribute('role', 'img');
        avatarEl.setAttribute('aria-label', name);
        // 设置文字头像的 class
        avatarEl.className = 'pingback avatar';
        // 把文字头像的内容设置为评论者昵称的第一个字
        avatarEl.innerHTML = name.substring(0, 1);

        // 检测是否重复出现
        var nameIndex = avatarName.indexOf(name);
        if (nameIndex === -1) {
          avatarName.push(name);
          // 生成随机颜色
          var bgColor = {r: rand(250, 1), g: rand(250, 1), b: rand(250, 1)};
          // 把颜色添加到数组，遇到同名的头像可以使用同一组颜色
          avatarColor.push(bgColor);
          // 设置文字头像的背景颜色
          avatarEl.style.background = 'rgb(' + bgColor.r + ',' + bgColor.g + ',' + bgColor.b + ')';
        }else {
          // 设置文字头像的背景颜色
          avatarEl.style.background = 'rgb(' + avatarColor[nameIndex].r + ',' + avatarColor[nameIndex].g + ',' + avatarColor[nameIndex].b + ')';
        }

        // 把文字头像插入到页面
        $(ev.target).before(avatarEl);
        // 移除加载失败的头像
        $(ev.target).remove();
      });
    }
  }

  //  生成随机数的函数
  function rand(max, min) {
    var num = max - min;
    return Math.round(Math.random() * num + min);
  }

  //  加载 Emoji
  if ($('#emoji-panel').length) {
    $.ajax({
      type: 'post',
      url: $('#show-emoji-btn').attr('data-url'),
      data: 'emoji=emoji',
      timeout: 10000,
      global: false,
      success: function (data) {
        data = JSON.parse(data);
        //  检查是否加载正确
        if (data.smileys === undefined) {
          $('#emoji-panel').append('<div>未知错误！</div>');
          return false;
        }
        emojiList = data;
        //  调用面目表情按钮事件
        $('#emoji-classification button').eq(0).click();
      },
      error: function (xhr, err, abnormal) {
        $('#emoji-panel').append('<div>服务器请求出错！错误代码' + err + '</div>');
      }
    });
  }

  //  Emoji 开关点击
  $('#show-emoji-btn').on('click', function (ev) {
    //  设置 Emoji 面板的显示和隐藏
    $('#emoji-panel').slideToggle(250);
    //  设置 Emoji 的显示和隐藏状态
    showEmoji = !showEmoji;
    //  设置用于屏幕阅读器的 Emoji 面板的显示和隐藏状态
    $(ev.target).attr('aria-expanded', showEmoji);
    return false;
  });

  // 页面空白区域点击
  $('body').on('click', function () {
    // 如果表情面板处于开启状态就关闭表情面板
    if (showEmoji) $('#show-emoji-btn').click();
  });

  // Emoji 表情面板的空白区域点击
  $('#emoji-panel').on('click', function () {
    return false;
  });

  // 评论内容输入框点击
  $('#textarea').on('click', function () {
    return false;
  });

  //  切换Emoji类型按钮点击
  $('#emoji-classification button').on('click', function (ev) {
    var emoji = emojiList[$(ev.target).attr('data-classification')];
    var emojiEl = '';
    //  获取主题配色
    var btnColor = $('#emoji-classification').attr('data-color');

    //  清除之前选中的按钮的选中状态
    $('#emoji-classification .selected').attr('aria-checked', false);
    $('#emoji-classification .selected').removeClass([btnColor, 'selected']);
    //  设置点击按钮的选中状态
    $(ev.target).attr('aria-checked', true);
    $(ev.target).addClass(['selected', btnColor]);

    //  生成 Emoji 元素
    emoji.forEach(function (e) {
      emojiEl += '<div class="emoji p-2" tabindex="0" role="listitem">' + e + '</div>';
    });

    //  清除之前的 Emoji
    if ($('#emoji-list .emoji').length) {
      $('#emoji-list .emoji').remove();
    }

    //  把 Emoji 插入到页面
    $('#emoji-list').append(emojiEl);
    //  设置类型标题
    $('#emoji-title').html($(ev.target).attr('title'));
    //  设置用于屏幕阅读器的表情列表标题
    $('#emoji-list').attr('aria-label', $(ev.target).attr('title') + '（按回车可以把表情添加到评论内容输入框）');
  });

  //  Emoji 表情点击
  $('#emoji-list').on('click', '.emoji', function (ev) {
    //  把表情添加到评论内容输入框
    $('#textarea').val($('#textarea').val() + $(ev.target).html());
  });

  //  Emoji 表情按下回车键
  $('#emoji-list').on('keypress', '.emoji',function (ev) {
    if (ev.keyCode === 13) {
      //  把表情添加到评论内容输入框
      $('#textarea').val($('#textarea').val() + $(ev.target).html());
    }
  });

  //  Emoji 表情面板按下 ESC
  $('#emoji-panel').on('keydown', function (ev){
    if (ev.keyCode === 27) {
      //  调用 Emoji 开关事件
      $('#show-emoji-btn').click();
      $('#textarea').focus();
    }
  });
});

hljs.initHighlightingOnLoad();  //  代码高亮初始化