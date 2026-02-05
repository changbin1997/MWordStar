/*!
* HomePage: https://www.misterma.com
* GithubPage: https://github.com/changbin1997
* ProjectPage: https://github.com/changbin1997/MWordStar
* author: Changbin (changbin1997)
* Licensed under MIT
*/

export default class BootstrapStyle {
  /**
   * 一些 bootstrap 样式初始化
   */
  init() {
    // 表格样式初始化
    this.tableInit();
    // 分页链接样式初始化
    this.paginationLinkInit();
    // 初始化工具提示
    $('[data-toggle="tooltip"]').tooltip();
  }

  /**
   * 表格样式初始化
   */
  tableInit() {
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
  }

  /**
   * 分页链接样式初始化
   */
  paginationLinkInit() {
    // 给分页链接设置样式
    if ($('.pagination-nav ul li').length) {
      // 给分页链接添加符合 Bootstrap 样式标准的 class
      $('.pagination-nav ul li').addClass('page-item');
      $('.pagination-nav ul li a').addClass('page-link');
      // 给上一页和下一页加入文字提示
      $('.pagination-nav .prev a').attr({
        'aria-label': window.t.previousPage,
        'title': window.t.previousPage,
        'data-toggle': 'tooltip',
        'data-placement': 'top'
      });
      $('.pagination-nav .next a').attr({
        'aria-label': window.t.nextPage,
        'title': window.t.nextPage,
        'data-toggle': 'tooltip',
        'data-placement': 'top'
      });
      // 给当前页的链接加入一个标识
      $('.pagination-nav .active a').attr('aria-current', 'page');
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
  }
}