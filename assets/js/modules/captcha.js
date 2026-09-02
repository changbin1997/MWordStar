let captchaTimer = null;  // 验证码自动刷新定时器

export default () => {
  // 验证码图片是否存在
  if ($('#captcha-img').length === 0) {
    return;
  }

  // 清除上一次的定时器，避免 PJAX 切换页面后残留多个定时器
  if (captchaTimer) {
    clearInterval(captchaTimer);
  }

  const $container = $('#img-captcha');
  const $image = $('#captcha-img');
  const $token = $('#captcha-token');
  // 请求验证码的地址，来自图片的 data-url 属性
  const url = $image.data('url');

  // 获取验证码
  function getCaptcha() {
    $.ajax({
      url,
      data: {
        action: 'captcha'
      },
      dataType: 'json',
      success: res => {
        if (res.result === 'success') {
          // 移除错误提示
          $container.find('.captcha-error').remove();
          // 把验证码图片和 token 写入表单
          $image.show();
          $image.attr('src', `data:image/png;base64,${res.image}`);
          $image.attr('alt', window.t?.captchaImageAlt || '');
          $token.val(res.token);
        } else {
          // 生成失败时把图片替换为错误信息
          showError(res.message || window.t?.captchaLoadError || '');
        }
      },
      error: () => {
        showError(window.t?.captchaLoadError || '');
      }
    });
  }

  // 显示错误信息，点击错误信息可以重新获取验证码
  function showError(message) {
    $container.find('.captcha-error').remove();
    $image.hide();
    $('<span>', {
      class: 'captcha-error text-danger',
      text: message,
      click: getCaptcha
    }).appendTo($container);
  }

  // 点击验证码图片更换验证码
  $image.on('click', getCaptcha);

  // 首次加载验证码
  getCaptcha();

  // 每隔 5 分钟自动刷新验证码，避免阅读文章后提交评论时验证码过期
  captchaTimer = setInterval(getCaptcha, 5 * 60 * 1000);
};
