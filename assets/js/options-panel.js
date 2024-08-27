window.addEventListener('load', () => {
  const title = [
    '外观', '站点信息', '辅助功能', '链接调转',
    '侧边栏', '文章列表', '文章头图', '文章内容区域',
    '评论区', '导航', '友情链接', 'PJAX', '开发者'
  ];
  const optionUl = document.querySelectorAll('form .typecho-option');  // 列表
  const optionForm = document.querySelector('.typecho-page-main form');  // 选项表单
  const titleEl = [];

  // 生成分组标题
  title.forEach(val => {
    const h2El = document.createElement('h2');
    h2El.innerHTML = val;
    h2El.classList.add('option-title');
    titleEl.push(h2El);
  });

  // 给选项分组列表加入屏幕阅读器专用的标签
  optionUl.forEach(el => {
    if (el.querySelector('label') !== null) {
      el.setAttribute('aria-label', el.querySelector('label').innerText);
    }
  });

  // 插入分组标题
  optionForm.insertBefore(titleEl[0], optionUl[0]);  //  外观
  optionForm.insertBefore(titleEl[1], optionUl[4]);  //  站点信息
  optionForm.insertBefore(titleEl[2], optionUl[7]);  //  辅助功能
  optionForm.insertBefore(titleEl[3], optionUl[8]);  //  链接跳转
  optionForm.insertBefore(titleEl[4], optionUl[11]);  //  侧边栏
  optionForm.insertBefore(titleEl[5], optionUl[25]);  //  文章列表
  optionForm.insertBefore(titleEl[6], optionUl[27]);  //  文章头图
  optionForm.insertBefore(titleEl[7], optionUl[31]);  //  文章内容相关
  optionForm.insertBefore(titleEl[8], optionUl[39]);  //  评论区
  optionForm.insertBefore(titleEl[9], optionUl[44]);  //  导航栏
  optionForm.insertBefore(titleEl[10], optionUl[49]);  //  友情链接
  optionForm.insertBefore(titleEl[11], optionUl[52]);  //  PJAX
  optionForm.insertBefore(titleEl[12], optionUl[56]);  //  开发者

  // 插入分隔线
  titleEl.forEach(el => {
    optionForm.insertBefore(document.createElement('hr'), el);
  });

  //  导出按钮点击
  document.querySelector('#export-btn').onclick = () => {
    const input = optionForm.querySelectorAll('input');  // 获取所有 input
    const textarea = optionForm.querySelectorAll('textarea');  // 获取所有 textarea
    let backup = [];  // 主题配置内容

    // 获取 input 的内容
    input.forEach(el => {
      // 导出 type 为 text 的 input
      if (el.getAttribute('type') === 'text') {
        backup.push({
          name: el.getAttribute('name'),
          value: encodeURIComponent(el.value),
          type: el.getAttribute('type')
        });
      }
      // 导出 radio 的 input
      if (el.getAttribute('type') === 'radio') {
        backup.push({
          name: el.getAttribute('name'),
          value: el.value,
          type: el.getAttribute('type'),
          checked: el.checked
        });
      }
      // 导出 checkbox 的 input
      if (el.getAttribute('type') === 'checkbox') {
        backup.push({
          name: el.getAttribute('name'),
          value: el.value,
          type: el.getAttribute('type'),
          checked: el.checked
        });
      }
    });

    // 获取 textarea 的内容
    textarea.forEach(el => {
      backup.push({
        name: el.getAttribute('name'),
        value: encodeURIComponent(el.value),
        type: el.tagName
      });
    });

    backup = JSON.stringify(backup, null, 2);
    const blob = new Blob([backup]);
    document.querySelector('#download-file').href = URL.createObjectURL(blob);
    document.querySelector('#download-file').download = 'mwordstar-config.json';
    document.querySelector('#download-file').click();
  };

  //  导入按钮点击
  document.querySelector('#import-btn').addEventListener('click', () => {
    document.querySelector('#file-select').click();
  });

  //  文件选择完成
  document.querySelector('#file-select').addEventListener('change', ev => {
    if (ev.target.value === '') {
      return false;
    }

    const reader = new FileReader();
    reader.readAsText(ev.target.files[0]);

    reader.addEventListener('load', readerEv => {
      try {
        const config = JSON.parse(readerEv.target.result);
        const input = optionForm.querySelectorAll('input');  // 获取所有 input
        const textarea = optionForm.querySelectorAll('textarea');  // 获取所有 textarea

        config.forEach(function(val) {
          // 设置 input
          input.forEach(el => {
            // text input
            if (el.getAttribute('type') === 'text' && el.getAttribute('name') === val.name) {
              el.value = decodeURIComponent(val.value);
            }
            // radio input
            if (el.getAttribute('type') === 'radio') {
              if (el.getAttribute('name') === val.name && el.value === val.value) {
                el.checked = val.checked;
              }
            }
            // checkbox input
            if (el.getAttribute('type') === 'checkbox') {
              if (el.getAttribute('name') === val.name && el.value === val.value) {
                el.checked = val.checked;
              }
            }
          });

          // 设置 textarea
          textarea.forEach(el => {
            if (el.getAttribute('name') === val.name && el.tagName === val.type) {
              el.value = decodeURIComponent(val.value);
            }
          });
        });

        if (confirm('主题配置信息已成功导入，您确定要保存设置吗？')) {
          document.querySelector('.typecho-page-main form').submit();
        }
      }catch (error) {
        alert(error.message);
      }
    });

    reader.addEventListener('error', () => {
      alert('读取文件时发生错误！');
    });
  });

  // 插入主题配色图片
  const imgBox = document.createElement('div');
  imgBox.innerHTML = `
  <p>配色预览：</p>
  <div role="img" aria-label="主题配色预览图" id="preview-img"></div>
  `;
  optionUl[1].parentNode.insertBefore(imgBox, optionUl[1]);

  // 获取配色单选框
  const colorRadio = document.getElementsByName('color');
  // 获取预览图
  const img = document.querySelector('#preview-img');
  for (let i = 0; i < colorRadio.length; i++) {
    // 给配色单选框添加一个索引
    colorRadio[i].index = i;
    // 根据选中的单选框设置图片
    if (colorRadio[i].checked) {
      img.style.backgroundPositionY = `-${colorRadio[i].index * 313}px`;
    }
    // 配色单选框改变
    colorRadio[i].addEventListener('change', ev => {
      img.style.backgroundPositionY = `-${ev.target.index * 313}px`;
    });
  }

  // 创建选项列表
  const optionsList = document.querySelector('#options-list');
  // 创建选项列表项
  for (let i = 0;i < title.length;i ++) {
    const itemLi = document.createElement('li');
    const itemLink = document.createElement('a');
    itemLink.innerHTML = title[i];
    itemLink.href = 'javascript:;';
    itemLink.index = i;
    // 给目录链接添加跳转事件
    itemLink.addEventListener('click', ev => {
      titleEl[ev.target.index].scrollIntoView({behavior: 'smooth'});
    });

    itemLi.appendChild(itemLink);
    optionsList.querySelector('ul').appendChild(itemLi);
  }

  // 设置选项列表目录的位置
  const left = document.querySelector('.col-tb-offset-2').offsetLeft;
  optionsList.style.left = left - optionsList.offsetWidth + 'px';

  // 窗口大小改变时调整选项目录的位置
  window.addEventListener('resize', () => {
    if (window.innerWidth < 768) return false;
    const left = document.querySelector('.col-tb-offset-2').offsetLeft;
    optionsList.style.left = left - optionsList.offsetWidth + 'px';
  });

  // 目录下方的保存选项按钮提交保存
  document.querySelector('.submit-options').addEventListener('click', () => {
    document.querySelector('.typecho-page-main form').submit();
  });
});