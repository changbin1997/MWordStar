/**
 * GitHub 仓库列表展示模块
 * 负责从 GitHub API 异步拉取特定用户的公开仓库，并渲染到页面上
 */
export default class GithubRepositoryShowcase {
  /**
   * 仓库列表总页数
   * @type {number}
   */
  pages = 0;

  /**
   * 当前需要加载的页码
   * @type {number}
   */
  pageNum = 1;

  /**
   * GitHub 用户名
   * @type {string}
   */
  githubUserName = '';

  /**
   * 模块初始化方法
   * 绑定事件并触发第一次数据加载
   * @returns {boolean|void} 如果当前页面没有仓库容器，则直接返回 false
   */
  init() {
    // 如果不是 github 仓库展示页面就直接返回
    if ($('.github-page').length < 1) return false;
    // 如果没有填写 github 用户名就直接返回
    if ($('#github-username').length < 1) return false;

    // 缓存 jQuery DOM 节点，避免后续频繁查询 DOM 树，提升性能
    this.$repoList = $('#repository-list');
    this.$loadMoreBtn = $('.load-more-repository-btn');

    // 重置总页数和页码
    this.pages = 0;
    this.pageNum = 1;
    // 获取 github 用户名
    this.githubUserName = $('#github-username').attr('data-user');

    // 加载更多的按钮点击
    this.$loadMoreBtn.on('click', () => {
      this.load();
    });

    // 加载第一页的数据
    this.load();
  }

  /**
   * 执行 Ajax 请求加载数据并渲染 DOM
   */
  load() {
    // 查询参数
    const query = {
      sort: 'updated',
      per_page: 20,
      page: this.pageNum
    };
    const url = `https://api.github.com/users/${this.githubUserName}/repos`;

    // 把加载更多的按钮标题设置为正在加载和禁用按钮
    this.$loadMoreBtn.html(window.t.loading);
    this.$loadMoreBtn.prop('disabled', true);

    $.ajax({
      type: 'get',
      url: url,
      data: query,
      async: true,
      dataType: 'json',
      timeout: 20000,
      success: (data, textStatus, jqXHR) => {
        // 解析总页数：GitHub 通过响应头 Link 传递分页信息，仅在首次请求时解析
        if (this.pages === 0) {
          const linkHeader = jqXHR.getResponseHeader('link');
          if (linkHeader) {
            // 通过正则匹配 rel="last" 中的 page 参数值
            const match = linkHeader.match(/<[^>]+[?&]page=(\d+)[^>]*>;\s*rel="last"/);
            this.pages = match ? parseInt(match[1], 10) : this.pageNum;
          } else {
            // 如果没有 link 响应头，说明数据很少，只有一页
            this.pages = 1;
          }
        }

        let repositoryListHtml = ''; // 存储 github 仓库列表

        data.forEach(item => {
          // 防止 API 返回 null 时在页面上显示 "null"
          const description = item.description || window.t.noDescription;
          const language = item.language || window.t.unknown;

          // 生成 github 仓库列表
          repositoryListHtml += `
          <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12 repository-item mb-3">
            <div class="card">
              <div class="card-body">
                <h4 class="card-title">
                  <a href="${item.html_url}" target="_blank">${item.name}</a>
                </h4>
                <p class="card-text my-2" title="${description}">${description}</p>
              </div>
              <div class="card-footer text-muted">
                <span class="mr-2">${item.stargazers_count} stars</span>
                <span class="mr-2">${item.forks_count} forks</span>
                <span>${language}</span>
              </div>
            </div>
          </div>
          `;
        });

        // 把列表插入到页面
        this.$repoList.append(repositoryListHtml);

        // 每次成功查询完成后页码 +1
        this.pageNum += 1;
      },
      error: (xhr, error, abnormal) => {
        // 只有第一次加载出错才插入错误信息
        if ($('.repository-item').length) return false;

        // 获取状态码
        const httpCode = xhr.status === 0 ? '' : `${xhr.status} `;

        // 插入错误信息
        const errorMessageHtml = `
        <div class="col-12">
          <div class="alert mb-0" role="alert">${httpCode}${xhr.statusText || '请求出错'}</div>
        </div>
        `;
        this.$repoList.append(errorMessageHtml);
      },
      complete: () => {
        const $loadingAnim = $('.loading-animation');
        // 移除加载动画 (首屏显示，加载一次后废弃)
        if ($loadingAnim.length) {
          $loadingAnim.remove();
        }

        // 恢复按钮文字和状态
        this.$loadMoreBtn.html(window.t.loadMore);
        this.$loadMoreBtn.prop('disabled', false);

        // 检查当前是否是最后一页 (如果当前准备请求的页码大于总页数，说明没数据了)
        if (this.pageNum > this.pages) {
          this.$loadMoreBtn.hide();
        } else {
          this.$loadMoreBtn.show();
        }
      }
    });
  }
}