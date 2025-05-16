<?php

$host = '127.0.0.1';
$port = 8000;
$publicDir = __DIR__ . '/../public';

echo "Starting development server at http://{$host}:{$port}\n";
echo "Press Ctrl+C to stop\n";

$command = sprintf(
    'php -S %s:%d -t %s',
    $host,
    $port,
    escapeshellarg($publicDir)
);

passthru($command);
