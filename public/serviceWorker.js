var staticDevCoffee = 'hpEnvy4525';
var assets = [
  '/index.php',
];

const version = 1

self.addEventListener("install", installEvent => {
  installEvent.waitUntil(
    caches.open(staticDevCoffee).then(cache => {
      cache.addAll(assets)
    })
  )
})

self.addEventListener("fetch", fetchEvent => {
  fetchEvent.respondWith(
    caches.match(fetchEvent.request).then(res => {
      return res || fetch(fetchEvent.request)
    })
  )
})
caches.keys().then(function(names) {
    for (let name of names)
        caches.delete(name);
});
