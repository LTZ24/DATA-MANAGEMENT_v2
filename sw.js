const CACHE_VERSION = 'v1.0.0';
const CACHE_NAME = `db-guru-62-${CACHE_VERSION}`;
const OFFLINE_URL = '/Data-Base-Guru-v2/offline.html';

const STATIC_CACHE_URLS = [
    '/Data-Base-Guru-v2/',
    '/Data-Base-Guru-v2/index.php',
    '/Data-Base-Guru-v2/offline.html',
    '/Data-Base-Guru-v2/assets/css/style.css',
    '/Data-Base-Guru-v2/assets/css/ajax.css',
    '/Data-Base-Guru-v2/assets/js/main.js',
    '/Data-Base-Guru-v2/assets/js/ajax.js',
    '/Data-Base-Guru-v2/assets/js/session-keepalive.js',
    '/Data-Base-Guru-v2/assets/js/pwa.js',
    '/Data-Base-Guru-v2/assets/images/smk62.png',
    '/Data-Base-Guru-v2/assets/images/icons/icon-192x192.png',
    '/Data-Base-Guru-v2/assets/images/icons/icon-512x512.png',
    'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css'
];

const DYNAMIC_CACHE_URLS = [
    '/Data-Base-Guru-v2/pages/files/index.php',
    '/Data-Base-Guru-v2/pages/files/upload.php',
    '/Data-Base-Guru-v2/pages/links/index.php',
    '/Data-Base-Guru-v2/pages/forms/index.php',
    '/Data-Base-Guru-v2/pages/settings.php'
];

self.addEventListener('install', (event) => {
    console.log('[SW] Installing service worker...', CACHE_VERSION);
    
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then((cache) => {
                console.log('[SW] Caching static assets');
                return cache.addAll(STATIC_CACHE_URLS);
            })
            .then(() => {
                console.log('[SW] Static assets cached successfully');
                return self.skipWaiting();
            })
            .catch((error) => {
                console.error('[SW] Failed to cache static assets:', error);
            })
    );
});

self.addEventListener('activate', (event) => {
    console.log('[SW] Activating service worker...', CACHE_VERSION);
    
    event.waitUntil(
        caches.keys()
            .then((cacheNames) => {
                return Promise.all(
                    cacheNames.map((cacheName) => {
                        if (cacheName !== CACHE_NAME) {
                            console.log('[SW] Deleting old cache:', cacheName);
                            return caches.delete(cacheName);
                        }
                    })
                );
            })
            .then(() => {
                console.log('[SW] Service worker activated');
                return self.clients.claim();
            })
    );
});

self.addEventListener('fetch', (event) => {
    const { request } = event;
    const url = new URL(request.url);
    
    if (request.method !== 'GET') {
        return;
    }
    
    if (url.origin === location.origin && url.pathname.includes('/auth/')) {
        return;
    }
    
    if (url.pathname.includes('/includes/api.php')) {
        event.respondWith(networkFirst(request));
        return;
    }
    
    if (url.pathname.endsWith('.php')) {
        event.respondWith(networkFirst(request));
        return;
    }
    
    if (url.pathname.match(/\.(css|js|png|jpg|jpeg|gif|svg|woff|woff2|ttf|eot)$/)) {
        event.respondWith(cacheFirst(request));
        return;
    }
    
    event.respondWith(networkFirst(request));
});

async function cacheFirst(request) {
    try {
        const cachedResponse = await caches.match(request);
        
        if (cachedResponse) {
            console.log('[SW] Cache hit:', request.url);
            return cachedResponse;
        }
        
        console.log('[SW] Cache miss, fetching:', request.url);
        const networkResponse = await fetch(request);
        
        if (networkResponse.ok) {
            const cache = await caches.open(CACHE_NAME);
            cache.put(request, networkResponse.clone());
        }
        
        return networkResponse;
        
    } catch (error) {
        console.error('[SW] Cache-first failed:', error);
        
        const cachedResponse = await caches.match(request);
        if (cachedResponse) {
            return cachedResponse;
        }
        
        return await caches.match(OFFLINE_URL);
    }
}

async function networkFirst(request) {
    try {
        console.log('[SW] Network first:', request.url);
        const networkResponse = await fetch(request);
        
        if (networkResponse.ok) {
            const cache = await caches.open(CACHE_NAME);
            cache.put(request, networkResponse.clone());
        }
        
        return networkResponse;
        
    } catch (error) {
        console.log('[SW] Network failed, trying cache:', request.url);
        
        const cachedResponse = await caches.match(request);
        
        if (cachedResponse) {
            console.log('[SW] Serving from cache:', request.url);
            return cachedResponse;
        }
        
        console.log('[SW] No cache available, serving offline page');
        return await caches.match(OFFLINE_URL);
    }
}

self.addEventListener('message', (event) => {
    if (event.data && event.data.type === 'SKIP_WAITING') {
        console.log('[SW] Received SKIP_WAITING message');
        self.skipWaiting();
    }
    
    if (event.data && event.data.type === 'CACHE_URLS') {
        console.log('[SW] Received CACHE_URLS message');
        event.waitUntil(
            caches.open(CACHE_NAME)
                .then((cache) => {
                    return cache.addAll(event.data.urls);
                })
        );
    }
    
    if (event.data && event.data.type === 'CLEAR_CACHE') {
        console.log('[SW] Received CLEAR_CACHE message');
        event.waitUntil(
            caches.keys().then((cacheNames) => {
                return Promise.all(
                    cacheNames.map((cacheName) => caches.delete(cacheName))
                );
            })
        );
    }
});

self.addEventListener('sync', (event) => {
    console.log('[SW] Background sync:', event.tag);
    
    if (event.tag === 'sync-data') {
        event.waitUntil(syncData());
    }
});

async function syncData() {
    try {
        console.log('[SW] Syncing data...');
        
    } catch (error) {
        console.error('[SW] Sync failed:', error);
    }
}

self.addEventListener('push', (event) => {
    console.log('[SW] Push notification received');
    
    const options = {
        body: event.data ? event.data.text() : 'Notifikasi baru dari Database Guru',
        icon: '/Data-Base-Guru-v2/assets/images/icons/icon-192x192.png',
        badge: '/Data-Base-Guru-v2/assets/images/icons/icon-72x72.png',
        vibrate: [200, 100, 200],
        tag: 'db-guru-notification',
        requireInteraction: false
    };
    
    event.waitUntil(
        self.registration.showNotification('Database Guru SMKN 62', options)
    );
});

self.addEventListener('notificationclick', (event) => {
    console.log('[SW] Notification clicked');
    
    event.notification.close();
    
    event.waitUntil(
        clients.openWindow('/Data-Base-Guru-v2/index.php')
    );
});
