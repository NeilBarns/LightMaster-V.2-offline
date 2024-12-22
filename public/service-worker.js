const CACHE_NAME = 'lightmaster-cache-v1';
const urlsToCache = [
    '/',
    '/offline.html',
    '/css/app.css',
    '/js/app.js',
    'https://code.jquery.com/jquery-3.6.0.min.js',
    'https://cdn.jsdelivr.net/npm/uikit@3.20.8/dist/css/uikit.min.css',
    'https://cdn.jsdelivr.net/npm/uikit@3.20.8/dist/js/uikit.min.js',
    'https://cdn.jsdelivr.net/npm/uikit@3.20.8/dist/js/uikit-icons.min.js',
    // 'https://cdn.jsdelivr.net/npm/ag-grid-community/dist/ag-grid-community.min.js',
    'https://cdn.jsdelivr.net/npm/chart.js',
    'https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js',
    'https://cdnjs.cloudflare.com/ajax/libs/fomantic-ui/2.9.3/semantic.min.css',
    'https://cdnjs.cloudflare.com/ajax/libs/fomantic-ui/2.9.3/semantic.min.js',
    '"https://unpkg.com/ag-grid-community/styles/ag-grid.css',
    'https://unpkg.com/ag-grid-community/styles/ag-theme-balham.css',
    'https://unpkg.com/ag-grid-community/dist/ag-grid-community.min.js'
    // Add other assets as needed
];

// Install event: Cache assets
// self.addEventListener('install', event => {
//     event.waitUntil(
//         caches.open(CACHE_NAME)
//         .then(cache => {
//             return cache.addAll(urlsToCache);
//         })
//     );
// });

// // Fetch event: Serve cached assets
// self.addEventListener('fetch', event => {
//     event.respondWith(
//         caches.match(event.request)
//         .then(response => {
//             return response || fetch(event.request).catch(() => caches.match('/offline.html'));
//         })
//     );
// });

// // Activate event: Clean up old caches
// self.addEventListener('activate', event => {
//     const cacheWhitelist = [CACHE_NAME];
//     event.waitUntil(
//         caches.keys().then(cacheNames => {
//             return Promise.all(
//                 cacheNames.map(cacheName => {
//                     if (cacheWhitelist.indexOf(cacheName) === -1) {
//                         return caches.delete(cacheName);
//                     }
//                 })
//             );
//         })
//     );
// });
