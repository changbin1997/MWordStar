/*!
* HomePage: https://www.misterma.com
* GithubPage: https://github.com/changbin1997
* ProjectPage: https://github.com/changbin1997/MWordStar
* author: Changbin (changbin1997)
* Licensed under MIT
*/

import PJAX from './PJAX.js';

export default () => {

  if ($('.load-more-post-btn').length) {
    const loadMorePostBtn = $('.load-more-post-btn');

    // 加载更多点击
    loadMorePostBtn.on('click', () => {
      // 按钮已禁用则直接返回
      if (loadMorePostBtn.prop('disabled')) return;
      // 获取下一页的链接地址
      const $nextLink = $('.pagination .next .page-link');
      if (!$nextLink.length || !$nextLink.attr('href')) return false;
      const nextPageUrl = $nextLink.attr('href');

      // 禁用按钮，显示加载状态
      loadMorePostBtn.prop('disabled', true);
      loadMorePostBtn.html(window.t.loading);
      // 发送请求
      $.ajax({
        url: nextPageUrl,
        method: 'GET',
        dataType: 'html',
        timeout: 30000,
        success: html => {
          // 恢复按钮状态
          loadMorePostBtn.prop('disabled', false);
          loadMorePostBtn.html(window.t.loadMore);

          const $html = $(html);

          // 从响应中提取文章列表项
          const $newPosts = $html.find('.article-list > .post');

          if ($newPosts.length) {
            // 隐藏新文章，插入到分页导航之前（加载更多按钮上方），然后淡入显示
            $newPosts.hide().insertBefore('.article-list > nav.pagination-nav');
            $newPosts.each(function (i) {
              $(this).delay(i * 80).fadeIn(400);
            });
          }

          // 替换分页导航（隐藏的 .page-nav 用于存储下一页链接）
          const $newNav = $html.find('.article-list > nav.pagination-nav').first();
          if ($newNav.length) {
            $('.article-list > nav.pagination-nav').first().replaceWith($newNav);
          }

          // 检查是否还有下一页，没有则隐藏加载更多按钮
          const $newNextLink = $('.pagination .next .page-link');
          if (!$newNextLink.length || !$newNextLink.attr('href')) {
            loadMorePostBtn.closest('nav').hide();
          }

          // 如果开启了 PJAX 就给新加载的链接添加 PJAX 的 class
          if ($('body').attr('data-pjax') === 'on') {
            const pjax = new PJAX();
            pjax.pjaxLinkInit();
          }
        },
        error: () => {
          // 恢复按钮状态
          loadMorePostBtn.prop('disabled', false);
          loadMorePostBtn.html(window.t.loadMore);
        }
      });
    });
  }
}