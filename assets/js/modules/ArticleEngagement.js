/*!
* HomePage: https://www.misterma.com
* GithubPage: https://github.com/changbin1997
* ProjectPage: https://github.com/changbin1997/MWordStar
* author: Changbin (changbin1997)
* Licensed under MIT
*/

export default class ArticleEngagement {
  /**
   * 点赞初始化
   */
  static likeInit() {
    if ($('#agree-btn').length) {
      $('#agree-btn').on('click', () => {
        $('#agree-btn').prop('disabled', true);
        $.ajax({
          type: 'post',
          url: $('#agree-btn').attr('data-url'),
          data: `agree=${$('#agree-btn').attr('data-cid')}`,
          async: true,
          timeout: 30000,
          cache: false,
          success: data => {
            const re = /\d/;
            if (re.test(data)) {
              $('#agree-btn .agree-num').html(data);
              $('.post-page').append(`<span id="agree-p" role="alert">${window.t.like} + 1</span>`);
              $('#agree-p').css({
                top: $('#agree-btn').offset().top - 25,
                left: $('#agree-btn').offset().left + $('#agree-btn').outerWidth() / 2 - $('#agree-p').outerWidth() / 2
              });

              $('#agree-p').animate({
                top: $('#agree-btn').offset().top - 70,
                opacity: 0
              }, 400, () => {
                $('#agree-p').remove();
              });
            }
          },
          error: () => {
            $('#agree-btn').prop('disabled', false);
          }
        });
      });
    }
  }

  /**
   * 生成分享二维码
   */
  static shareQrCode() {
    // 是否是文章页
    if ($('#qr').length) {
      // 生成文章二维码
      $('#qr').qrcode({
        width: 150,
        height: 150,
        text: $('#share-btn').attr('data-url')
      });

      // 给二维码图片添加 alt 属性
      $('#qr canvas').attr({
        role: 'img',
        'aria-label': window.t.QRCode
      });
    }
  }
}