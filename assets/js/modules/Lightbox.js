/*!
* HomePage: https://www.misterma.com
* GithubPage: https://github.com/changbin1997
* ProjectPage: https://github.com/changbin1997/MWordStar
* author: Changbin (changbin1997)
* Licensed under MIT
*/

export default class Lightbox {
  imgCount = 0;  // 图片总数量
  imgIndex = null;
  srcImgSize = {width: 0, height: 0};  // 图片真实尺寸
  imgElSize = {width: 0, height: 0, left: 0, top: 0};  // 文章内的图片元素尺寸和位置
  isShow = false;  // 图片灯箱是否开启
  maxImgSize = {width: 0, height: 0};  // 图片灯箱内显示的图片大小
  allowMove = false;  // 图片是否可以拖动
  direction = 0;  // 图片旋转角度

  /**
   * 计算图片灯箱内显示的图片尺寸
   * @returns {{targetHeight: number, targetWidth: number}} 返回图片灯箱内使用的图片宽度和高度
   */
  calculateImgSize() {
    // 默认使用原始尺寸
    let targetWidth = this.srcImgSize.width;
    let targetHeight = this.srcImgSize.height;
    if (this.srcImgSize.width > window.innerWidth || this.srcImgSize.height > window.innerHeight) {
      // 计算宽高各自的缩放比例
      const widthRatio = window.innerWidth / this.srcImgSize.width;
      const heightRatio = window.innerHeight / this.srcImgSize.height;
      // 取较小的缩放比例，保证宽高都不超出
      const scale = Math.min(widthRatio, heightRatio);

      targetWidth = Math.round(this.srcImgSize.width * scale);
      targetHeight = Math.round(this.srcImgSize.height * scale);
    }

    return {targetHeight, targetWidth};
  }

  /**
   * 初始化
   */
  init() {
    if ($('.post-content img').length < 1) return;
    // 获取图片总数量
    this.imgCount = $('.post-content img').length;
    // 给文章中的图片添加一个索引
    for (let i = 0;i < this.imgCount;i ++) {
      $('.post-content img').eq(i).attr('data-index', i);
    }

    // 文章内的图片点击
    $('.post-content img').on('click', ev => {
      // 显示图片灯箱
      this.show(ev);

      // 鼠标拖动初始化
      this.mouseMove();

      // 手指拖动初始化
      this.touchMove();

      // 图片缩放初始化
      this.zoom();

      // 图片旋转初始化
      this.rotate();

      // 更换图片初始化
      this.changeImg();

      // 关闭图片灯箱点击
      $('#max-img-box .close-img').on('click', () => {
        this.hide();
      });

      // 键盘事件
      $('#max-img-box').on('keydown', ev => {
        // ESC 关闭
        if (ev.keyCode === 27 || ev.key === 'Escape') {
          $('#max-img-box .close-img').click();
        }
        // 右方向键更换图片
        if (ev.keyCode === 39 || ev.key === 'ArrowRight') {
          $('#max-img-box .next-image').click();
        }
        // 左方向键更换图片
        if (ev.keyCode === 37 || ev.key === 'ArrowLeft') {
          $('#max-img-box .previous-image').click();
        }
      });
    });
  }

  /**
   * 获取文章内的图片尺寸和位置
   * @param el
   */
  getImgElSize(el) {
    this.imgElSize.width = el.width();
    this.imgElSize.height = el.height();
    this.imgElSize.left = el.offset().left;
    this.imgElSize.top = el.offset().top;
  }

