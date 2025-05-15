<?php

class Database
{
  private static $connection = null;

  public static function getConnection()
  {
    $host = 'localhost';
    $dbname = 'complaints_suggestions_db';
    $user = 'root';
    $pass = 'root';

    if (self::$connection === null) {
      try {
        self::$connection = new PDO(
          "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
          $user,
          $pass
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
      return $fetchAll ? $stmt->fetchAll(PDO::FETCH_ASSOC) : $stmt->fetch(PDO::FETCH_ASSOC);
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
