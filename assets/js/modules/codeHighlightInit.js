/*!
* HomePage: https://www.misterma.com
* GithubPage: https://github.com/changbin1997
* ProjectPage: https://github.com/changbin1997/MWordStar
* author: Changbin (changbin1997)
* Licensed under MIT
*/

export default () => {
  // 页面中有代码块才加载 highlight.js
  if ($('.enable-highlight').length && $('pre').length) {
    // highlight.js 的加载地址，已输出到 body 的 data-hljs-url
    const hljsUrl = $('body').attr('data-hljs-url');
    // 没有配置 highlight.js 的地址就不处理
    if (!hljsUrl) return;

    // 给文章中的代码块添加高亮、行号和拷贝按钮
    const highlightInit = () => {
      for (let i = 0;i < $('pre').length;i ++) {
        // 是否是代码块
        if ($('pre').eq(i).children('code').length) {
          let codeStr = $('pre code').eq(i).text();
          // 检查代码末尾是否以换行符结尾
          if (codeStr.endsWith('\n')) {
            // 如果是，在末尾追加一个普通空格（或者零宽空格 '\u200b'）
            $('pre code').eq(i).text(codeStr + ' ');
          }

          // 添加代码高亮样式
          hljs.highlightBlock($('pre code').eq(i).get(0));

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
    };

    // highlight.js 已经加载过就直接初始化，避免重复加载
    if (window.hljs) {
      highlightInit();
      return;
    }

    // 已经存在相同地址的 script 标签，说明正在加载，等待加载完成
    const scriptEl = $(`script[src="${hljsUrl}"]`);
    if (scriptEl.length) {
      scriptEl.on('load', highlightInit);
      return;
    }

    // 动态创建 script 标签加载 highlight.js
    const script = document.createElement('script');
    script.src = hljsUrl;
    script.onload = highlightInit;
    // 加载失败时移除 script 标签，方便后续 PJAX 重新加载
    script.onerror = () => {
      $(script).remove();
    };
    document.body.appendChild(script);
  }
}

