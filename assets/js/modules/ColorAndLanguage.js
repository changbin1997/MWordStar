/*!
* HomePage: https://www.misterma.com
* GithubPage: https://github.com/changbin1997
* ProjectPage: https://github.com/changbin1997/MWordStar
* author: Changbin (changbin1997)
* Licensed under MIT
*/

export default class ColorAndLanguage {
  /**
   * 语言和配色初始化
   */
  init() {
    // 导航栏的语言切换点击
    $('header .change-language').on('click', ev => {
      this.changeLanguage(ev);
    });

    // 侧边栏语言切换初始化
    this.sidebarChangeLanguageInit();

    // 切换主题配色初始化
    this.changeColorInit();

    // 配色切换按钮初始化
    this.changeColorBtnInit();
  }

  /**
   * 侧边栏的语言切换初始化（侧边栏内容会被 PJAX 替换，可能需要调用多次，所以单独拆分出来）
   */
  sidebarChangeLanguageInit() {
    // 侧边栏的语言切换单选改变
    $('.sidebar .change-language').on('change', ev => {
      this.changeLanguage(ev);
    });
  }

  /**
   * 更改语言
   * @param ev 事件 event
   */
  changeLanguage(ev) {
    const language = $(ev.target).attr('data-language');
    // 获取当前的时间戳
    let time = Date.parse(new Date());
    // 在当前的时间戳上 + 180天
    time += 15552000000;
    time = new Date(time);
    // 写入 cookie
    document.cookie = `language=${language};path=/;expires=Tue,${time}`;
    location.reload();
  }

  /**
   * 切换主题配色初始化
   */
  changeColorInit() {
    // 切换主题配色按钮点击
    $('#change-color-btn').on('click', ev => {
      let color = '';  // 颜色
      if ($('.dark-color').length) {
        // 切换为浅色
        $('body').removeClass('dark-color');
        $('body').addClass($(ev.target).closest('button').attr('data-light'));
        $('body').attr('data-color', $(ev.target).closest('button').attr('data-light'));
        color = $(ev.target).closest('button').attr('data-light');
        // 更改代码块配色
        if ($('body').attr('data-code-theme') === 'auto') {
          $('body').removeClass('github-dark').addClass('stackoverflow-light');
        }
      }else {
        // 切换为深色
        $('body').removeClass($('body').attr('data-color'));
        $('body').addClass('dark-color');
        $('body').attr('data-color', 'dark-color');
        color = 'dark-color';
        // 更改代码块配色
        if ($('body').attr('data-code-theme') === 'auto') {
          $('body').removeClass('stackoverflow-light').addClass('github-dark');
        }
      }
      // 重新初始化配色切换按钮
      this.changeColorBtnInit();
      // 重新初始化配色切换按钮的 bootstrap 的工具提示
      $('#change-color-btn').tooltip('show');

      // 获取当前时间戳
      let time = Date.parse(new Date());
      // 在当前的时间戳上 + 180天
      time += 15552000000;
      time = new Date(time);
      // 写入 cookie
      document.cookie = `themeColor=${color};path=/;expires=Tue,${time}`;
    });

    // 切换主题配色按钮键盘按下回车
    $('#change-color-btn').on('keypress', ev => {
      if (ev.keyCode === 13) $('#change-color-btn').click();
    });
  }

  /**
   * 根据使用的主题配色来设置配色切换按钮的提示
   */
  changeColorBtnInit() {
    if ($('.dark-color').length) {
      $('#change-color-btn i').removeClass('icon-sun');
      $('#change-color-btn i').addClass('icon-moon');
      $('#change-color-btn').attr('title', window.t.switchToLightMode);
    }else {
      $('#change-color-btn i').removeClass('icon-moon');
      $('#change-color-btn i').addClass('icon-sun');
      $('#change-color-btn').attr('title', window.t.switchToDarkMode);
    }
    $('#change-color-btn').attr('data-original-title', $('#change-color-btn').attr('title'));
    $('#change-color-btn').tooltip('update');
  }
}