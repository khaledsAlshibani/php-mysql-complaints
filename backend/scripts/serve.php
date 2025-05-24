<?php

$host = '127.0.0.1';
$port = 8000;
$publicDir = __DIR__ . '/../public';

$routerFile = $publicDir . '/router.php';
file_put_contents($routerFile, <<<'PHP'
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
PHP
);

echo "Starting development server at http://{$host}:{$port}\n";
echo "Press Ctrl+C to stop\n";

$command = sprintf(
    'php -S %s:%d -t %s %s',
    $host,
    $port,
    escapeshellarg($publicDir),
    escapeshellarg($routerFile)
);

passthru($command);
