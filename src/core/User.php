<?php

class User {
    private $db;
    
    public function __construct() {
        $this->db = Database::getConnection();
    }
    
    public function authenticate($username, $password) {
        try {
            $query = "SELECT * FROM users WHERE username = ? AND password = ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$username, $password]);
            
            $user = $stmt->fetch();
            
            if ($user) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_type'] = $user['user_type'];
                return true;
            }
            return false;
        } catch (PDOException $e) {
            error_log("Authentication Error: " . $e->getMessage());
            throw new Exception("Authentication failed");
        }
    }
    
    public function logout() {
        session_destroy();
        return true;
    }
    
    public function isLoggedOut() {
        return !isset($_SESSION['user_id']);
    }
    
    public function requireLogin() {
        if ($this->isLoggedOut()) {
            header("Location: " . BASE_URL . "templates/pages/login.php");
            exit;
        }
    }
    
    public function requireLogout() {
        if (!$this->isLoggedOut()) {
            header("Location: " . BASE_URL . "index.php");
            exit;
        }
    }
    
    public function getUserId() {
        return $_SESSION['user_id'] ?? null;
    }
    
    public function getUserType() {
        return $_SESSION['user_type'] ?? null;
    }
    
    public function getFullNameById($userId) {
        try {
            $query = "SELECT full_name FROM users WHERE id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$userId]);
            
            $result = $stmt->fetch();
            return $result ? $result['full_name'] : "User not found";
        } catch (PDOException $e) {
            error_log("Get Full Name Error: " . $e->getMessage());
            throw new Exception("Failed to get user's full name");
        }
    }
}
