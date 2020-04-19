window.onload = function (ev) {
    var title = ['外观', '站点信息', '辅助功能', '侧边栏', '文章头图', '文章内容区域', '评论区', '导航栏', '友情链接', '开发者'];
    var ul = document.querySelectorAll('form ul');
    var form = document.querySelector('.typecho-page-main form');
    var titleEl = [];
    title.forEach(function (val) {
        var el = document.createElement('h2');
        el.innerHTML = val;
        titleEl.push(el);
    });

    form.insertBefore(titleEl[0], ul[0]);  //  外观
    form.insertBefore(titleEl[1], ul[2]);  //  站点信息
    form.insertBefore(titleEl[2], ul[5]);  //  辅助功能
    form.insertBefore(titleEl[3], ul[6]);  //  侧边栏
    form.insertBefore(titleEl[4], ul[13]);  //  文章头图
    form.insertBefore(titleEl[5], ul[15]);  //  文章内容相关
    form.insertBefore(titleEl[6], ul[18]);  //  评论区
    form.insertBefore(titleEl[7], ul[20]);  //  导航栏
    form.insertBefore(titleEl[8], ul[21]);  //  友情链接
    form.insertBefore(titleEl[9], ul[24]);  //  开发者
};