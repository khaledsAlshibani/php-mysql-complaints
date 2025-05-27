<?php

use App\Core\Router;
use App\Config\Config;

require_once __DIR__ . '/../vendor/autoload.php';

// Initialize configuration
require_once __DIR__ . '/../app/config/config.php';
Config::getInstance()->init();

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

$router = new Router();

// Include route files
require_once __DIR__ . '/api/auth.php';
require_once __DIR__ . '/api/user.php';
require_once __DIR__ . '/api/complaint.php';
require_once __DIR__ . '/api/suggestion.php';
require_once __DIR__ . '/api/feedback.php';

$router->handleRequest();
