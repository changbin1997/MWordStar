/*!
* HomePage: https://www.misterma.com
* GithubPage: https://github.com/changbin1997
* ProjectPage: https://github.com/changbin1997/MWordStar
* author: Changbin (changbin1997)
* Licensed under MIT
*/

// 代码高亮和 MathJax 初始化
export default class CodeAndMath {
  // MathJax 的加载地址，加载失败时按顺序切换到备用地址
  static mathJaxUrls = [
    'https://cdnjs.cloudflare.com/ajax/libs/mathjax/4.0.0/tex-mml-chtml.js',
    'https://cdn.jsdelivr.net/npm/mathjax@4.0.0/tex-mml-chtml.js'
  ];

  // MathJax 配置，只在加载前写入 window.MathJax
  static mathJaxConfig = {
    tex: {
      inlineMath: {
        // 添加 $...$ 行内公式分隔符
        '[+]': [['$', '$']]
      }
    }
  };

  // 是否已经写入 MathJax 配置
  static mathJaxConfigured = false;

  // 文本中常见的数学公式标记
  static mathPatterns = [
    /\$\$[\s\S]+?\$\$/,  // $$...$$ 块级公式
    /\\\[[\s\S]+?\\\]/,  // \[...\] 块级公式
    /\\\(.+?\\\)/,       // \(...\) 行内公式
    /\$[^$\n]+\$/,       // $...$ 行内公式
    /\\begin\{[a-zA-Z*]+\}[\s\S]+?\\end\{[a-zA-Z*]+\}/,  // \begin{...}...\end{...}
    /`[^`\n]+`/          // AsciiMath 的 `...` 标记
  ];

  /**
   * 初始化（代码高亮 + MathJax）
   */
  init() {
    // 代码高亮
    this.highlight();
    // MathJax
    this.mathJax();
  }

  /**
   * 代码高亮
   */
  highlight() {
    // 页面中有代码块才加载 highlight.js
    if ($('.enable-highlight').length && $('pre').length) {
      // highlight.js 的加载地址，已输出到 body 的 data-hljs-url
      const hljsUrl = $('body').attr('data-hljs-url');
      // 没有配置 highlight.js 的地址就不处理
      if (!hljsUrl) return;

      // 给文章中的代码块添加高亮、行号和拷贝按钮
      const highlightInit = () => {
        for (let i = 0; i < $('pre').length; i++) {
          // 是否是代码块
          if ($('pre').eq(i).children('code').length) {
            let codeStr = $('pre code').eq(i).text();
            // 检查代码末尾是否以换行符结尾
            if (codeStr.endsWith('\n')) {
              // 如果是，在末尾追加一个普通空格（或者零宽空格 '\u200b'）
              $('pre code')
                .eq(i)
                .text(codeStr + ' ');
            }

            // 添加代码高亮样式
            hljs.highlightBlock($('pre code').eq(i).get(0));

            // 生成代码行号
            if ($('.line-num-show').length) {
              // 获取代码行数
              const lineCount = $('pre code')
                .eq(i)
                .html()
                .split(/\r\n|\r|\n/).length;
              let lineNumbersEl = '';
              for (let j = 0; j < lineCount; j++) {
                lineNumbersEl += `<div class="text-right">${Number(j + 1)}</div>`;
              }
              $('pre')
                .eq(i)
                .prepend(`<div class="line-box">${lineNumbersEl}</div>`);
            }

            // 创建和添加拷贝按钮
            const btnEl = document.createElement('button');
            btnEl.className = 'copy-code-btn btn btn-sm';
            // 根据代码块的配色设置拷贝按钮的颜色
            btnEl.setAttribute('type', 'button');
            btnEl.innerHTML = '<i class="icon-copy"></i>';
            btnEl.setAttribute('data-clipboard-target', `#code-${i}`);
            btnEl.setAttribute('data-original-title', window.t.copyCode);
            btnEl.setAttribute('aria-label', window.t.copyCode);
            btnEl.setAttribute('data-toggle', 'tooltip');
            btnEl.setAttribute('data-placement', 'left');
            btnEl.setAttribute('id', `copy-btn-${i}`);
            $('pre').eq(i).prepend(btnEl);
            // 给代码块添加一个 id 方便拷贝
            $('pre code').eq(i).attr('id', `code-${i}`);
          }
        }

        // 初始化拷贝模块
        const clipboard = new ClipboardJS('.copy-code-btn');
        // 拷贝成功
        clipboard.on('success', function (ev) {
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
        clipboard.on('error', (ev) => {
          $(ev.trigger).attr('title', window.t.copyError);
          $(ev.trigger).attr('data-original-title', window.t.copyError);
          $(ev.trigger).tooltip('hide');
          $(ev.trigger).tooltip('show');
          setTimeout(function () {
            $(ev.trigger).attr('title', window.t.copyCode);
            $(ev.trigger).attr('data-original-title', window.t.copyCode);
          }, 1000);
        });
        // 初始化气球提示
        $('[data-toggle="tooltip"]').tooltip();
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

  /**
   * MathJax 初始化
   */
  mathJax() {
    // body 未开启 MathJax 支持就不处理
    if (!$('body').hasClass('mathjax-enable')) return;
    // 不是文章页
    if ($('.post-page').length < 1) return;
    // 没有文章内容就不处理
    const contentEl = $('.post-content');
    if (!contentEl.length) return;
    // 检测文章内容是否包含数学公式（忽略代码块）
    if (!this.hasMathJax(contentEl)) return;

    // MathJax 已经加载完成，重新渲染动态内容（PJAX 等）
    if (window.MathJax && typeof MathJax.typeset === 'function') {
      this.renderMathJax(contentEl);
      return;
    }

    // 写入 MathJax 配置（只在加载前配置一次）
    if (!CodeAndMath.mathJaxConfigured) {
      CodeAndMath.mathJaxConfigured = true;
      window.MathJax = CodeAndMath.mathJaxConfig;
    }

    // 已经有加载中的脚本就等待其加载完成，
    // MathJax 加载完成后会自动渲染当前页面，无需在这里处理
    if (this.getLoadingScript()) return;

    // 动态加载 MathJax，加载失败时切换到备用地址
    this.loadMathJax(0);
  }

  /**
   * 检测文章内容是否包含数学公式
   * @param contentEl jQuery 对象，文章内容元素
   */
  hasMathJax(contentEl) {
    // 忽略代码块中的数学公式
    const $clone = contentEl.clone().find('pre, code').remove().end();
    // MathML 标签
    if ($clone.find('math').length) return true;
    // 文本中的数学公式标记
    return this.detectMathJax($clone.text());
  }

  /**
   * 检测文本中是否包含数学公式标记
   * @param text string 要检测的文本
   */
  detectMathJax(text) {
    if (typeof text !== 'string') return false;
    return CodeAndMath.mathPatterns.some(pattern => pattern.test(text));
  }

  /**
   * 渲染文章内容中的数学公式
   * @param contentEl jQuery 对象，文章内容元素
   */
  renderMathJax(contentEl) {
    if (!window.MathJax) return;
    // 优先使用同步的 typeset：MathJax 4.0.0 的 typesetPromise 在内容替换后的重复渲染会挂起，
    // 如果同步渲染抛出 retry 错误（需要异步加载扩展或字体），再回退到 typesetPromise
    const render = () => {
      try {
        if (typeof MathJax.typeset === 'function') {
          MathJax.typeset([contentEl.get(0)]);
          return;
        }
      }catch (e) {
        // 忽略同步渲染错误
      }
      if (typeof MathJax.typesetPromise === 'function') {
        MathJax.typesetPromise([contentEl.get(0)]).catch(() => {});
      }
    };
    // 等待 MathJax 的 document 创建完成后再渲染；
    // 4.0.0 的 startup.promise 在自动排版后可能不会 resolve，不能用来等待
    let attempts = 0;
    const waitReady = () => {
      if (MathJax.startup && MathJax.startup.document) {
        render();
      }else if (attempts < 100) {
        attempts ++;
        setTimeout(waitReady, 100);
      }
    };
    waitReady();
  }

  /**
   * 获取正在加载中的 MathJax 脚本
   */
  getLoadingScript() {
    for (const url of CodeAndMath.mathJaxUrls) {
      const script = document.querySelector(`script[src="${url}"]`);
      if (script) return script;
    }
    return null;
  }

  /**
   * 动态加载 MathJax 库
   * @param index number 要使用的加载地址索引
   */
  loadMathJax(index) {
    const url = CodeAndMath.mathJaxUrls[index];
    // 没有更多备用地址就不处理
    if (!url) return;
    const script = document.createElement('script');
    script.src = url;
    // 加载完成后 MathJax 会自动渲染当前页面中的公式
    script.onerror = () => {
      // 移除加载失败的脚本，方便后续重新加载
      $(script).remove();
      // 尝试下一个备用地址
      this.loadMathJax(index + 1);
    };
    document.body.appendChild(script);
  }
}