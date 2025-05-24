<?php
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// If the request is for an upload file, serve it from storage
if (strpos($uri, '/uploads/') === 0) {
    $file = __DIR__ . '/../storage' . $uri;
    if (file_exists($file)) {
        $mime = mime_content_type($file);
        header('Content-Type: ' . $mime);
        readfile($file);
        exit;
    }
}

// For all other requests, let the main application handle it
require __DIR__ . '/index.php';