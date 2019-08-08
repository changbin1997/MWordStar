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
            src: $(this).attr('src'),
            alt: $(this).attr('alt')
        });
    });

    //  大图点击
    $('#max-img').on('click', function () {
        //  隐藏大图
        $(this).fadeOut(300, function () {
            $('#max-img img').attr({
                src: '',
                alt: ''
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

});

hljs.initHighlightingOnLoad();  //  代码高亮初始化