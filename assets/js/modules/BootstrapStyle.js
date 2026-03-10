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
}