const version = 3,
    cacheName = `przylbica-dla-medyka-${version}`;

self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(cacheName).then(cache => {
            return cache.addAll([
                '/media/css/styles.css',
                '/media/css/styles.min.css',
                '/media/js/index.js',
                '/media/js/index.min.js',
                '/media/js/external.min.js',
                '/includes/manifest.json',
                '/media/img/logo.png',
                '/media/img/favicon.png',
                '/media/img/404.jpg',
                '/media/img/offline.jpg',
                '/includes/images/icons/icon-72x72.png',
                '/includes/images/icons/icon-96x96.png',
                '/includes/images/icons/icon-128x128.png',
                '/includes/images/icons/icon-144x144.png',
                '/includes/images/icons/icon-152x152.png',
                '/includes/images/icons/icon-192x192.png',
                '/includes/images/icons/icon-384x384.png',
                '/includes/images/icons/icon-512x512.png',
                '/offline',
                'https://cdnjs.cloudflare.com/ajax/libs/mdbootstrap/4.7.5/css/mdb.min.css',
                'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.1/css/all.min.css',
                'https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/css/bootstrap.min.css',
                'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.1/webfonts/fa-solid-900.ttf',
                'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.1/webfonts/fa-solid-900.woff',
                'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.1/webfonts/fa-solid-900.woff2',
                'https://cdnjs.cloudflare.com/ajax/libs/mdbootstrap/4.7.5/font/roboto/Roboto-Bold.ttf',
                'https://cdnjs.cloudflare.com/ajax/libs/mdbootstrap/4.7.5/font/roboto/Roboto-Bold.woff',
                'https://cdnjs.cloudflare.com/ajax/libs/mdbootstrap/4.7.5/font/roboto/Roboto-Bold.woff2',
                'https://cdnjs.cloudflare.com/ajax/libs/mdbootstrap/4.7.5/font/roboto/Roboto-Light.ttf',
                'https://cdnjs.cloudflare.com/ajax/libs/mdbootstrap/4.7.5/font/roboto/Roboto-Light.woff',
                'https://cdnjs.cloudflare.com/ajax/libs/mdbootstrap/4.7.5/font/roboto/Roboto-Light.woff2',
                'https://cdnjs.cloudflare.com/ajax/libs/mdbootstrap/4.7.5/font/roboto/Roboto-Regular.ttf',
                'https://cdnjs.cloudflare.com/ajax/libs/mdbootstrap/4.7.5/font/roboto/Roboto-Regular.woff',
                'https://cdnjs.cloudflare.com/ajax/libs/mdbootstrap/4.7.5/font/roboto/Roboto-Regular.woff2',
                'https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.19/css/dataTables.bootstrap4.css',
                'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker3.min.css',
                'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.12/css/select2.min.css',
                'https://fonts.googleapis.com/icon?family=Material+Icons',
                'https://unpkg.com/leaflet@1.6.0/dist/leaflet.css'
            ]);
        })
    )
});

self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames.map(name => {
                    if (name !== cacheName) {
                        return caches.delete(name);
                    }
                })
            );
        })
    );
});

self.addEventListener('fetch', event => {
    if (event.request.method === 'POST') {
        return;
    }
    event.respondWith(
        caches.open(cacheName).then(cache => {
            return cache.match(event.request).then(response => {
                if (response) {
                    return response;
                }
                return fetch(event.request).then(response => {
                    if (response.status === 404) {
                        return cache.match('/offline');
                    }
                    return response;
                });
            });
        }).catch(() => {
            return caches.match('/offline');
        })
    );
});