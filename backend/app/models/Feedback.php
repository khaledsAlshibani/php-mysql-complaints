<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class Feedback extends Model
{
    protected string $table = 'feedback';
    protected array $fillable = [
        'admin_id',
        'complaint_id',
        'suggestion_id',
        'content'
    ];

    private int $id;
    private int $adminId;
    private ?int $complaintId;
    private ?int $suggestionId;
    private string $content;
    private string $createdAt;

    public function find(int $id): ?static
    {
        $stmt = $this->db->prepare('SELECT * FROM feedback WHERE id = :id');
        $stmt->execute(['id' => $id]);

        if ($feedback = $stmt->fetch(PDO::FETCH_ASSOC)) {
            return $this->mapToObject($feedback);
        }

        return null;
    }

    public function create(array $data): int|false
    {
        $stmt = $this->db->prepare('
            INSERT INTO feedback (admin_id, complaint_id, suggestion_id, content)
            VALUES (:adminId, :complaintId, :suggestionId, :content)
        ');

        $success = $stmt->execute([
            'adminId' => $data['admin_id'],
            'complaintId' => $data['complaint_id'] ?? null,
            'suggestionId' => $data['suggestion_id'] ?? null,
            'content' => $data['content']
        ]);

        return $success ? (int)$this->db->lastInsertId() : false;
    }

    public function update(array $data): bool
    {
        $stmt = $this->db->prepare('
            UPDATE feedback 
            SET content = :content
            WHERE id = :id
        ');

        return $stmt->execute([
            'content' => $data['content'],
            'id' => $this->id
        ]);
    }

    public function delete(): bool
    {
        $stmt = $this->db->prepare('DELETE FROM feedback WHERE id = :id');
        return $stmt->execute(['id' => $this->id]);
    }

    public function getAdmin(): ?User
    {
        $user = new User();
        return $user->find($this->adminId);
    }

    public function getAllForComplaint(int $complaintId): array
    {
        $stmt = $this->db->prepare('
            SELECT f.*, u.username, u.first_name, u.last_name 
            FROM feedback f
            JOIN users u ON f.admin_id = u.id
            WHERE f.complaint_id = :complaintId 
            ORDER BY f.created_at DESC
        ');
        $stmt->execute(['complaintId' => $complaintId]);
        $feedbacks = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Format each feedback entry
        return array_map(function($feedback) {
            return [
                'id' => (int)$feedback['id'],
                'content' => $feedback['content'],
                'createdAt' => $feedback['created_at'],
                'admin' => [
                    'id' => (int)$feedback['admin_id'],
                    'username' => $feedback['username'],
                    'fullName' => trim($feedback['first_name'] . ' ' . ($feedback['last_name'] ?? ''))
                ],
                'complaintId' => $feedback['complaint_id'] ? (int)$feedback['complaint_id'] : null,
                'suggestionId' => $feedback['suggestion_id'] ? (int)$feedback['suggestion_id'] : null
            ];
        }, $feedbacks);
    }

    public function getAllForSuggestion(int $suggestionId): array
    {
        $stmt = $this->db->prepare('
            SELECT f.*, u.username, u.first_name, u.last_name 
            FROM feedback f
            JOIN users u ON f.admin_id = u.id
            WHERE f.suggestion_id = :suggestionId 
            ORDER BY f.created_at DESC
        ');
        $stmt->execute(['suggestionId' => $suggestionId]);
        $feedbacks = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Format each feedback entry
        return array_map(function($feedback) {
            return [
                'id' => (int)$feedback['id'],
                'content' => $feedback['content'],
                'createdAt' => $feedback['created_at'],
                'admin' => [
                    'id' => (int)$feedback['admin_id'],
                    'username' => $feedback['username'],
                    'fullName' => trim($feedback['first_name'] . ' ' . ($feedback['last_name'] ?? ''))
                ],
                'complaintId' => $feedback['complaint_id'] ? (int)$feedback['complaint_id'] : null,
                'suggestionId' => $feedback['suggestion_id'] ? (int)$feedback['suggestion_id'] : null
            ];
        }, $feedbacks);
    }

    public function getAllByAdmin(int $adminId): array
    {
        $stmt = $this->db->prepare('
            SELECT f.*, u.username, u.first_name, u.last_name 
            FROM feedback f
            JOIN users u ON f.admin_id = u.id
            WHERE f.admin_id = :adminId 
            ORDER BY f.created_at DESC
        ');
        $stmt->execute(['adminId' => $adminId]);
        $feedbacks = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Format each feedback entry
        return array_map(function($feedback) {
            return [
                'id' => (int)$feedback['id'],
                'content' => $feedback['content'],
                'createdAt' => $feedback['created_at'],
                'admin' => [
                    'id' => (int)$feedback['admin_id'],
                    'username' => $feedback['username'],
                    'fullName' => trim($feedback['first_name'] . ' ' . ($feedback['last_name'] ?? ''))
                ],
                'complaintId' => $feedback['complaint_id'] ? (int)$feedback['complaint_id'] : null,
                'suggestionId' => $feedback['suggestion_id'] ? (int)$feedback['suggestion_id'] : null
            ];
        }, $feedbacks);
    }

    public function getAllForSubmission(int $submissionId): array
    {
        $stmt = $this->db->prepare('
            SELECT f.*, u.username, u.first_name, u.last_name 
            FROM feedback f
            JOIN users u ON f.admin_id = u.id
            WHERE f.complaint_id = :submissionId OR f.suggestion_id = :submissionId
            ORDER BY f.created_at DESC
        ');
        $stmt->execute(['submissionId' => $submissionId]);
        $feedbacks = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Format each feedback entry
        return array_map(function($feedback) {
            return [
                'id' => (int)$feedback['id'],
                'content' => $feedback['content'],
                'createdAt' => $feedback['created_at'],
                'admin' => [
                    'id' => (int)$feedback['admin_id'],
                    'username' => $feedback['username'],
                    'fullName' => trim($feedback['first_name'] . ' ' . ($feedback['last_name'] ?? ''))
                ],
                'submissionId' => $feedback['complaint_id'] ? (int)$feedback['complaint_id'] : (int)$feedback['suggestion_id']
            ];
        }, $feedbacks);
    }

    protected function mapToObject(array $data): static
    {
        $this->id = (int)$data['id'];
        $this->adminId = (int)$data['admin_id'];
        $this->complaintId = isset($data['complaint_id']) ? (int)$data['complaint_id'] : null;
        $this->suggestionId = isset($data['suggestion_id']) ? (int)$data['suggestion_id'] : null;
        $this->content = $data['content'];
        $this->createdAt = $data['created_at'];

        return $this;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getAdminId(): int
    {
        return $this->adminId;
    }

    public function getComplaintId(): ?int
    {
        return $this->complaintId;
    }

    public function getSuggestionId(): ?int
    {
        return $this->suggestionId;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }
}
