window.addEventListener('load', () => {
  const title = [
    '语言', '外观', '站点信息', '辅助功能', '链接调转',
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

  // 针对屏幕阅读器的优化
  optionUl.forEach(el => {
    // 给选项分组列表加入屏幕阅读器专用的标签
    if (el.querySelector('label') !== null) {
      el.setAttribute('aria-label', el.querySelector('label').innerText);
    }
    // 给选项表单关联选项描述
    if (el.querySelector('.description') !== null) {
      const descriptionId = `description-${el.getAttribute('id')}`;
      el.querySelector('.description').setAttribute('id', descriptionId);
      el.setAttribute('aria-describedby', descriptionId);
    }
  });

  // 插入分组标题
  optionForm.insertBefore(titleEl[0], optionUl[0]);  // 语言
  optionForm.insertBefore(titleEl[1], optionUl[1]);  // 外观
  optionForm.insertBefore(titleEl[2], optionUl[5]);  // 站点信息
  optionForm.insertBefore(titleEl[3], optionUl[8]);  // 辅助功能
  optionForm.insertBefore(titleEl[4], optionUl[9]);  // 链接跳转
  optionForm.insertBefore(titleEl[5], optionUl[12]);  // 侧边栏
  optionForm.insertBefore(titleEl[6], optionUl[26]);  // 文章列表
  optionForm.insertBefore(titleEl[7], optionUl[28]);  // 文章头图
  optionForm.insertBefore(titleEl[8], optionUl[32]);  // 文章内容相关
  optionForm.insertBefore(titleEl[9], optionUl[40]);  // 评论区
  optionForm.insertBefore(titleEl[10], optionUl[45]);  // 导航栏
  optionForm.insertBefore(titleEl[11], optionUl[50]);  // 友情链接
  optionForm.insertBefore(titleEl[12], optionUl[53]);  // PJAX
  optionForm.insertBefore(titleEl[13], optionUl[57]);  // 开发者

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


  // 获取友情链接表格
  const linkList = document.querySelector('#link-list table tbody');
  let linkInputName = null;  // 存储友链表单的名称

  // 打开链接编辑器
  document.querySelectorAll('.show-link-editor').forEach(el => {
    el.setAttribute('title', `${el.getAttribute('data-title')}编辑`);

    el.addEventListener('click', ev => {
      // 设置链接编辑器标题
      document.querySelector('#link-editor-title').innerHTML = `${ev.target.getAttribute('data-title')}编辑`;
      // 从链接表单获取 JSON
      linkInputName = ev.target.getAttribute('data-name');
      let linkJson = document.querySelector(`textarea[name="${linkInputName}"]`).value;

      // 显示链接编辑器
      document.querySelector('#link-editor-box').style.display = 'block';
      // 如果屏幕尺寸较小就调节编辑器尺寸
      if (window.innerWidth < 900 || window.innerHeight < 500) {
        const linkListHeight = window.innerHeight - document.querySelector('#link-editor-title').offsetHeight - document.querySelector('#link-editor .dialog-actions').offsetHeight;
        document.querySelector('#link-list').style.height = linkListHeight + 'px';
      }
      // 禁止页面滚动
      document.body.classList.add('no-scroll');
      // 清空链接表格
      linkList.innerHTML = '';
      // 读取链接
      if (linkJson !== '') {
        try {
          linkJson = JSON.parse(linkJson);
        }catch (error) {
          alert(`无法解析链接 JSON ${error.message}`);
          return false;
        }

        // 生成链接表格
        linkJson.forEach(link => {
          if (link.url === undefined) link.url = '';
          if (link.name === undefined) link.name = '';
          if (link.title === undefined) link.title = '';
          if (link.logoUrl === undefined) link.logoUrl = '';

          const trEl = document.createElement('tr');
          trEl.innerHTML = `
          <td class="url-td">${link.url}</td>
          <td class="name-td">${link.name}</td>
          <td class="logo-url-td">${link.logoUrl}</td>
          <td class="title-td">${link.title}</td>
          <td>
            <a href="javascript:;" class="edit-item">编辑</a>  
            <a href="javascript:;" class="remove-item">删除</a>
          </td>
          `;
          linkList.appendChild(trEl);
        });
      }

      // 让第一个可聚焦的元素获取焦点
      const dialog = document.querySelector('#link-editor');
      const focusableElements = dialog.querySelectorAll('a, button, input');
      focusableElements[0].focus();
    });
  });

  //使用事件委托的方式给友链表格内的按钮添加事件
  linkList.addEventListener('click', ev => {
    // 删除链接点击
    if (ev.target && ev.target.classList.contains('remove-item')) {
      ev.target.closest('tr').remove();
    }

    // 保存编辑链接点击
    if (ev.target && ev.target.classList.contains('save-item')) {
      // 获取编辑的链接
      const link = {
        url: linkList.querySelector('.editing .url-td input').value,
        name: linkList.querySelector('.editing .name-td input').value,
        title: linkList.querySelector('.editing .title-td input').value,
        logoUrl: linkList.querySelector('.editing .logo-url-td input').value
      };
      // 检查必填项是否为空
      if (link.url === '' || link.name === '') {
        alert('链接 URL 或名称不能为空！');
        return false;
      }
      // 生成表格
      const trEl = document.createElement('tr');
      trEl.innerHTML = `
        <td class="url-td">${link.url}</td>
        <td class="name-td">${link.name}</td>
        <td class="logo-url-td">${link.logoUrl}</td>
        <td class="title-td">${link.title}</td>
        <td>
          <a href="javascript:;" class="edit-item">编辑</a>  
          <a href="javascript:;" class="remove-item">删除</a>
        </td>
      `;
      // 把表格插入到编辑项后面
      linkList.querySelector('.editing').insertAdjacentElement('afterend', trEl);
      // 删除编辑项
      linkList.querySelector('.editing').remove();
    }

    // 编辑链接点击
    if (ev.target && ev.target.classList.contains('edit-item')) {
      // 获取编辑内容
      const link = {
        url: ev.target.closest('tr').querySelector('.url-td').innerHTML,
        name: ev.target.closest('tr').querySelector('.name-td').innerHTML,
        logoUrl: ev.target.closest('tr').querySelector('.logo-url-td').innerHTML,
        title: ev.target.closest('tr').querySelector('.title-td').innerHTML
      };
      // 生成编辑表格
      const trEl = document.createElement('tr');
      trEl.classList.add('editing');
      trEl.innerHTML = `
      <td class="url-td"><input type="text" value="${link.url}" placeholder="链接地址"></td>
      <td class="name-td"><input type="text" value="${link.name}" placeholder="链接名称"></td>
      <td class="logo-url-td"><input type="text" value="${link.logoUrl}" placeholder="网站Logo"></td>
      <td class="title-td"><input type="text" value="${link.title}" placeholder="网站简介"></td>
      <td>
        <a href="javascript:;" class="save-item" title="保存当前编辑的行">保存</a>
        <a href="javascript:;" class="remove-item" title="删除当前编辑的行">取消</a>
      </td>
      `;
      // 插入编辑表格
      ev.target.closest('tr').insertAdjacentElement('afterend', trEl);
      // 删除之前的链接表格
      ev.target.closest('tr').remove();
      // 聚焦到第一个编辑表单
      linkList.querySelector('.editing input').focus();
    }
  });

  // 添加链接按钮点击
  document.querySelector('.add-link').addEventListener('click', () => {
    // 是否有正在编辑的行
    if (linkList.querySelectorAll('.editing').length) {
      alert('当前表格中有未完成的编辑！');
      return false;
    }

    // 生成编辑表格
    const trEl = document.createElement('tr');
    trEl.classList.add('editing');
    trEl.innerHTML = `
    <td class="url-td"><input type="text" placeholder="链接地址"></td>
    <td class="name-td"><input type="text" placeholder="链接名称"></td>
    <td class="logo-url-td"><input type="text" placeholder="网站Logo"></td>
    <td class="title-td"><input type="text" placeholder="网站简介"></td>
    <td>
      <a href="javascript:;" class="save-item" title="保存当前编辑的行">保存</a>
      <a href="javascript:;" class="remove-item" title="删除当前编辑的行">取消</a>
    </td>
    `;
    // 插入到表格
    linkList.appendChild(trEl);
    // 聚焦到第一个编辑表单
    linkList.querySelector('.editing input').focus();
  });

  // 关闭链接编辑器
  document.querySelector('#link-editor-box .close-btn').addEventListener('click', () => {
    document.querySelector('#link-editor-box').style.display = 'none';
    // 恢复页面滚动
    document.body.classList.remove('no-scroll');
    // 聚焦到打开对话框的按钮
    document.querySelector(`button[data-name="${linkInputName}"]`).focus();
  });

  // 链接编辑器的确定按钮点击
  document.querySelector('#link-editor-box .save-btn').addEventListener('click', () => {
    // 是否有未完成的编辑
    if (linkList.querySelectorAll('.editing').length) {
      alert('发现了未完成的编辑，您需要先删除或保存未完成的编辑！');
      return false;
    }
    // 获取链接表单
    const linkInput = document.querySelector(`textarea[name="${linkInputName}"]`);
    // 关闭链接编辑器
    document.querySelector('#link-editor-box .close-btn').click();
    // 如果链接全部删除
    if (linkList.querySelectorAll('tr').length < 1) {
      linkInput.value = '';
      return true;
    }

    const links = [];  // 存储链接
    // 从链接表格获取链接
    linkList.querySelectorAll('tr').forEach(el => {
      const linkItem = {
        url: el.querySelector('.url-td').innerHTML,
        name: el.querySelector('.name-td').innerHTML
      };
      if (el.querySelector('.logo-url-td').innerHTML !== '') {
        linkItem.logoUrl = el.querySelector('.logo-url-td').innerHTML;
      }
      if (el.querySelector('.title-td').innerHTML !== '') {
        linkItem.title = el.querySelector('.title-td').innerHTML;
      }
      links.push(linkItem);
    });
    // 把链接 JSON 填写到对应的友链表单
    linkInput.value = JSON.stringify(links, null, 2);
    // 对话框提示
    const linkType = document.querySelector(`button[data-name="${linkInputName}"]`).getAttribute('data-title');
    if (confirm(`您编辑的链接已添加到 ${linkType}，您要保存设置吗？`)) {
      document.querySelector('.typecho-page-main form').submit();
    }
  });

  // 链接编辑器的确定按钮按下TAB
  document.querySelector('#link-editor-box .save-btn').addEventListener('keydown', ev => {
    if (ev.key === 'Tab') {
      ev.preventDefault();
      // 获取对话框内可聚焦的元素
      const dialog = document.querySelector('#link-editor');
      const focusableElements = dialog.querySelectorAll('a, button, input');
      // 让第一个可聚焦的元素获取焦点
      focusableElements[0].focus();
    }
  });
});