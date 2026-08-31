const CACHE_NAME = 'mwordstar-assets-v1';

// 带构建时间戳的文件（Webpack 打包生成，每次构建时间戳都会变化）
const BUILD_FILE = /(bundle-\d+\.js|style-\d+\.css)$/;

// 安装阶段：立即跳过等待，让新 SW 尽快准备就绪
self.addEventListener('install', (event) => {
  self.skipWaiting();
});

// 激活阶段：立即接管页面控制权
self.addEventListener('activate', (event) => {
  event.waitUntil(self.clients.claim());
});

// 拦截资源请求
self.addEventListener('fetch', (event) => {
  const requestUrl = new URL(event.request.url);

  // 策略 A：处理带时间戳的 Webpack 构建文件 (bundle-\d+.js 和 style-\d+.css)
  if (BUILD_FILE.test(requestUrl.pathname)) {
    event.respondWith(
      caches.open(CACHE_NAME).then(async (cache) => {
        // 先查本地是否有当前时间戳的构建文件
        const cachedResponse = await cache.match(event.request);
        if (cachedResponse) {
          return cachedResponse; // 命中缓存，秒级加载
        }

        // 本地没有，说明重新打包更新了！先自动删除旧版构建文件的缓存
        const keys = await cache.keys();
        await Promise.all(
          keys.map((key) => {
            if (BUILD_FILE.test(new URL(key.url).pathname)) {
              return cache.delete(key);
            }
          })
        );

        // 从网络请求最新的构建文件并存入本地缓存
        const networkResponse = await fetch(event.request);
        if (networkResponse && networkResponse.status === 200) {
          cache.put(event.request, networkResponse.clone());
        }
        return networkResponse;
      })
    );
    return;
  }

  // 策略 B：处理几乎不变的 highlight.pack.js
  if (requestUrl.pathname.endsWith('highlight.pack.js')) {
    event.respondWith(
      caches.open(CACHE_NAME).then(async (cache) => {
        const cachedResponse = await cache.match(event.request);
        if (cachedResponse) {
          return cachedResponse; // 命中缓存，直接返回
        }

        // 首次加载（或代码块首次出现时），走网络获取并写入缓存
        const networkResponse = await fetch(event.request);
        if (networkResponse && networkResponse.status === 200) {
          cache.put(event.request, networkResponse.clone());
        }
        return networkResponse;
      })
    );
    return;
  }
});
