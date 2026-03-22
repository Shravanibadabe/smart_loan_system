const CACHE_NAME = "loan-ai-v4";

const STATIC_FILES = [
  "/loan_ai_project/style.css"
];

// Install
self.addEventListener("install", event => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => cache.addAll(STATIC_FILES))
  );
});

// Fetch
self.addEventListener("fetch", event => {
  const url = event.request.url;

  // ❌ DO NOT cache PHP files
  if (url.includes(".php")) {
    return fetch(event.request);
  }

  // ✅ Cache only static files
  event.respondWith(
    caches.match(event.request)
      .then(response => response || fetch(event.request))
  );
});