  /**
   * 显示图片灯箱
   * @param ev event 事件对象
   */
  show(ev) {
    // 获取图片真实尺寸
    this.srcImgSize.width = ev.target.naturalWidth;
    this.srcImgSize.height = ev.target.naturalHeight;
    // 获取当前点击的图片尺寸和位置
    this.getImgElSize($(ev.target));
    // 获取当前点击的图片索引
    this.imgIndex = Number($(ev.target).attr('data-index'));
    // 图片灯箱HTML
    const lightboxHtml = `
    <div id="max-img-box" tabindex="-1" role="dialog" aria-modal="true" aria-labelledby="img-alt" aria-describedby="img-counter">
      <p id="img-counter" aria-live="polite">${this.imgIndex + 1}/${this.imgCount}</p>
      <div class="btn-bar">
        <button type="button" class="btn zoom-in-btn" aria-label="${window.t.zoomIn}" title="${window.t.zoomIn}">
          <i class="icon-zoom-in"></i>
        </button>
        <button type="button" class="btn zoom-out-btn" aria-label="${window.t.zoomOut}" title="${window.t.zoomOut}">
          <i class="icon-zoom-out"></i>
        </button>
        <button type="button" class="btn rotate-left-btn" aria-label="${window.t.rotateLeft}" title="${window.t.rotateLeft}">
          <i class="icon-undo"></i>
        </button>
        <button type="button" class="btn rotate-right-btn" aria-label="${window.t.rotateRight}" title="${window.t.rotateRight}">
          <i class="icon-redo"></i>
        </button>
        <button type="button" class="btn close-img" aria-label="${window.t.closeImage}" title="${window.t.closeImage}">
          <i class="icon-cancel-circle"></i>
        </button>
      </div>
      <a href="javascript:;" aria-label="${window.t.previousImage}" title="${window.t.previousImage}" class="previous-image" role="button">
        <i class="icon-chevron-left"></i>
      </a>
      <a href="javascript:;" aria-label="${window.t.nextImage}" title="${window.t.nextImage}" class="next-image" role="button">
        <i class="icon-chevron-right"></i>
      </a>
      <p id="img-alt" aria-live="polite">${$(ev.target).attr('alt')}</p>
    </div>
    `;
    // 把图片灯箱HTML插入到页面
    $('body').append(lightboxHtml);

    // 创建一张图片
    const imgEl = document.createElement('img');
    imgEl.src = $(ev.target).attr('src');
    imgEl.alt = $(ev.target).attr('alt');
    imgEl.setAttribute('id', 'max-img');
    imgEl.className = 'shadow';
    // 让图片的尺寸和位置和原图保持一致
    imgEl.style.top = `${this.imgElSize.top}px`;
    imgEl.style.left = `${this.imgElSize.left}px`;
    imgEl.style.width = `${this.imgElSize.width}px`;
    imgEl.style.height = `${this.imgElSize.height}px`;
    // 把图片插入到页面
    $('body').append(imgEl);

    // 计算图片灯箱内的图片尺寸
    const {targetHeight, targetWidth} = this.calculateImgSize();
    this.maxImgSize.width = targetWidth;
    this.maxImgSize.height = targetHeight;

    // 把图片移动到页面中心，同时改变大小
    $('#max-img').animate({
      width: targetWidth,
      height: targetHeight,
      top: $(document).scrollTop() + window.innerHeight / 2 - targetHeight / 2,
      left: window.innerWidth / 2 - targetWidth / 2
    }, 250, () => {
      // 移动完成后把图片节点移动到灯箱内
      $('#max-img-box').append($('#max-img'));
      // 重新设置定位
      $('#max-img').css('top', window.innerHeight / 2 - targetHeight / 2);
    });

    // 显示图片灯箱背景
    $('#max-img-box').fadeIn(250);
    // 禁止滚动
    $('body').addClass('stop-scrolling');
    // 聚焦到图片灯箱
    $('#max-img-box').focus();
    // 把图片灯箱状态设置为开启
    this.isShow = true;
    // 开启图片懒加载的情况下，加载文章内的下一张图片
    if (this.imgIndex < this.imgCount - 1) this.loadImg(this.imgIndex + 1);
  }

  /**
   * 重置图片角度
   */
  resetDirection() {
    this.direction = 0;
    $('#max-img').css('transform', `rotate(${this.direction}deg)`);
  }

  /**
   * 关闭图片灯箱
   */
  hide() {
    // 灰度图片角度
    this.resetDirection();
    // 恢复图片的鼠标样式和禁止拖动
    $('#max-img-box #max-img').css('cursor', 'default');
    this.allowMove = false;
    // 淡出图片灯箱背景
    $('#max-img-box').fadeOut(250, () => {
      // 隐藏完成后移除图片灯箱
      $('#max-img-box').remove();
      // 恢复页面滚动条
      $('body').removeClass('stop-scrolling');
    });
    // 获取图片灯箱内的图片top
    const targetTop = $('#max-img').offset().top;
    // 把图片节点移出到 body
    $('body').append($('#max-img'));
    // 重新设置定位
    $('#max-img').css('top',targetTop);
    // 把图片还原到页面中的位置和大小
    $('#max-img').animate({
      width: this.imgElSize.width,
      height: this.imgElSize.height,
      top: this.imgElSize.top,
      left: this.imgElSize.left
    }, 250, () => {
      // 完成后移除图片
      $('#max-img').remove();
    });
    // 把图片灯箱状态设置为关闭
    this.isShow = false;
  }

