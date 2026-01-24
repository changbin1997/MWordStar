/*!
* HomePage: https://www.misterma.com
* GithubPage: https://github.com/changbin1997
* ProjectPage: https://github.com/changbin1997/MWordStar
* author: Changbin (changbin1997)
* Licensed under MIT
*/

export default class PJAX {
  commentParentId = null;  // 存储父评论的id，用于PJAX评论提交后跳转

  /**
   * PJAX 初始化
   * @param {function} pjaxEnd pjax 替换完成后的回调函数
   * @returns {boolean} pjax 功能没有开启就返回 false
   */
  init(pjaxEnd = null) {
    // 没有开启 pjax 就直接返回
    if ($('body').attr('data-pjax') !== 'on') return false;
    // 给 pjax 链接添加 class
    this.pjaxLinkInit();

    // pjax 初始化
    $(document).pjax('.pjax-link', '#main', {
      fragment: '#main',
      timeout: 20000
    });

    // pjax 搜索表单提交
    $(document).on('submit', 'form[role="search"]', ev => {
      $.pjax.submit(ev, '#main', {
        fragment: '#main',
        replace: false,
        timeout: 20000
      });
    });

    // pjax 评论表单提交
    $(document).on('submit', '#comment-form', ev => {
      // 如果是回复评论就存储父评论的id
      if ($('#comment-parent').length && $('#comment-parent').val() !== '') {
        this.commentParentId = $('#comment-parent').val();
      }

      $.pjax.submit(ev, '#main', {
        fragment: '#main',
        replace: false,
        push: false,
        timeout: 20000
      });
    });

    // pjax 即将开始请求
    $(document).on('pjax:start', () => {
      // 如果开启了移动设备的导航菜单就关闭菜单
      if ($('.navbar-toggler').attr('aria-expanded') === 'true') {
        $('.navbar-toggler').click();
      }
      // 移除工具提示
      $('[data-toggle="tooltip"]').tooltip('dispose');
      // 显示进度条
      if ($('#progress-bar').length) {
        $('#progress-bar').show();
      }
    });

    // pjax 开始请求
    $(document).on('pjax:send', () => {
      if ($('#progress-bar').length) {
        // 更改进度条
        $('#progress-bar #progress').animate({
          width: '30%'
        }, 100);
        $('#progress-bar #progress').attr('aria-valuenow', '30');
      }
    });

    // pjax 请求完成
    $(document).on('pjax:complete', () => {
      if ($('#progress-bar').length) {
        // 更改进度条
        $('#progress-bar #progress').animate({
          width: '80%'
        }, 200);
        $('#progress-bar #progress').attr('aria-valuenow', '80');
      }
    });

    // pjax 替换完成
    $(document).on('pjax:end', ev => {
      // 隐藏进度条
      if ($('#progress-bar').length) {
        $('#progress-bar #progress').animate({
          width: '100%'
        }, 100, () => {
          $('#progress-bar').hide();
          $('#progress-bar #progress').css('width', '0');
          $('#progress-bar #progress').attr('aria-valuenow', '0');
        });
        $('#progress-bar #progress').attr('aria-valuenow', '100');
      }

      // 清除导航栏链接的选中状态
      $('.navbar-nav .nav-item').removeClass('active');
      $('.navbar-nav .nav-item a').removeAttr('aria-current');
      // 重新设置导航栏链接的选中状态
      for (let i = 0;i < $('.navbar-nav .nav-item a').length;i ++) {
        if ($('.navbar-nav .nav-item a').eq(i).attr('href') === ev.currentTarget.URL) {
          $('.navbar-nav .nav-item').eq(i).addClass('active');
          $('.navbar-nav .nav-item a').eq(i).attr('aria-current', 'page');
          break;
        }
      }

      // 如果是评论提交就滚动到评论区
      if (ev.relatedTarget?.id === 'comment-form') {
        if (this.commentParentId !== null && $(`#comment-${this.commentParentId}`).length) {
          // 如果是回复评论就滚动到父评论的区域
          $('html, body').animate({
            scrollTop: $(`#comment-${this.commentParentId}`).offset().top
          }, 250);
        }else {
          // 如果是评论提交就滚动到评论区
          $('html, body').animate({
            scrollTop: $('#comments').offset().top
          }, 250);
        }
        this.commentParentId = null;
      }

      // 给 pjax 链接添加 class
      this.pjaxLinkInit();

      // 如果传入了回调函数就执行函数
      if (typeof pjaxEnd === 'function') {
        pjaxEnd(ev);
      }
    });
  }

  /**
   * 给 pjax 链接添加 class
   */
  pjaxLinkInit() {
    const currentDomain = window.location.hostname;

    $('a').each((index, element) => {
      const href = $(element).attr('href');
      const target = $(element).attr('target');

      // 检查链接是否包含当前域名，且不含有 target="_blank"
      if (href && href.includes(currentDomain) && target !== '_blank') {
        $(element).addClass('pjax-link');
      }
    });
  }
}