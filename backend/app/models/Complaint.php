<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class Complaint extends Model
{
    protected string $table = 'complaints';
    protected array $fillable = [
        'user_id',
        'content',
        'status'
    ];

    private int $id;
    private int $userId;
    private string $content;
    private string $status;
    private string $createdAt;

    public function find(int $id): ?static
    {
        $stmt = $this->db->prepare('SELECT * FROM complaints WHERE id = :id');
        $stmt->execute(['id' => $id]);

        if ($complaint = $stmt->fetch(PDO::FETCH_ASSOC)) {
            return $this->mapToObject($complaint);
        }

        return null;
    }

    public function create(array $data): int|false
    {
        $stmt = $this->db->prepare('
            INSERT INTO complaints (user_id, content, status)
            VALUES (:userId, :content, :status)
        ');

        $success = $stmt->execute([
            'userId' => $data['user_id'],
            'content' => $data['content'],
            'status' => $data['status'] ?? 'pending_no_feedback'
        ]);

        return $success ? (int)$this->db->lastInsertId() : false;
    }

    public function update(array $data): bool
    {
        $updateFields = [];
        $params = ['id' => $this->id];

        if (isset($data['content'])) {
            $updateFields[] = 'content = :content';
            $params['content'] = $data['content'];
        }

        if (isset($data['status'])) {
            $updateFields[] = 'status = :status';
            $params['status'] = $data['status'];
        }

        if (empty($updateFields)) {
            return true;
        }

        $sql = 'UPDATE complaints SET ' . implode(', ', $updateFields) . ' WHERE id = :id';
        $stmt = $this->db->prepare($sql);

        return $stmt->execute($params);
    }

    public function delete(): bool
    {
        $stmt = $this->db->prepare('DELETE FROM complaints WHERE id = :id');
        return $stmt->execute(['id' => $this->id]);
    }

    public function getUser(): ?User
    {
        $user = new User();
        return $user->find($this->userId);
    }

    public function getFeedback(): array
    {
        $feedback = new Feedback();
        return $feedback->getAllForComplaint($this->id);
    }

    public function getAllByUser(int $userId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM complaints WHERE user_id = :userId ORDER BY created_at DESC');
        $stmt->execute(['userId' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllByStatus(string $status): array
    {
        $stmt = $this->db->prepare('SELECT * FROM complaints WHERE status = :status ORDER BY created_at DESC');
        $stmt->execute(['status' => $status]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    protected function mapToObject(array $data): static
    {
        $this->id = (int)$data['id'];
        $this->userId = (int)$data['user_id'];
        $this->content = $data['content'];
        $this->status = $data['status'];
        $this->createdAt = $data['created_at'];

        return $this;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }
}