  /**
   * 图片旋转初始化
   */
  rotate() {
    // 图片左旋转点击
    $('#max-img-box .rotate-left-btn').on('click', () => {
      this.direction -= 90;
      // 设置过渡时间
      $('#max-img').css('transition', '0.25s');
      $('#max-img').css('transform', `rotate(${this.direction}deg)`);
      // 清除过渡
      setTimeout(function () {
        $('#max-img').css('transition', '0s');
      }, 250);
    });

    // 图片右旋转点击
    $('#max-img-box .rotate-right-btn').on('click', () => {
      this.direction += 90;
      // 设置过渡时间
      $('#max-img').css('transition', '0.25s');
      $('#max-img').css('transform', `rotate(${this.direction}deg)`);
      // 清除过渡
      setTimeout(function () {
        $('#max-img').css('transition', '0s');
      }, 250);
    });
  }

  /**
   * 图片缩放初始化
   */
  zoom() {
    // 放大按钮点击
    $('#max-img-box .zoom-in-btn').on('click', () => {
      // 每次放大 20%
      const targetWidth = $('#max-img-box #max-img').width() + this.maxImgSize.width / 5;
      const targetHeight = $('#max-img-box #max-img').height() + this.maxImgSize.height / 5;
      // 放大
      $('#max-img-box #max-img').animate({
        width: targetWidth,
        height: targetHeight
      }, 250, () => {
        // 如果图片超出了可视区
        if (
            $('#max-img-box #max-img').offset().left + $('#max-img-box #max-img').width() >= window.innerWidth ||
            $('#max-img-box #max-img').offset().top + $('#max-img-box #max-img').height() >= $(document).scrollTop() + window.innerHeight
        ) {
          // 把图片的鼠标样式设置为可拖动
          $('#max-img-box #max-img').css('cursor', 'move');
          this.allowMove = true;
        }
      });
    });

    // 缩小按钮点击
    $('#max-img-box .zoom-out-btn').on('click', () => {
      // 如果当前图片尺寸是初始大小就不再缩小
      if (
          $('#max-img-box #max-img').width() === this.maxImgSize.width &&
          $('#max-img-box #max-img').height() === this.maxImgSize.height
      ) return;
      // 还原到初始大小和位置
      $('#max-img-box #max-img').animate({
        width: this.maxImgSize.width,
        height: this.maxImgSize.height,
        top: window.innerHeight / 2 - this.maxImgSize.height / 2,
        left: window.innerWidth / 2 - this.maxImgSize.width / 2
      }, 250, () => {
        // 恢复图片的鼠标样式和禁止拖动
        $('#max-img-box #max-img').css('cursor', 'default');
        this.allowMove = false;
      });
    });
  }

  /**
   * 图片鼠标拖动
   */
  mouseMove() {
    $('#max-img').on('mousedown', ev => {
      ev.preventDefault();
      // 是否允许拖动
      if (!this.allowMove) return false;
      const X = ev.clientX - ev.target.offsetLeft;
      const Y = ev.clientY - ev.target.offsetTop;
      // 开始拖动
      $('#max-img-box').on('mousemove', ev => {
        $('#max-img').css({
          left: ev.clientX - X,
          top: ev.clientY - Y
        });
      });
      // 停止拖动
      $('#max-img-box').on('mouseup', ev => {
        $('#max-img-box').off('mousemove');
      });
    });
  }

  /**
   * 图片手指拖动
   */
  touchMove() {
    $('#max-img').on('touchstart', ev => {
      ev.preventDefault();
      // 是否允许拖动
      if (!this.allowMove) return false;
      const X = ev.touches[0].pageX - ev.target.offsetLeft;
      const Y = ev.touches[0].pageY - ev.target.offsetTop;
      // 开始拖动
      $('#max-img-box').on('touchmove', ev => {
        ev.preventDefault();
        $('#max-img').css({
          left: ev.touches[0].pageX - X,
          top: ev.touches[0].pageY - Y
        });
        return false;
      });
      // 停止拖动
      $('#max-img-box').on('touchend', ev => {
        $('#max-img-box').off('touchmove');
      });
    });
  }

