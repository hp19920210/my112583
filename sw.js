// Service Worker

// 缓存的名称和版本，当你想更新缓存时，只需修改版本号
const CACHE_NAME = 'site-directory-cache-v1';

// 定义需要预先缓存的核心文件列表
const URLS_TO_CACHE = [
    '/', // 网站根目录，通常会映射到 index.php
    'index.php',
    'assets/sites-style.css',
    'assets/icons/icon-192x192.png',
    'assets/icons/icon-512x512.png',
    'manifest.json'
];

// 1. 安装事件 (install)
// 当 Service Worker 首次被注册时触发
self.addEventListener('install', event => {
    console.log('Service Worker: Installing...');
    // event.waitUntil() 确保在所有核心文件都被缓存之前，install 事件不会结束
    event.waitUntil(
        caches.open(CACHE_NAME) // 打开我们定义的缓存
            .then(cache => {
                console.log('Service Worker: Caching app shell');
                return cache.addAll(URLS_TO_CACHE); // 将所有核心文件添加到缓存中
            })
            .then(() => self.skipWaiting()) // 强制新的Service Worker立即激活
    );
});

// 2. 激活事件 (activate)
// 当新的 Service Worker 激活时触发，通常用于清理旧缓存
self.addEventListener('activate', event => {
    console.log('Service Worker: Activating...');
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames.map(cacheName => {
                    // 如果缓存名称不是当前版本，就删除它
                    if (cacheName !== CACHE_NAME) {
                        console.log('Service Worker: Clearing old cache:', cacheName);
                        return caches.delete(cacheName);
                    }
                })
            );
        })
        .then(() => self.clients.claim()) // 让新的Service Worker立即控制所有页面
    );
});

// 3. 拦截网络请求事件 (fetch)
// 页面发出的任何网络请求都会在这里被拦截
self.addEventListener('fetch', event => {
    // 我们采用“缓存优先，网络回退”的策略
    event.respondWith(
        caches.match(event.request) // 首先在缓存中查找是否有匹配的请求
            .then(response => {
                if (response) {
                    // 如果缓存中有匹配的响应，直接返回它
                    // console.log('Service Worker: Serving from cache:', event.request.url);
                    return response;
                }
                // 如果缓存中没有，则通过网络去请求
                // console.log('Service Worker: Fetching from network:', event.request.url);
                return fetch(event.request).then(
                    // （可选）我们也可以在这里将新的请求缓存起来，但为了简单起见，我们暂时只缓存核心文件
                );
            })
    );
});