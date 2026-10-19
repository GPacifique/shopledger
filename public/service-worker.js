const CACHE_NAME = 'mahwi-static-v1';

const STATIC_ASSETS = [
    '/manifest.json',
    '/icons/mahwi-192.png',
    '/icons/mahwi-512.png'
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then((cache) => cache.addAll(STATIC_ASSETS))
            .then(() => self.skipWaiting())
    );
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) =>
            Promise.all(
                keys
                    .filter((key) => key !== CACHE_NAME)
                    .map((key) => caches.delete(key))
            )
        ).then(() => self.clients.claim())
    );
});

self.addEventListener('fetch', (event) => {
    // Do not cache Laravel requests or authenticated business data.
    // Add carefully selected offline strategies in a later step.
});