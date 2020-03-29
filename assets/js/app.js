$(function () {
    var emoji = null;  //  emoji表情
    var maxImg = false;  //  大图的状态

    //  给文章表格添加样式
    if ($('.post-content table').length > 0) {
        for (var i = 0;i < $('.post-content table').length;i ++) {
            //  生成 Bootstrap 的响应式表格
            var table = '<div class="table-responsive"><table class="table table-striped table-bordered table-hover">' + $('.post-content table').eq(i).html() +  '</table></div>';
            $('.post-content table').eq(i).replaceWith(table);  //  替换文章中的表格
        }
    }

    //  文章的图片点击
    $('.article-page article img').on('click', function () {
        $('#max-img').css('display', 'block');
        setTimeout(function () {
            $('#max-img').css('opacity', 1);
            $('#max-img img').css({
                "max-width": "90%",
                "max-height": "90%"
            });
        });
        //  设置大图的 src 和 alt
        $('#max-img img').attr({
            "src": $(this).attr('src'),
            "alt": $(this).attr('alt')
        });
        $('.hide-img').focus();  //  让关闭图片的按钮获取焦点
        maxImg = true;
    });

    //  大图点击
    $('#max-img').on('click', function () {
        //  隐藏大图
        $('#max-img').css('opacity', 0);
        $('#max-img img').css({
            "max-width": 0,
            "max-height": 0
        });
        setTimeout(function () {
            $('#max-img').css('display', 'none');
            $('#max-img img').attr('src', '');
        }, 300);
        maxImg = false;
    });

    //  给链接设置在新标签页打开
    if ($('.post-content a').length > 0) {
        //  给文章中的所有链接添加 target 属性，让文章中的链接在新标签页打开
        $('.post-content a').attr('target', '_blank');
    }

    //  给分页链接设置样式
    if ($('.pagination-nav ul li').length > 0) {
        //  给分页链接添加符合 Bootstrap 样式标准的 class
        $('.pagination-nav ul').addClass('justify-content-center');
        $('.pagination-nav ul li').addClass('page-item');
        $('.pagination-nav ul li a').addClass('page-link');
        //  给上一页和下一页加入 aria-label 属性，兼容屏幕阅读器
        $('.pagination-nav .prev a').attr('aria-label', '上一页');
        $('.pagination-nav .next a').attr('aria-label', '下一页');
    }

    //  给文章信息的分类链接添加 title
    if ($('.icon-folder-open').length > 0) {
        $('.icon-folder-open').nextAll().attr({
            'title': '分类',
            'data-toggle': 'tooltip',
            'data-placement': 'top'
        });
    }

    //  给文章信息的标签链接添加 title
    if ($('.tags a').length > 0) {
        $('.tags a').attr({
            'title': '标签',
            'data-toggle': 'tooltip',
            'data-placement': 'top'
        });
    }

    //  给评论区的链接添加 target
    if ($('.comment-info b a').length > 0) {
        $('.comment-info b a').attr('target', '_blank');  //  让评论区的链接在新标签页打开
    }


    //  给评论区的分页链接设置样式
    if ($('.comments-lists .page-navigator').length > 0) {
        $('.comments-lists .page-navigator li').addClass('pagination');
        $('.comments-lists .page-navigator li a').addClass('page-link');
        var pageNav = '<nav aria-label="分页导航区"><ol class="pagination justify-content-center page-navigator">' + $('.comments-lists .page-navigator').html() + '</ol></nav>';
        $('.comments-lists .page-navigator').replaceWith(pageNav);
    }

    //  给侧边栏近期文章的第一篇文章设置头图
    if ($('.latest-articles a').eq(0).children('.article-img').length > 0) {
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
    if ($('.post-content .protected').length > 0) {
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
            var emojiDOM = '';
            emoji[$(this).attr('classification')].forEach(function (val) {
                emojiDOM += '<div class="float-left emoji" tabindex="0" role="button">' + val + '</div>';
            });
            $('#emoji-box .emoji-select').html('');  //  清空其它的 emoji
            $('#emoji-box .emoji-select').append(emojiDOM);  //  把 emoji 插入到页面
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
        $('header .navbar-brand').get(0).focus();
        $(document).scrollTop(0);
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

        if (maxImg) {
            $('#max-img').click();
        }
    });

    //  大图的关闭按钮按下回车
    $('.hide-img').on('keypress', function (ev) {
        if (ev.keyCode == 13) {
            $('#max-img').click();
        }
    });

    //  ESC键按下
    $(document).on('keyup', function (ev) {
        if (ev.keyCode == 27 && maxImg) {
            $('#max-img').click();  //  关闭大图
        }
    });

    //  是否是文章页
    if ($('#qrcode').length > 0) {
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
                    $('.post-page').append('<span id="agree-p">赞 + 1</span>');
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
    if ($('#comments .parent').length > 0) {
        for (var i = 0; i < $('#comments .parent').length; i ++) {
            var parentLink = '<a class="mr-1" href="' + $('#comments .parent').eq(i).attr('href') + '">' + $('#comments .parent').eq(i).html() + '</a>';
            $('#comments .parent').eq(i).next().prepend(parentLink);
        }
        $('#comments .parent').remove();
    }

    //  生成目录
    if ($('.post-content').length > 0 && $('.post-content').attr('data-atalog')) {
        if ($('.post-content h2').length < 1 && $('.post-content h3').length < 1 && $('.post-content h4').length < 1 && $('.post-content h5').length < 1) {
            return false;
        }

        $('.post-content h2').addClass('content-title');
        $('.post-content h3').addClass('content-title');
        $('.post-content h4').addClass('content-title');
        $('.post-content h5').addClass('content-title');
        $('.post-content h2').attr('data-index', 2);
        $('.post-content h3').attr('data-index', 3);
        $('.post-content h4').attr('data-index', 4);
        $('.post-content h5').attr('data-index', 5);

        var el = atalog(2, 0);
        $('.post-content').prepend('<div class="pt-2 pb-2 atalog"><h2>目录</h2>' + el.el + '</div>');
        $('.post-content .atalog').children('ul').attr('aria-label', '目录');
    }

    $('.post-content .atalog a').on('click', function () {
        $(document).scrollTop(Number($('.content-title').eq($(this).attr('data-index')).offset().top) - 60);
    });
});

//  生成目录
function atalog(titleIndex, start) {
    var el = {
        el: '',
        index: start
    };
    var current = 0;
    for (var i = start;i < $('.content-title').length;i ++) {
        if (i < current) {
            continue;
        }
        if ($('.content-title').eq(i).attr('data-index') > titleIndex) {
            var result = atalog($('.content-title').eq(i).attr('data-index'), i);
            if (result.el != '') {
                el.el += '<li> ' + result.el + '</li>';
            }
            current = result.index + 1;
            el.index = result.index;
            continue;
        }
        if ($('.content-title').eq(i).attr('data-index') < titleIndex) {
            if (el.el != '') {
                el.el = '<ul class="mb-2">' + el.el + '</ul>';
            }
            return el;
        }
        el.el += '<li><a data-index="' + i + '" href="javascript:;">' + $('.content-title').eq(i).text() + '</a></li>';
        el.index = i;
    }

    if (el.el != '') {
        el.el = '<ul class="mb-2"> ' + el.el + '</ul>';
    }
    return el;
}

hljs.initHighlightingOnLoad();  //  代码高亮初始化