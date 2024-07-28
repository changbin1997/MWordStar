window.addEventListener('load', function() {
  var title = ['外观', '站点信息', '辅助功能', '链接调转', '侧边栏', '文章列表', '文章头图', '文章内容区域', '评论区', '导航', '友情链接', 'PJAX', '开发者'];
  var ul = document.querySelectorAll('form ul');
  var form = document.querySelector('.typecho-page-main form');
  var titleEl = [];
  title.forEach(function (val) {
    var el = document.createElement('h2');
    el.innerHTML = val;
    titleEl.push(el);
  });

  ul.forEach(function (el) {
    //  让屏幕阅读器能够读取复选框和单选框的选项名称
    if (el.children[0].children[1] !== undefined && el.children[0].children[1].tagName === 'SPAN') {
      if (el.children[0].children[0].tagName === 'LABEL') {
        el.setAttribute('aria-label', el.children[0].children[0].innerHTML);
      }
    }
  });

  form.insertBefore(titleEl[0], ul[0]);  //  外观
  form.insertBefore(titleEl[1], ul[4]);  //  站点信息
  form.insertBefore(titleEl[2], ul[7]);  //  辅助功能
  form.insertBefore(titleEl[3], ul[8]);  //  链接跳转
  form.insertBefore(titleEl[4], ul[11]);  //  侧边栏
  form.insertBefore(titleEl[5], ul[25]);  //  文章列表
  form.insertBefore(titleEl[6], ul[27]);  //  文章头图
  form.insertBefore(titleEl[7], ul[31]);  //  文章内容相关
  form.insertBefore(titleEl[8], ul[39]);  //  评论区
  form.insertBefore(titleEl[9], ul[44]);  //  导航栏
  form.insertBefore(titleEl[10], ul[49]);  //  友情链接
  form.insertBefore(titleEl[11], ul[52]);  //  PJAX
  form.insertBefore(titleEl[12], ul[56]);  //  开发者

  var h2Title = document.querySelectorAll('form h2');
  h2Title.forEach(function (el) {
    var hrEl = document.createElement('hr');
    form.insertBefore(hrEl, el);
  });

  //  导出按钮点击
  document.querySelector('#export-btn').onclick = function () {
    var input = document.querySelectorAll('form input');  //  获取所有 input
    var textarea = document.querySelectorAll('form textarea');  //  获取所有 textarea
    var backup = [];  //  主题配置内容
    input.forEach(function (val) {
      //  text 输入框
      if (val.getAttribute('type') === 'text') {
        backup.push({
          name: val.name,
          value: encodeURIComponent(val.value),
          type: val.getAttribute('type')
        });
      }

      //  radio 单选框和
      if (val.getAttribute('type') === 'radio') {
        if (val.checked) {
          backup.push({
            name: val.name,
            value: val.value,
            type: val.getAttribute('type')
          });
        }
      }

      //  checkbox 复选框
      if (val.getAttribute('type') === 'checkbox') {
        backup.push({
          name: val.name,
          value: val.value,
          type: val.getAttribute('type'),
          checked: val.checked
        });
      }
    });

    textarea.forEach(function (val) {
      backup.push({
        name: val.name,
        value: encodeURIComponent(val.value),
        type: val.tagName
      });
    });

    backup = JSON.stringify(backup);
    var blob = new Blob([backup]);
    document.querySelector('#download-file').href = URL.createObjectURL(blob);
    document.querySelector('#download-file').download = 'MWordStar-config.json';
    document.querySelector('#download-file').click();
  };

  //  导入按钮点击
  document.querySelector('#import-btn').onclick = function () {
    document.querySelector('#file-select').click();
  };

  //  文件选择完成
  document.querySelector('#file-select').onchange = function () {
    if (this.value === '') {
      return false;
    }

    var reader = new FileReader();
    reader.readAsText(this.files[0]);
    //  读取完成
    reader.onload = function (ev) {
      var config = JSON.parse(ev.target.result);
      var input = document.querySelectorAll('form input');  //  获取所有 input
      var textarea = document.querySelectorAll('form textarea');  //  获取所有 textarea
      config.forEach(function (val) {
        input.forEach(function (el) {
          //  设置 text 输入框的 value
          if (el.getAttribute('type') === 'text' && el.name === val.name) {
            el.value = decodeURIComponent(val.value);
          }
          //  设置单选框的选中状态
          if (el.getAttribute('type') === 'radio') {
            if (el.name === val.name && el.value === val.value) {
              el.checked = true;
            }
          }
          //  设置复选框的选中状态
          if (el.getAttribute('type') === 'checkbox') {
            if (el.name === val.name && el.value === val.value) {
              el.checked = val.checked;
            }
          }
        });

        textarea.forEach(function (el) {
          if (el.name === val.name && el.tagName === val.type) {
            el.value = decodeURIComponent(val.value);
          }
        });
      });
      if (confirm('主题配置信息已成功导入，您确定要保存设置吗？')) {
        document.querySelector('.typecho-page-main form').submit();
      }
    };

    //  读取文件出错
    reader.onerror = function () {
      alert('读取配置文件时发生错误！');
    };
  };

  // 插入主题配色图片
  var imgBox = document.createElement('div');
  var pEl = document.createElement('p');
  pEl.innerHTML = '配色预览：';
  imgBox.appendChild(pEl);
  var img = document.createElement('div');
  img.setAttribute('role', 'img');
  img.setAttribute('aria-label', '主题配色预览图');
  img.setAttribute('id', 'preview-img');
  imgBox.appendChild(img);
  ul[1].parentNode.insertBefore(imgBox, ul[1]);

  // 获取配色单选框
  var colorRadio = document.getElementsByName('color');
  for (var i = 0;i < colorRadio.length;i ++) {
    // 给配色单选框添加一个索引
    colorRadio[i].index = i;
    // 根据选中的单选框设置图片
    if (colorRadio[i].checked) {
      img.style.backgroundPositionY = '-' + colorRadio[i].index * 313 + 'px';
    }
    // 配色单选框改变
    colorRadio[i].addEventListener('change', function() {
      img.style.backgroundPositionY = '-' + this.index * 313 + 'px';
    });
  }

  // 创建选项列表
  var optionsList = document.querySelector('#options-list');
  var optionsListUl = optionsList.querySelector('ul');
  // 创建选项列表项
  for (var i = 0;i < title.length;i ++) {
    var itemLi = document.createElement('li');
    var itemLink = document.createElement('a');
    itemLink.innerHTML = title[i];
    itemLink.href = 'javascript:;';
    itemLink.index = i;
    // 给目录链接添加跳转事件
    itemLink.addEventListener('click', function() {
      titleEl[this.index].scrollIntoView({behavior: 'smooth'});
    });

    itemLi.appendChild(itemLink);
    optionsListUl.appendChild(itemLi);
  }



  // 设置选项列表目录的位置
  var left = document.querySelector('.col-tb-offset-2').offsetLeft;
  optionsList.style.left = left - optionsList.offsetWidth + 'px';

  // 窗口大小改变时调整选项目录的位置
  window.addEventListener('resize', function() {
    if (window.innerWidth < 768) return false;
    var left = document.querySelector('.col-tb-offset-2').offsetLeft;
    optionsList.style.left = left - optionsList.offsetWidth + 'px';
  });

  // 目录下方的保存选项按钮提交保存
  document.querySelector('.submit-options').addEventListener('click', function() {
    document.querySelector('.typecho-page-main form').submit();
  });
});