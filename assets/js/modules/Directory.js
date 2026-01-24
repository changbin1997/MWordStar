/*!
* HomePage: https://www.misterma.com
* GithubPage: https://github.com/changbin1997
* ProjectPage: https://github.com/changbin1997/MWordStar
* author: Changbin (changbin1997)
* Licensed under MIT
*/

export default class Directory {
  isShow = false;  // 是否打开移动设备的目录
  directoryTop = 0;  // 侧边栏章节目录的高度

  /**
   * 目录功能初始化
   */
  init() {
    // 章节目录跳转初始化
    this.directoryStyleInit();
    // 侧边栏目录高亮初始化
    this.directoryHighlightInit();
    // 调整侧边栏目录的尺寸
    this.directorySize();
  }

  /**
   * 固定侧边栏目录的位置，页面滚动时调用
   */
  directoryPosition() {
    if ($('.sidebar .directory').length && window.innerWidth >= 992) {
      if ($(document).scrollTop() >= this.directoryTop) {
        $('.sidebar .directory').css({
          position: 'fixed',
          top: 80
        });
      }else {
        $('.sidebar .directory').css('position', 'static');
      }
    }
  }

  /**
   * 移动设备目录开关按钮初始化，PJAX 替换完成后调用
   */
  directoryBtnInit() {
    // 如果开启了移动设备目录并且目录按钮不存在就插入一个按钮
    if ($('#directory-mobile').length && $('#directory-btn').length < 1) {
      const directoryBtn = `
      <button
      type="button"
      id="directory-btn"
      class="btn rounded-circle d-block d-sm-block d-md-block d-lg-none d-xl-none"
      aria-expanded="false"
      aria-label="${window.t.tableOfContents}" title="${window.t.tableOfContents}"
      >
        <i class="icon-list-ol"></i>
      </button>
      `;
      $('#footer-btn-box').prepend(directoryBtn);
      // 重置目录状态
      if (this.isShow) this.isShow = false;
    }

    // 如果移动设备目录按钮存在但文章目录不存在就删除按钮
    if ($('#directory-btn').length && $('#directory-mobile').length < 1) {
      $('#directory-btn').remove();
    }
  }

  /**
   * 调整侧边栏目录的位置
   */
  directorySize() {
    if ($('.sidebar .directory').length) {
      // 获取侧边栏章节目录的位置
      this.directoryTop = $('.sidebar .directory').offset().top;
      $('.sidebar .directory').css('width', $('.sidebar .reference-line').innerWidth());
      $('.sidebar .directory > .article-directory').css({
        'max-height': window.innerHeight - 180
      });
    }
  }

  /**
   * 章节目录跳转初始化
   */
  directoryStyleInit() {
    // 目录链接点击
    $('.directory-link').on('click', ev => {
      const titleSelect = `[data-title="${$(ev.target).closest('a').attr('data-directory')}"]`;
      $('html').animate({
        scrollTop: $(titleSelect).offset().top - 60
      }, 400);
      return false;
    });

    // 如果开启了移动设备文章目录就给目录添加事件
    if ($('#directory-mobile').length) {
      // 移动设备目录按钮点击
      $('#directory-btn').on('click',  () => {
        if (!this.isShow) {
          $('#directory-mobile').css('display', 'flex');
          $('#directory-mobile').animate({opacity: 1}, 250);
          this.isShow = true;
        }else {
          $('#directory-mobile').animate({opacity: 0}, 250, () => {
            $('#directory-mobile').hide();
          });
          this.isShow = false;
        }
        $('#directory-btn').attr('aria-expanded', this.isShow);
      });

      // 移动设备的关闭目录按钮点击
      $('#directory-mobile .close-btn').on('click', () => {
        $('#directory-btn').click();
      });
    }
  }

  /**
   * 侧边栏目录高亮初始化
   */
  directoryHighlightInit() {
    // 定义常量和进行一次性元素选择 ---
    const MOBILE_BREAKPOINT = 992; // Bootstrap 4 的 'lg' 断点，小于此值为移动端
    const $targets = $('.title-position');

    // 明确选择桌面端和移动端的链接，并缓存
    const $desktopLinks = $('.sidebar .directory-link');
    const $mobileLinks = $('#directory-mobile .directory-link');

    // 为两套链接分别构建高效的查找 Map
    const desktopLinkMap = new Map();
    $desktopLinks.each(function() {
      desktopLinkMap.set($(this).attr('href'), $(this));
    });

    const mobileLinkMap = new Map();
    $mobileLinks.each(function() {
      mobileLinkMap.set($(this).attr('href'), $(this));
    });

    // 如果页面上没有任何标题，直接退出
    if (!$targets.length) {
      return;
    }

    // 计算动态偏移量 ---
    const $stickyHeader = $('.sticky-top');
    const headerHeight = $stickyHeader.length ? $stickyHeader.outerHeight() : 0;
    const activationOffset = headerHeight + 30;

    // 核心更新函数
    const updateActiveLink = (linksToUpdate, linkMap) => {
      // 如果被告知要更新的链接集不存在，则不执行
      if (!linksToUpdate || !linksToUpdate.length) {
        return;
      }

      const scrollTop = $(window).scrollTop();
      const activationPoint = scrollTop + activationOffset;
      let activeTargetId = null;

      $targets.each(function() {
        if ($(this).offset().top < activationPoint) {
          activeTargetId = `#${$(this).attr('id')}`;
        } else {
          return false;
        }
      });

      const windowHeight = $(window).height();
      const docHeight = $(document).height();
      if (scrollTop + windowHeight >= docHeight - 5) {
        const $lastTarget = $targets.last();
        if ($lastTarget.length) {
          activeTargetId = `#${$lastTarget.attr('id')}`;
        }
      }

      // 更新 Class
      linksToUpdate.removeClass('directory-active');
      if (activeTargetId) {
        const $activeLink = linkMap.get(activeTargetId);
        if ($activeLink) {
          $activeLink.addClass('directory-active');
        }
      }
    };

    // 这个函数负责判断环境并调用执行者
    const scrollspyController = () => {
      // 清理工作：在判断前，先移除所有链接的高亮，以防窗口切换时残留
      $desktopLinks.removeClass('directory-active');
      $mobileLinks.removeClass('directory-active');

      // 根据窗口宽度决定使用哪一套链接和 Map
      if (window.innerWidth >= MOBILE_BREAKPOINT) {
        // 桌面模式
        updateActiveLink($desktopLinks, desktopLinkMap);
      } else {
        // 移动模式
        updateActiveLink($mobileLinks, mobileLinkMap);
      }
    };

    // 辅助函数：防抖和节流 ---
    const debounce = (func, delay) => {
      let timeout;
      return (...args) => {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, args), delay);
      };
    };
    const throttle = (func, delay) => {
      let inProgress = false;
      return (...args) => {
        if (inProgress) return;
        inProgress = true;
        setTimeout(() => {
          func.apply(this, args);
          inProgress = false;
        }, delay);
      };
    };

    const throttledController = throttle(scrollspyController, 100);
    const debouncedController = debounce(scrollspyController, 250);

    $(window).on('scroll', throttledController);
    $(window).on('resize', debouncedController);

    // 页面加载完成后，立即执行一次“总指挥”，以设置正确的初始状态
    scrollspyController();
  }
}