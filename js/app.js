$(function () {
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
        $('#max-img').fadeIn(300);  //  显示大图
        //  设置大图的 src 和 alt
        $('#max-img img').attr({
            "src": $(this).attr('src'),
            "alt": $(this).attr('alt')
        });
    });

    //  大图点击
    $('#max-img').on('click', function () {
        //  隐藏大图
        $(this).fadeOut(300, function () {
            $('#max-img img').attr({
                "src": "",
                "alt": ""
            });
        });
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
        $('.icon-folder-open').nextAll().attr('title', '分类');
    }

    //  给文章信息的标签链接添加 title
    if ($('.tags a').length > 0) {
        $('.tags a').attr('title', '标签');
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

    //  监听滚动条
    $(document).on('scroll', function (ev) {
        if ($('.jumbotron').length > 0 && $('header .navbar-toggler').attr('aria-expanded') == 'false') {
            //  根据滚动条的高度调整导航条的背景透明度
            if ($(document).scrollTop() > $('.jumbotron').outerHeight() - 56) {
                $('header .navbar').addClass('bg-dark');
            }else {
                $('header .navbar').removeClass('bg-dark');
            }
        }
    });
    
    //  导航菜单按钮点击
    $('header .navbar-toggler').on('click', function () {
        //  是否开启了巨幕显示和巨幕是否处于可见区域
        if ($('.jumbotron').length > 0 && $(document).scrollTop() < $('.jumbotron').outerHeight() - 56) {
            //  如果导航菜单为展开状态
            if ($(this).attr('aria-expanded') == 'false') {
                $('header .navbar').addClass('bg-dark');  //  设置导航区域的透明度为不透明
            }else {
                $('header .navbar').removeClass('bg-dark');  //  设置导航区域的透明度为透明
            }
        }
    });
});

hljs.initHighlightingOnLoad();  //  代码高亮初始化