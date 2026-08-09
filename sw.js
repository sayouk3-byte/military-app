/**
 * Service Worker សម្រាប់ប្រព័ន្ធគ្រប់គ្រងទិន្នន័យនាយទាហាន (PWA Offline Service Worker)
 */

const CACHE_NAME = 'military-app-v5';
const ASSETS_TO_CACHE = [
  './',
  './index.php',
  './assets/css/style.css?v=5',
  './assets/js/app.js?v=5',
  './manifest.json'
];

// Install Event - Cache Core Assets & Skip Waiting immediately
self.addEventListener('install', (event) => {
  self.skipWaiting();
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      console.log('[SW] Caching App Assets v5');
      return cache.addAll(ASSETS_TO_CACHE);
    })
  );
});

// Activate Event - Purge all old caches immediately
self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((keys) => {
      return Promise.all(
        keys.map((key) => {
          if (key !== CACHE_NAME) {
            console.log('[SW] Removing old cache:', key);
            return caches.delete(key);
          }
        })
      );
    })
  );
  self.clients.claim();
});

// Fetch Event - Network First for assets, bypass cache for api.php
self.addEventListener('fetch', (event) => {
  if (event.request.method !== 'GET') return;
  if (event.request.url.includes('api.php')) return;

  event.respondWith(
    fetch(event.request)
      .then((networkResponse) => {
        if (networkResponse && networkResponse.status === 200) {
          const responseClone = networkResponse.clone();
          caches.open(CACHE_NAME).then((cache) => {
            cache.put(event.request, responseClone);
          });
        }
        return networkResponse;
      })
      .catch(() => {
        return caches.match(event.request).then((cachedResponse) => {
          if (cachedResponse) return cachedResponse;
          if (event.request.headers.get('accept') && event.request.headers.get('accept').includes('text/html')) {
            return caches.match('./index.php');
          }
        });
      })
  );
});
