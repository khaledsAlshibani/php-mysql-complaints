<?php

class Complaint {
    private $db;
    
    public function __construct() {
        $this->db = Database::getConnection();
    }
    
    public function getComplaintsByUser($userId) {
        try {
            $query = "SELECT * FROM complaints WHERE user_id = ? ORDER BY id DESC";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$userId]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get Complaints Error: " . $e->getMessage());
            throw new Exception("Failed to get complaints");
        }
    }
    
    public function getAllComplaints() {
        try {
            $query = "SELECT * FROM complaints ORDER BY id DESC";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get All Complaints Error: " . $e->getMessage());
            throw new Exception("Failed to get all complaints");
        }
    }
    
    public function getComplaintById($id) {
        try {
            $query = "SELECT * FROM complaints WHERE id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$id]);
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get Complaint Error: " . $e->getMessage());
            throw new Exception("Failed to get complaint");
        }
    }
    
    public function createComplaint($data) {
        try {
            $query = "INSERT INTO complaints (user_id, title, description, status) VALUES (?, ?, ?, ?)";
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                $data['user_id'],
                $data['title'],
                $data['description'],
                $data['status'] ?? 'pending'
            ]);
            
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log("Create Complaint Error: " . $e->getMessage());
            throw new Exception("Failed to create complaint");
        }
    }
    
    public function updateComplaint($id, $data) {
        try {
            $existingComplaint = $this->getComplaintById($id);
            if (!$existingComplaint) {
                throw new Exception("Complaint not found");
            }

            $updateFields = [];
            $params = [];

            if (isset($data['title'])) {
                $updateFields[] = "title = ?";
                $params[] = $data['title'];
            }

            if (isset($data['description'])) {
                $updateFields[] = "description = ?";
                $params[] = $data['description'];
            }

            if (isset($data['status'])) {
                $updateFields[] = "status = ?";
                $params[] = $data['status'];
            }

            if (isset($data['feedback'])) {
                $updateFields[] = "feedback = ?";
                $params[] = $data['feedback'];
            }

            $params[] = $id;

            $query = "UPDATE complaints SET " . implode(", ", $updateFields) . " WHERE id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            
            return true;
        } catch (PDOException $e) {
            error_log("Update Complaint Error: " . $e->getMessage());
            throw new Exception("Failed to update complaint");
        }
    }
    
    public function deleteComplaint($id) {
        try {
            $query = "DELETE FROM complaints WHERE id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$id]);
            
            return true;
        } catch (PDOException $e) {
            error_log("Delete Complaint Error: " . $e->getMessage());
            throw new Exception("Failed to delete complaint");
        }
    }

    public function addFeedback($id, $feedback) {
        try {
            return $this->updateComplaint($id, [
                'feedback' => $feedback,
                'status' => '1'
            ]);
        } catch (Exception $e) {
            error_log("Add Feedback Error: " . $e->getMessage());
            throw new Exception("Failed to add feedback");
        }
    }

    public function removeFeedback($id) {
        try {
            return $this->updateComplaint($id, [
                'feedback' => null,
                'status' => '0'
            ]);
        } catch (Exception $e) {
            error_log("Remove Feedback Error: " . $e->getMessage());
            throw new Exception("Failed to remove feedback");
        }
    }
}
