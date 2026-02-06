/*!
* HomePage: https://www.misterma.com
* GithubPage: https://github.com/changbin1997
* ProjectPage: https://github.com/changbin1997/MWordStar
* author: Changbin (changbin1997)
* Licensed under MIT
*/

export default () => {
  // 给链接设置在新标签页打开
  if ($('.post-content').attr('data-target') === '_blank' && $('.post-content a').length) {
    // 给文章中的所有链接添加 target 属性，让文章中的链接在新标签页打开
    $('.post-content a').attr('target', '_blank');
  }

  // 文章是否有密码
  if ($('.post-content .protected').length) {
    $('input[name="protectPassword"]').attr('placeholder', window.t.enterYourPassword);
    $('input[name="protectPassword"]').focus();
    $('.protected .submit').val(window.t.submit);
    $('.protected .word').html(window.t.enterThePasswordToViewIt);
  }

  // 给文章信息的分类链接添加 title
  if ($('.article-info .icon-folder-open').length) {
    $('.article-info .icon-folder-open').nextAll().attr({
      'title': window.t.category,
      'data-toggle': 'tooltip',
      'data-placement': 'top'
    });
    $('.icon-folder-open').nextAll().addClass($('.icon-folder-open').attr('data-color'));
  }

  // 给文章信息的标签链接添加 title
  if ($('.tags a').length) {
    $('.tags a').attr({
      'title': window.t.tag,
      'data-toggle': 'tooltip',
      'data-placement': 'top'
    });
    $('.tags a').addClass($('.tags').attr('data-color'));
  }

  // 给评论区的链接添加 target
  if ($('.comment-info b a').length) {
    // 让评论区的链接在新标签页打开
    $('.comment-info b a').attr('target', '_blank');
  }

  // 给回复链接添加回复对象名称的 title
  $('.comment-reply').each(function() {
    const authorName = $(this).closest('.comment-box').find('.author a').text() ||
        $(this).closest('.comment-box').find('.author').text();
    $(this).find('a').attr({
      title: `${window.t.replyTo} ${authorName}`,
      'data-toggle': 'tooltip',
      'data-placement': 'top'
    });
  });

  // 评论区的回复链接鼠标移入和移出
  $('#comments .comment-reply a').hover(ev => {
    // 根据主题配色设置评论高亮颜色
    const color = $('.dark-color').length ? '#383838' : '#E4E4E4';
    // 获取回复评论的 cid
    const cid = $(ev.target).parent().attr('data-id');
    // 改变回复评论的内容样式
    $(`#c-${cid}`).css('background', color);
  }, ev => {
    const cid = $(ev.target).parent().attr('data-id');
    $(`#c-${cid}`).css('background', 'none');
  });

  // @回复对象鼠标移入就高亮回复对象的评论内容
  $('#comments .parent-link').hover(ev => {
    // 根据主题配色设置评论高亮颜色
    const color = $('.dark-color').length ? '#383838' : '#E4E4E4';
    // 获取回复对象评论的 cid
    const cid = $(ev.target).attr('data-parent');
    $(`#c-comment-${cid}`).css('background', color);
  }, ev => {
    const cid = $(ev.target).attr('data-parent');
    $(`#c-comment-${cid}`).css('background', 'none');
  });

  // 提交回复按钮按下 tab
  $(document).on('keydown', '.comments-lists .respond .submit', ev => {
    ev.preventDefault();
    if (ev.key === 'Tab' || ev.keyCode === 9) {
      // 让取消回复的链接获取焦点
      $('#cancel-comment-reply-link').focus();
    }
  });

  // 设置取消回复链接的链接名称
  $('#cancel-comment-reply-link').html(window.t.cancelReply);

  // 给上一篇文章和下一篇文章的链接关联文字描述
  $('.previous a').attr('aria-describedby', 'previous-post-text');
  $('.next a').attr('aria-describedby', 'next-post-text');

  // 给评论区的作者链接加入用于屏幕阅读器的描述
  $('.author-tag').each(function() {
    $(this).closest('.comment-info').find('.author a').attr('title', window.t.author);
  });
}