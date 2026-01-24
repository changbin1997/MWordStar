/*!
* HomePage: https://www.misterma.com
* GithubPage: https://github.com/changbin1997
* ProjectPage: https://github.com/changbin1997/MWordStar
* author: Changbin (changbin1997)
* Licensed under MIT
*/

export default () => {
  // 给文章中的代码块添加拷贝按钮和拷贝事件
  if ($('.enable-highlight').length && $('pre').length) {
    for (let i = 0;i < $('pre').length;i ++) {
      // 添加代码高亮样式
      hljs.highlightBlock($('pre code').eq(i).get(0));

      // 是否是代码块
      if ($('pre').eq(i).children('code').length) {
        // 生成代码行号
        if ($('.line-num-show').length) {
          // 获取代码行数
          const lineCount = $('pre code').eq(i).html().split(/\r\n|\r|\n/).length;
          let lineNumbersEl = '';
          for (let j = 0;j < lineCount;j ++) {
            lineNumbersEl += `<div class="text-right">${Number(j + 1)}</div>`;
          }
          $('pre').eq(i).prepend(`<div class="line-box">${lineNumbersEl}</div>`);
        }

        // 创建和添加拷贝按钮
        const btnEl = document.createElement('button');
        btnEl.className = 'copy-code-btn btn btn-sm';
        // 根据代码块的配色设置拷贝按钮的颜色
        btnEl.setAttribute('type', 'button');
        btnEl.innerHTML = '<i class="icon-copy"></i>';
        btnEl.setAttribute('data-clipboard-target', `#code-${i}`);
        btnEl.setAttribute('title', window.t.copyCode);
        btnEl.setAttribute('aria-label', window.t.copyCode);
        btnEl.setAttribute('data-toggle', 'tooltip');
        btnEl.setAttribute('data-placement', 'left');
        btnEl.setAttribute('id', `copy-btn-${i}`);
        $('pre').eq(i).prepend(btnEl);
        // 给代码块添加一个 id 方便拷贝
        $('pre code').eq(i).attr('id', `code-${i}`);
      }
      // 初始化拷贝模块
      const clipboard = new ClipboardJS('.copy-code-btn');
      // 拷贝成功
      clipboard.on('success', function(ev) {
        // 把工具提示更改为拷贝成功
        $(ev.trigger).attr('title', window.t.copySuccess);
        $(ev.trigger).attr('data-original-title', window.t.copySuccess);
        $(ev.trigger).tooltip('update');
        $(ev.trigger).tooltip('show');
        // 延迟 1 秒后把工具提示更改为拷贝代码
        setTimeout(() => {
          $(ev.trigger).attr('title', window.t.copyCode);
          $(ev.trigger).attr('data-original-title', window.t.copyCode);
        }, 1000);
      });
      // 拷贝出错
      clipboard.on('error', ev => {
        $(ev.trigger).attr('title', window.t.copyError);
        $(ev.trigger).attr('data-original-title', window.t.copyError);
        $(ev.trigger).tooltip('hide');
        $(ev.trigger).tooltip('show');
        setTimeout(function() {
          $(ev.trigger).attr('title', window.t.copyCode);
          $(ev.trigger).attr('data-original-title', window.t.copyCode);
        }, 1000);
      });
    }
  }
}