  /**
   * 开启图片懒加载时，提前加载文章内的下一张图片
   * @param index 图片索引
   */
  loadImg(index) {
    const jqueryImgEl = $('.post-content img').eq(index);
    // 如果图片还没有加载
    if (jqueryImgEl.hasClass('load-img')) {
      jqueryImgEl.attr('src', jqueryImgEl.attr('data-src'));
    }
  }

  /**
   * 更换图片初始化
   */
  changeImg() {
    // 更换下一张图片点击
    $('#max-img-box .next-image').on('click', () => {
      // 如果当前是最后一张图片就不再往后切换
      if (this.imgIndex === this.imgCount - 1) return false;
      // 恢复图片的鼠标样式和禁止拖动
      $('#max-img-box #max-img').css('cursor', 'default');
      this.allowMove = false;
      // 重置图片角度
      this.resetDirection();
      // 获取下一张图片的真实尺寸
      this.srcImgSize.width = $('.post-content img').get(this.imgIndex + 1).naturalWidth;
      this.srcImgSize.height = $('.post-content img').get(this.imgIndex + 1).naturalHeight;
      // 重新获取当前显示的图片在文章内的尺寸和位置
      this.getImgElSize($('.post-content img').eq(this.imgIndex + 1));
      // 计算图片灯箱内的新图片的尺寸
      const {targetHeight, targetWidth} = this.calculateImgSize();
      // 添加动画
      $('#max-img').addClass('change-img-animation');
      // 在下一帧执行
      requestAnimationFrame(() => {
        // 设置图片 src 和 alt
        $('#max-img').attr({
          src: $('.post-content img').eq(this.imgIndex + 1).attr('src'),
          alt: $('.post-content img').eq(this.imgIndex + 1).attr('alt')
        });
        $('#max-img').css({
          width: targetWidth,
          height: targetHeight,
          left: window.innerWidth / 2 - targetWidth / 2,
          top: window.innerHeight / 2 - targetHeight / 2
        });
        setTimeout(() => {
          $('#max-img').removeClass('change-img-animation');
        }, 250);
        // 设置图片 alt 文字显示
        $('#img-alt').html($('.post-content img').eq(this.imgIndex + 1).attr('alt'));
        // 当前图片索引+1
        this.imgIndex ++;
        // 重新设置当前图片的序号和总数量
        $('#max-img-box #img-counter').html(`${this.imgIndex + 1}/${this.imgCount}`);
        // 开启图片懒加载的情况下，加载文章内的下一张图片
        if (this.imgIndex < this.imgCount - 1) this.loadImg(this.imgIndex + 1);
      });
    });

    // 更换上一张图片点击
    $('#max-img-box .previous-image').on('click', () => {
      if (this.imgIndex === 0) return false;
      // 恢复图片的鼠标样式和禁止拖动
      $('#max-img-box #max-img').css('cursor', 'default');
      this.allowMove = false;
      // 重置图片角度
      this.resetDirection();
      // 获取上一张图片的真实尺寸
      this.srcImgSize.width = $('.post-content img').get(this.imgIndex - 1).naturalWidth;
      this.srcImgSize.height = $('.post-content img').get(this.imgIndex - 1).naturalHeight;
      // 重新获取当前显示的图片在文章内的尺寸和位置
      this.getImgElSize($('.post-content img').eq(this.imgIndex - 1));
      // 计算图片灯箱内的新图片的尺寸
      const {targetHeight, targetWidth} = this.calculateImgSize();
      // 添加动画
      $('#max-img').addClass('change-img-animation');
      // 在下一帧执行
      requestAnimationFrame(() => {
        // 设置图片 src 和 alt
        $('#max-img').attr({
          src: $('.post-content img').eq(this.imgIndex - 1).attr('src'),
          alt: $('.post-content img').eq(this.imgIndex - 1).attr('alt')
        });
        $('#max-img').css({
          width: targetWidth,
          height: targetHeight,
          left: window.innerWidth / 2 - targetWidth / 2,
          top: window.innerHeight / 2 - targetHeight / 2
        });
        setTimeout(() => {
          $('#max-img').removeClass('change-img-animation');
        }, 250);
        // 设置图片 alt 文字显示
        $('#img-alt').html($('.post-content img').eq(this.imgIndex - 1).attr('alt'));
        // 当前图片索引-1
        this.imgIndex --;
        // 重新设置当前图片的序号和总数量
        $('#max-img-box #img-counter').html(`${this.imgIndex + 1}/${this.imgCount}`);
      });
    });
  }
}