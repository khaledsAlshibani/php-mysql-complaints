<?php

use Dotenv\Dotenv;

class Config {
    private static $loaded = false;
    
    public static function load() {
        if (!self::$loaded) {
            $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
            $dotenv->safeLoad();
            self::$loaded = true;
        }
    }
    
    public static function get($key, $default = null) {
        return $_ENV[$key] ?? $default;
    }
    
    public static function getBaseUrl() {
        $port = self::get('PORT');
        if ($port) {
            return "http://" . self::get('HOST', 'localhost') . ":{$port}/";
        }
        return self::get('APP_URL', 'http://localhost/php-mysql-complaints-app/src/');
    }
} 