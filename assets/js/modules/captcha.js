/*!
* HomePage: https://www.misterma.com
* GithubPage: https://github.com/changbin1997
* ProjectPage: https://github.com/changbin1997/MWordStar
* author: Changbin (changbin1997)
* Licensed under MIT
*/

/**
 * 图片验证码管理类
 * 负责获取、显示、刷新验证码，并处理错误状态
 */
export default class Captcha {
  // ============ 实例属性（类字段定义） ============
  /** @type {number|null} 自动刷新定时器句柄 */
  captchaTimer = null;
  /** @type {jQuery|null} 验证码容器 */
  $container = null;
  /** @type {jQuery|null} 验证码图片元素 */
  $image = null;
  /** @type {jQuery|null} 验证码 token 输入域 */
  $token = null;
  /** @type {string|null} 请求验证码的 API 地址 */
  url = null;
  turnstileWidgetId = null;
  /** @type {number|null} Turnstile SDK 未加载完成时的重试定时器句柄 */
  turnstileWaitTimer = null;

  /**
   * 初始化 Cloudflare Turnstile 或图片验证码
   * @param {string} turnstileTheme 可选的 Cloudflare Turnstile 组件配色
   */
  constructor() {
    // Cloudflare Turnstile 第一次监听渲染
    this.renderTurnstile();
    // 图片验证码
    this.imageCaptcha();
  }

  /**
   * 重新渲染 Cloudflare Turnstile 或图片验证码
   * @param {string} turnstileTheme 可选的 Cloudflare Turnstile 组件配色
   */
  reRender() {
    this.renderTurnstile();
    this.imageCaptcha();
  }

  /**
   * 销毁 Cloudflare Turnstile 实例
   * 必须在 PJAX 替换 DOM 之前调用，避免 Turnstile SDK 检测到
   * 被移除的组件后输出 "Cannot find Widget ..." 之类的警告/报错
   */
  removeTurnstile() {
    if (this.turnstileWidgetId === null) return;
    if (typeof turnstile !== 'undefined') {
      turnstile.remove(this.turnstileWidgetId);
    }
    this.turnstileWidgetId = null;
  }

  /**
   * Typecho 评论回复/取消回复会移动评论表单，导致已渲染的 Turnstile
   * 组件失效。这里在表单被移动前销毁组件、移动后重新渲染。
   * 注意：PJAX 切换页面会重新执行模板脚本并重置 TypechoComment，
   * 所以每次 PJAX 完成后需要重新调用本方法。
   */
  bindCommentReply() {
    if (typeof window.TypechoComment === 'undefined') return;
    const commentReply = window.TypechoComment.reply;
    const cancelReply = window.TypechoComment.cancelReply;

    if (typeof commentReply === 'function') {
      window.TypechoComment.reply = (...args) => {
        this.removeTurnstile();
        const result = commentReply.apply(window.TypechoComment, args);
        this.renderTurnstile();
        return result;
      };
    }
    if (typeof cancelReply === 'function') {
      window.TypechoComment.cancelReply = (...args) => {
        this.removeTurnstile();
        const result = cancelReply.apply(window.TypechoComment, args);
        this.renderTurnstile();
        return result;
      };
    }
  }

  /**
   * 渲染 Cloudflare Turnstile 组件
   */
  renderTurnstile() {
    // Turnstile 容器不存在就返回
    if ($('#turnstile-container').length < 1) return;

    // 如果 Turnstile SDK 还没加载好，稍后自动重试渲染
    if (typeof turnstile === 'undefined') {
      this.waitTurnstile();
      return;
    }

    const container = $('#turnstile-container').get(0);
    // PJAX 切换后清空容器内可能残存的旧节点（作为兜底）
    container.innerHTML = '';

    // 获取主题配色
    const turnstileTheme = $('.dark-color').length ? 'dark' : 'light';
    // 执行渲染
    this.turnstileWidgetId = turnstile.render('#turnstile-container', {
      sitekey: container.getAttribute('data-sitekey'),
      theme: turnstileTheme,
      size: 'normal',
      'before-interactive-callback': () => {
        // 移除小组件容器的 class，避免下方间距问题
        $('#turnstile-box').removeClass('mb-0');
      },
      'expired-callback': () => {
        // token 过期后自动重置，避免提交评论时人机验证失败
        if (this.turnstileWidgetId !== null) {
          turnstile.reset(this.turnstileWidgetId);
        }
      }
    });
  }

  /**
   * Turnstile SDK 尚未加载完成时轮询等待，加载完成后重新渲染
   */
  waitTurnstile() {
    if (this.turnstileWaitTimer !== null) return;
    let attempts = 0;
    this.turnstileWaitTimer = setInterval(() => {
      if (typeof turnstile !== 'undefined') {
        clearInterval(this.turnstileWaitTimer);
        this.turnstileWaitTimer = null;
        this.renderTurnstile();
        return;
      }
      // 最多等待 30 秒，避免 SDK 加载失败时定时器一直运行
      if (++attempts >= 120) {
        clearInterval(this.turnstileWaitTimer);
        this.turnstileWaitTimer = null;
      }
    }, 250);
  }

  /**
   * 图片算数验证码
   */
  imageCaptcha() {
    // 【关键修复】：必须在判断元素是否存在之前，先清理上一次的定时器
    // 否则 PJAX 跳转到无验证码页面时，旧页面的定时器会一直在后台运行
    if (this.captchaTimer) {
      clearInterval(this.captchaTimer);
      this.captchaTimer = null;
    }

    // 验证码不存在就直接返回
    if ($('#captcha-img').length < 1) return;

    // 缓存 DOM 引用
    this.$container = $('#img-captcha');
    this.$image = $('#captcha-img');
    this.$token = $('#captcha-token');
    this.url = this.$image.data('url');

    // 解绑旧事件并绑定新事件（防止极小概率下的重复绑定）
    this.$image.off('click').on('click', this.getCaptcha.bind(this));

    // 首次加载
    this.getCaptcha();

    // 每 5 分钟自动刷新
    this.captchaTimer = setInterval(
        this.getCaptcha.bind(this),
        5 * 60 * 1000
    );
  }

  /**
   * 从服务端获取新的验证码
   * - 发送 AJAX 请求
   * - 成功时更新图片和 token
   * - 失败时显示错误信息
   */
  getCaptcha() {
    $.ajax({
      url: this.url,
      data: { action: 'captcha' },
      dataType: 'json',
      success: (res) => {
        if (res.result === 'success') {
          this.$container.find('.captcha-error').remove();
          this.$image.show();
          this.$image.attr('src', `data:image/png;base64,${res.image}`);
          this.$image.attr('alt', window.t?.captchaImageAlt || '');
          this.$token.val(res.token);
        } else {
          this.showError(res.message || window.t?.captchaLoadError || '');
        }
      },
      error: () => {
        this.showError(window.t?.captchaLoadError || '');
      }
    });
  }

  /**
   * 显示验证码加载错误信息
   * - 隐藏图片，显示可点击的错误文本
   * - 点击错误文本可重新获取验证码
   *
   * @param {string} message - 要显示的错误信息
   */
  showError(message) {
    this.$container.find('.captcha-error').remove();
    this.$image.hide();
    $('<span>', {
      class: 'captcha-error text-danger',
      text: message,
      click: this.getCaptcha.bind(this)
    }).appendTo(this.$container);
  }
}
