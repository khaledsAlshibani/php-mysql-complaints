<?php

class Suggestion {
    public function getSuggestionsByUser($userId) {
        return Database::executeSelect(
            "SELECT * FROM suggestions WHERE user_id = ? ORDER BY id DESC",
            [$userId]
        );
    }
    
    public function getAllSuggestions() {
        return Database::executeSelect(
            "SELECT * FROM suggestions ORDER BY id DESC"
        );
    }
    
    public function getSuggestionById($id) {
        return Database::executeSelect(
            "SELECT * FROM suggestions WHERE id = ?",
            [$id],
            false
        );
    }
    
    public function createSuggestion($data) {
        return Database::executeInsert(
            "INSERT INTO suggestions (user_id, title, description, status) VALUES (?, ?, ?, ?)",
            [
                $data['user_id'],
                $data['title'],
                $data['description'],
                $data['status'] ?? '0'
            ]
        );
    }
    
    public function updateSuggestion($id, $data) {
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

        return Database::executeUpdate(
            "UPDATE suggestions SET " . implode(", ", $updateFields) . " WHERE id = ?",
            $params
        );
    }
    
    public function deleteSuggestion($id) {
        return Database::executeDelete(
            "DELETE FROM suggestions WHERE id = ?",
            [$id]
        );
    }

    public function addFeedback($id, $feedback) {
        return $this->updateSuggestion($id, [
            'feedback' => $feedback,
            'status' => '1'
        ]);
    }

    public function removeFeedback($id) {
        return $this->updateSuggestion($id, [
            'feedback' => null,
            'status' => '0'
        ]);
    }
}
