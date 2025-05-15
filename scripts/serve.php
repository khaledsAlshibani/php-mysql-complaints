<?php
$host = 'localhost';
$port = 8000;
$docRoot = realpath(__DIR__ . '/../src');

if (!is_dir($docRoot)) {
    echo "Error: Document root '$docRoot' does not exist.\n";
    exit(1);
}

echo "-----------------------------------------------------\n";
echo "Starting PHP dev server at http://$host:$port\n";
echo "-----------------------------------------------------\n";

$cmd = sprintf('php -S %s:%d -t %s', $host, $port, escapeshellarg($docRoot));
passthru($cmd);
