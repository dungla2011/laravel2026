<?php

// Router for PHP built-in server
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// Always route through index.php for dynamic pages and API
if ($uri === '/' || $uri === '/index.php' || strpos($uri, '/api/') === 0) {
    require __DIR__ . '/index.php';
    return true;
}

// Static files (CSS, JS, images, etc.)
$file = __DIR__ . '/public' . $uri;
if (file_exists($file) && is_file($file)) {
    // Let PHP serve static files
    return false;
}

// 404 - route through index.php
require __DIR__ . '/index.php';
return true;
