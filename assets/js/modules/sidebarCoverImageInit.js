/*!
* HomePage: https://www.misterma.com
* GithubPage: https://github.com/changbin1997
* ProjectPage: https://github.com/changbin1997/MWordStar
* author: Changbin (changbin1997)
* Licensed under MIT
*/


/**
 * 侧边栏文章头图初始化
 */
export default () => {
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
}