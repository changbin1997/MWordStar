/*!
* HomePage: https://www.misterma.com
* GithubPage: https://github.com/changbin1997
* ProjectPage: https://github.com/changbin1997/MWordStar
* author: Changbin (changbin1997)
* Licensed under MIT
*/

export default class AvatarStyle {
  avatarColor = [];  // 存储文字头像颜色
  avatarName = [];  // 存储文字头像名称

  /**
   * 初始化
   */
  init() {
    // 头像图片加载完成后删除图片背景
    $('.avatar').on('load', ev => {
      $(ev.target).css('background', 'none');
    });

    // 给评论者头像添加错误事件
    for (let i = 0;i < $('.avatar').length;i ++) {
      // 检测是否是图片
      if ($('.avatar').eq(i)[0].tagName === 'IMG') {
        $('.avatar').eq(i).on('error', ev => {
          // 获取头像昵称
          const name = $(ev.target).attr('alt');
          // 创建文字头像元素
          const avatarEl = document.createElement('div');
          avatarEl.setAttribute('role', 'img');
          avatarEl.setAttribute('aria-label', name);
          // 设置文字头像的 class
          avatarEl.className = 'pingback avatar';
          // 把文字头像的内容设置为评论者昵称的第一个字
          avatarEl.innerHTML = name.substring(0, 1);

          // 检测是否重复出现
          const nameIndex = this.avatarName.indexOf(name);
          if (nameIndex === -1) {
            this.avatarName.push(name);
            // 生成随机颜色
            const bgColor = {r: this.rand(250, 1), g: this.rand(250, 1), b: this.rand(250, 1)};
            // 把颜色添加到数组，遇到同名的头像可以使用同一组颜色
            this.avatarColor.push(bgColor);
            // 设置文字头像的背景颜色
            avatarEl.style.background = `rgb(${bgColor.r}, ${bgColor.g}, ${bgColor.b})`;
          }else {
            // 设置文字头像的背景颜色
            avatarEl.style.background = `rgb(${this.avatarColor[nameIndex].r}, ${this.avatarColor[nameIndex].g}, ${this.avatarColor[nameIndex].b})`;
          }

          // 把文字头像插入到页面
          $(ev.target).before(avatarEl);
          // 移除加载失败的头像
          $(ev.target).remove();
        });
      }
    }

    // 独立页友链 Logo 加载完成
    $('.link-box .link img').on('load', ev => {
      // 去除背景颜色
      $(ev.target).css('background', 'none');
    });

    // 独立页 Logo 加载出错
    $('.link-box .link img').on('error', ev => {
      // 默认站点 Logo
      const logoEl = '<i class="link-logo float-left icon-link icon-logo rounded-circle" aria-label="站点Logo" role="img"></i>';
      // 把默认 Logo 插入到页面
      $(ev.target).before(logoEl);
      // 移除加载失败的 Logo
      $(ev.target).remove();
    });
  }

  /**
   * 生成随机数
   * @param {Number} max 最大值
   * @param {Number} min 最小值
   * @returns {number} 返回随机数
   */
  rand(max, min) {
    const num = max - min;
    return Math.round(Math.random() * num + min);
  }
}