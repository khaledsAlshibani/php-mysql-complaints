<?php

// Require Composer's autoloader
require_once __DIR__ . '/../vendor/autoload.php';

// Start session
session_start();

// Configure error reporting
error_reporting(E_ALL);
ini_set('display_errors', 'On');

// Load environment configuration
require_once __DIR__ . '/core/Config.php';
Config::load();

// Define directory constants
define('BASE_URL', Config::getBaseUrl());
define('HOME_DIR', str_replace('\\', '/', __DIR__) . '/');
define('CORE_DIR', HOME_DIR . 'core/');
define('TEMPLATES_DIR', HOME_DIR . 'templates/');
define('INC_DIR', TEMPLATES_DIR . 'includes/');
define('PAGES_DIR', TEMPLATES_DIR . 'pages/');
define('COMPS_DIR', TEMPLATES_DIR . 'components/');
define('MODALS_DIR', COMPS_DIR . 'modals/');

// Generate error logs into an `error.log` file
ini_set("error_log", HOME_DIR . "logs/error.log");

// Include core classes
require_once CORE_DIR . 'Database.php';
require_once CORE_DIR . 'User.php';
require_once CORE_DIR . 'Complaint.php';
require_once CORE_DIR . 'Suggestion.php';
require_once CORE_DIR . 'FormHandler.php';
require_once CORE_DIR . 'Req.php';
require_once CORE_DIR . 'Utility.php';
