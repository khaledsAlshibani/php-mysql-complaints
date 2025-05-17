<?php

namespace App\Core;

class Logger
{
    private const LOG_LEVELS = [
        'ERROR' => 'ERROR',
        'WARNING' => 'WARNING',
        'INFO' => 'INFO',
        'DEBUG' => 'DEBUG'
    ];

    private const LOG_DIRECTORY = __DIR__ . '/../../storage/logs/';
    private const MAX_LOG_SIZE = 5 * 1024 * 1024; // 5MB

    private static ?Logger $instance = null;
    private string $logFile;

    private function __construct()
    {
        $this->logFile = self::LOG_DIRECTORY . date('Y-m-d') . '.log';
        $this->ensureLogDirectoryExists();
    }

    public static function getInstance(): Logger
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function error(string $message, array $context = []): void
    {
        $this->log(self::LOG_LEVELS['ERROR'], $message, $context);
    }

    public function warning(string $message, array $context = []): void
    {
        $this->log(self::LOG_LEVELS['WARNING'], $message, $context);
    }

    public function info(string $message, array $context = []): void
    {
        $this->log(self::LOG_LEVELS['INFO'], $message, $context);
    }

    public function debug(string $message, array $context = []): void
    {
        $this->log(self::LOG_LEVELS['DEBUG'], $message, $context);
    }

    private function log(string $level, string $message, array $context = []): void
    {
        $this->rotateLogIfNeeded();

        $timestamp = date('Y-m-d H:i:s');
        $contextString = !empty($context) ? ' ' . json_encode($context) : '';
        $logMessage = "[{$timestamp}] [{$level}] {$message}{$contextString}" . PHP_EOL;

        file_put_contents($this->logFile, $logMessage, FILE_APPEND | LOCK_EX);
    }

    private function ensureLogDirectoryExists(): void
    {
        if (!file_exists(self::LOG_DIRECTORY)) {
            mkdir(self::LOG_DIRECTORY, 0755, true);
        }
    }

    private function rotateLogIfNeeded(): void
    {
        if (file_exists($this->logFile) && filesize($this->logFile) > self::MAX_LOG_SIZE) {
            $archiveFile = $this->logFile . '.' . time() . '.archive';
            rename($this->logFile, $archiveFile);
        }
    }

    private function __clone() {}

    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize singleton");
    }
}
