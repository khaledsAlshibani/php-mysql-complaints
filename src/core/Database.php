<?php

class Database
{
  private static $connection = null;

  public static function getConnection()
  {
    $host = Config::get('DB_HOST', 'localhost');
    $dbname = Config::get('DB_NAME', 'complaints_suggestions_db');
    $user = Config::get('DB_USER', 'root');
    $pass = Config::get('DB_PASS', 'root');

    if (self::$connection === null) {
      try {
        self::$connection = new PDO(
          "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
          $user,
          $pass,
          [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
          ]
        );
      } catch (PDOException $e) {
        error_log("Connection Error: " . $e->getMessage());
        die("Connection failed: " . $e->getMessage());
      }
    }
    return self::$connection;
  }

  public static function executeSelect($query, $params = [], $fetchAll = true)
  {
    try {
      $stmt = self::getConnection()->prepare($query);
      $stmt->execute($params);
      return $fetchAll ? $stmt->fetchAll() : $stmt->fetch();
    } catch (PDOException $e) {
      error_log("Select Query Error: " . $e->getMessage());
      throw new Exception("Failed to execute select query");
    }
  }

  public static function executeInsert($query, $params = [])
  {
    try {
      $stmt = self::getConnection()->prepare($query);
      $stmt->execute($params);
      return self::getConnection()->lastInsertId();
    } catch (PDOException $e) {
      error_log("Insert Query Error: " . $e->getMessage());
      throw new Exception("Failed to execute insert query");
    }
  }

  public static function executeUpdate($query, $params = [])
  {
    try {
      $stmt = self::getConnection()->prepare($query);
      return $stmt->execute($params);
    } catch (PDOException $e) {
      error_log("Update Query Error: " . $e->getMessage());
      throw new Exception("Failed to execute update query");
    }
  }

  public static function executeDelete($query, $params = [])
  {
    try {
      $stmt = self::getConnection()->prepare($query);
      return $stmt->execute($params);
    } catch (PDOException $e) {
      error_log("Delete Query Error: " . $e->getMessage());
      throw new Exception("Failed to execute delete query");
    }
  }
}
