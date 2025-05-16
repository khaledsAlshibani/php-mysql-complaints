<?php

namespace App\Services;

use PDO;
use App\Core\Database;

class SystemService
{
  private PDO $db;

  public function __construct()
  {
    $this->db = Database::getInstance();
  }

  public function initializeSystem(): void
  {
    $this->initializeAdminUser();
  }

  private function initializeAdminUser(): void
  {
    try {
      if ($this->adminExists()) {
        return;
      }

      $adminData = $this->getDefaultAdminData();
      $this->createAdminUser($adminData);

      echo "System administrator account initialized successfully.\n";
    } catch (\PDOException $e) {
      echo "Error initializing admin user: " . $e->getMessage() . "\n";
    }
  }

  private function adminExists(): bool
  {
    $stmt = $this->db->prepare('SELECT id FROM users WHERE role = ? LIMIT 1');
    $stmt->execute(['admin']);
    return (bool) $stmt->fetch();
  }

  private function getDefaultAdminData(): array
  {
    return [
      'username' => 'admin',
      'password' => password_hash('Admin123!', PASSWORD_DEFAULT),
      'first_name' => 'System',
      'last_name' => 'Administrator',
      'birth_date' => date('Y-m-d'),
      'role' => 'admin'
    ];
  }

  private function createAdminUser(array $adminData): void
  {
    $stmt = $this->db->prepare('
      INSERT INTO users (username, password, first_name, last_name, birth_date, role)
      VALUES (:username, :password, :first_name, :last_name, :birth_date, :role)
    ');

    $stmt->execute($adminData);
  }
}
