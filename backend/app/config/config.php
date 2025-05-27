<?php

namespace App\Config;

use App\Core\Response;
use Dotenv\Dotenv;

class Config {
    private static ?Config $instance = null;
    private $corsHeaders = [];
    private array $config = [];

    private function __construct() {
        $this->initErrorHandling();
        $this->loadEnvironmentVariables();
        $this->initCorsHeaders();

        $this->config['api'] = [
            'prefix' => 'api',
            'version' => 'v1'
        ];
    }

    public static function getInstance(): Config {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function initErrorHandling(): void {
        error_reporting(E_ALL);
        ini_set('display_errors', '0');

        set_error_handler(function($errno, $errstr, $errfile, $errline) {
            if (!(error_reporting() & $errno)) {
                return false;
            }
            
            $error = [
                'message' => $errstr,
                'file' => $errfile,
                'line' => $errline
            ];
            
            Response::sendError('Internal Server Error', 500, $error, 'SERVER_ERROR');
            exit(1);
        });

        set_exception_handler(function($e) {
            $error = [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ];
            
            Response::sendError('Internal Server Error', 500, $error, 'SERVER_ERROR');
            exit(1);
        });
    }

    private function loadEnvironmentVariables(): void {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
        $dotenv->load();
    }

    private function initCorsHeaders(): void {
        $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
        $this->corsHeaders = [
            'Access-Control-Allow-Origin' => $origin ?: 'null',
            'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE, OPTIONS',
            'Access-Control-Allow-Headers' => 'Content-Type, Authorization, Cookie',
            'Access-Control-Allow-Credentials' => 'true',
            'Content-Type' => 'application/json'
        ];
    }

    public function applyCorsHeaders(): void {
        foreach ($this->corsHeaders as $header => $value) {
            header("$header: $value");
        }
    }

    public function init(): void {
        $this->applyCorsHeaders();
    }

    public function get(string $key, $default = null)
    {
        return $this->config[$key] ?? $default;
    }
}