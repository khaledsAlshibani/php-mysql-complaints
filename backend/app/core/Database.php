<?php

namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $instance = null;
    private static string $host;
    private static string $port;
    private static string $dbName;
    private static string $username;
    private static string $password;

    private function __construct() {}
    private function __clone() {}

    public static function init(): void
    {
        self::$host = $_ENV['DB_HOST'] ?? 'localhost';
        self::$port = $_ENV['DB_PORT'] ?? '3306';
        self::$dbName = $_ENV['DB_NAME'] ?? '';
        self::$username = $_ENV['DB_USER'] ?? '';
        self::$password = $_ENV['DB_PASS'] ?? '';

        $missingVars = [];
        if (empty(self::$dbName)) $missingVars[] = 'DB_NAME';
        if (empty(self::$username)) $missingVars[] = 'DB_USER';
        if (empty(self::$password)) $missingVars[] = 'DB_PASS';

        if (!empty($missingVars)) {
            $message = 'Missing required environment variables: ' . implode(', ', $missingVars);
            error_log($message);
            throw new \RuntimeException($message);
        }
    }

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            if (!isset(self::$host)) {
                self::init();
            }

            $dsn = sprintf(
                "mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4",
                self::$host,
                self::$port,
                self::$dbName
            );

            try {
                self::$instance = new PDO($dsn, self::$username, self::$password, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
            } catch (PDOException $e) {
                $message = 'Database connection failed: ' . $e->getMessage();
                error_log($message);
                throw new \RuntimeException($message);
            }
        }

        return self::$instance;
    }
}
