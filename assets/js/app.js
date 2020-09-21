$(function () {
    var emoji = null;  //  emoji表情
    var maxImg = false;  //  大图的状态
    var imgDirection = 0;  //  图片方向
    var imgWH = '';  //  记录图片的宽高

    //  给文章表格添加样式
    if ($('.post-content table').length) {
        for (var i = 0;i < $('.post-content table').length;i ++) {
            //  生成 Bootstrap 的响应式表格
            var table = '<div class="table-responsive"><table class="table table-striped table-bordered table-hover">' + $('.post-content table').eq(i).html() +  '</table></div>';
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
            }else {
                $('#max-img img').css('width', imgSize.w);
            }
            $('#max-img img').css('height', 'auto');
            imgWH = 'width';
        }else {
            if (imgSize.h >= $(window).height()) {
                $('#max-img img').css('height', '90%');
            }else {
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
        var X = ev.touches[0].pageX - $(this).offset().left;
        var Y = ev.touches[0].pageY - $(this).offset().top;

        $(document).on('touchmove', function (ev) {
            $('#max-img img').css({
                left: ev.touches[0].pageX - X,
                top: ev.touches[0].pageY - Y - $(document).scrollTop()
            });
        });

        $(document).on('touchend', function () {
            $(document).off('touchmove');
        });
        return false;
    });

    //  大图拖拽
   $('#max-img img').on('mousedown', function (ev) {
        var X = ev.clientX - $(this).offset().left;
        var Y = ev.clientY - $(this).offset().top;

        $(document).on('mousemove', function (ev) {
            $('#max-img img').css({
                left: ev.clientX - X,
                top: ev.clientY - Y - $(document).scrollTop()
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
        var size = imgWH === 'width'?$('#max-img img').width() + 40:$('#max-img img').height() + 40;
        $('#max-img img').css('transition', '0.2s');
        $('#max-img img').css(imgWH, size + 'px');
        setTimeout(function () {
            $('#max-img img').css('transition', '0s');
        }, 300);
    });

    //  图片缩小
    $('#img-control .small').on('click', function () {
        var size = imgWH === 'width'?$('#max-img img').width() - 40:$('#max-img img').height() - 40;
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
        for (var i = 0;i < $('.post-content a').length;i ++) {
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

    $('.post-content a').addClass($('.post-content').attr('data-color'));  //  给文章中的链接添加颜色类

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
        $('.comment-info b a').attr('target', '_blank');  //  让评论区的链接在新标签页打开
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

    //  显示 emoji按钮点击
    $('#show-emoji').on('click', function () {
        //  是否是第一次点击
       if ($(this).attr('aria-expanded') && emoji == null) {
           //  通过 ajax 加载 emoji
           $.post($('#show-emoji').attr('url'), 'emoji=emoji', function (data) {
               emoji = JSON.parse(data);
               $('#emoji-box .emoji-classification button').eq(0).click();  //  设置 emoji分类
           });
       }
    });

    for (var i = 0;i < $('#emoji-box .emoji-classification button').length;i ++) {
        //  emoji分类按钮点击
        $('#emoji-box .emoji-classification button').eq(i).on('click', function () {
            if (emoji == null) {
                return false;
            }
            $('#emoji-box .emoji-classification button').removeClass('active');  //  取消所有分类按钮的选中状态
            $(this).addClass('active');  //  设置当前按钮为选中状态
            $('#emoji-box .emoji-classification button').attr('aria-checked', false);  //  取消所有分类按钮的选中状态（读屏专用）
            $(this).attr('aria-checked', true);  //  设置当前按钮为选中状态（读屏专用）
            var emojiDOM = '';
            emoji[$(this).attr('classification')].forEach(function (val) {
                emojiDOM += '<div role="listitem" class="float-left emoji" tabindex="0" role="button">' + val + '</div>';
            });
            $('#emoji-box .emoji-select').html('');  //  清空其它的 emoji
            $('#emoji-box .emoji-select').append(emojiDOM);  //  把 emoji 插入到页面
            $('#emoji-box .emoji-select').attr('aria-label', $(this).attr('title'));
        });
    }

    //  emoji表情点击
    $('#emoji-box .emoji-select').on('click', '.emoji', function () {
       $('#textarea').val($('#textarea').val() + $(this).html());  //  把表情加入评论框
    });

    //  emoji 键盘事件
    $('#emoji-box .emoji-select').on('keypress', '.emoji', function (ev) {
       if (ev.keyCode == 13) {
           $(this).click();
       }
    });

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
            }else {
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

    //  ESC键按下
    $(document).on('keyup', function (ev) {
        if (ev.keyCode == 27 && maxImg) {
            $('#max-img .hide-img').click();  //  关闭大图
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

    $('[data-toggle="tooltip"]').tooltip();  //  初始化工具提示

    //  把父评论的姓名加入到子评论中
    if ($('#comments .parent').length) {
        var linkColor = $('.post-content').attr('data-color');
        for (var i = 0; i < $('#comments .parent').length; i ++) {
            var parentLink = '<a class="mr-1 ' + linkColor + '" href="' + $('#comments .parent').eq(i).attr('href') + '">' + $('#comments .parent').eq(i).html() + '</a>';
            $('#comments .parent').eq(i).next().prepend(parentLink);
        }
        $('#comments .parent').remove();
    }

    //  头像图片加载完成后删除图片背景
    $('.avatar').on('load', function () {
        $(this).css('background', 'none');
    });
});

hljs.initHighlightingOnLoad();  //  代码高亮初始化