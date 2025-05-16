<?php

require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

return [
  'paths' => [
    'migrations' => '%%PHINX_CONFIG_DIR%%/database/migrations',
    'seeds'      => '%%PHINX_CONFIG_DIR%%/database/seeds',
  ],
  'environments' => [
    'default_migration_table' => 'phinxlog',
    'default_environment' => 'development',
    'development' => [
      'adapter' => 'mysql',
      'host' => $_ENV['DB_HOST'] ?? 'localhost',
      'name' => $_ENV['DB_NAME'],
      'user' => $_ENV['DB_USER'],
      'pass' => $_ENV['DB_PASS'],
      'port' => $_ENV['DB_PORT'] ?? '3306',
      'charset' => 'utf8mb4',
    ],
  ],
  'version_order' => 'creation'
];
