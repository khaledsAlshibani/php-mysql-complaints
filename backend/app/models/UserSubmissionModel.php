<?php

namespace App\Models;

use App\Core\Model;
use PDO;

/**
 * Class UserSubmissionModel
 *
 * Abstract base model for user-submitted content types such as complaints and suggestions.
 * 
 * This model provides common database operations including:
 * - CRUD methods (`create`, `find`, `update`, `delete`)
 * - Search and filtering by user ID, status, and content
 * - Relationships to related models like `User` and `Feedback`
 *
 * Child classes must implement:
 * - getTable(): string — returns the database table name
 *
 * Child classes may optionally override:
 * - getTableAlias(): string — returns the table alias for complex SQL queries (defaults to first letter of table name)
 *
 * Properties such as `id`, `userId`, `content`, `status`, and `createdAt` are expected to exist in the schema.
 *
 * @package App\Models
 * @abstract
 */
abstract class UserSubmissionModel extends Model
{
    protected string $table;
    protected string $tableAlias;
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
    
    public function __construct() {
        parent::__construct();
        $this->table = $this->getTable();
        $this->tableAlias = $this->getTableAlias();
    }

    abstract protected function getTable(): string;
    
    protected function getTableAlias(): string
    {
        return strtolower(substr($this->table, 0, 1));
    }

    public function find(int $id): ?static
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = :id");
        $stmt->execute(['id' => $id]);

        if ($complaint = $stmt->fetch(PDO::FETCH_ASSOC)) {
            return $this->mapToObject($complaint);
        }

        return null;
    }

    public function create(array $data): int|false
    {
        $stmt = $this->db->prepare('
            INSERT INTO ' . $this->table . ' (user_id, content, status)
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

        $sql = 'UPDATE ' . $this->table . ' SET ' . implode(', ', $updateFields) . ' WHERE id = :id';
        $stmt = $this->db->prepare($sql);

        return $stmt->execute($params);
    }

    public function delete(): bool
    {
        $stmt = $this->db->prepare('DELETE FROM ' . $this->table . ' WHERE id = :id');
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
        $stmt = $this->db->prepare('SELECT * FROM ' . $this->table . ' WHERE user_id = :userId ORDER BY created_at DESC');
        $stmt->execute(['userId' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllByUserWithSearch(int $userId, string $search): array
    {
        $stmt = $this->db->prepare('
            SELECT DISTINCT ' . $this->tableAlias . '.* 
            FROM ' . $this->table . ' ' . $this->tableAlias . '
            JOIN users u ON ' . $this->tableAlias . '.user_id = u.id
            WHERE ' . $this->tableAlias . '.user_id = :userId
            AND (
                LOWER(' . $this->tableAlias . '.content) LIKE :searchContent
                OR LOWER(u.username) LIKE :searchUsername
                OR LOWER(CONCAT(u.first_name, " ", COALESCE(u.last_name, ""))) LIKE :searchName
            )
            ORDER BY ' . $this->tableAlias . '.created_at DESC
        ');
        
        $searchTerm = '%' . strtolower($search) . '%';
        $stmt->execute([
            'userId' => $userId,
            'searchContent' => $searchTerm,
            'searchUsername' => $searchTerm,
            'searchName' => $searchTerm
        ]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllByUserAndStatus(int $userId, string $status): array
    {
        $stmt = $this->db->prepare('
            SELECT * FROM ' . $this->table . ' 
            WHERE user_id = :userId AND status = :status 
            ORDER BY created_at DESC
        ');
        $stmt->execute([
            'userId' => $userId,
            'status' => $status
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllByUserAndStatusWithSearch(int $userId, string $status, string $search): array
    {
        $stmt = $this->db->prepare('
            SELECT DISTINCT ' . $this->tableAlias . '.* 
            FROM ' . $this->table . ' ' . $this->tableAlias . '
            JOIN users u ON ' . $this->tableAlias . '.user_id = u.id
            WHERE ' . $this->tableAlias . '.user_id = :userId 
            AND ' . $this->tableAlias . '.status = :status
            AND (
                LOWER(' . $this->tableAlias . '.content) LIKE :searchContent
                OR LOWER(u.username) LIKE :searchUsername
                OR LOWER(CONCAT(u.first_name, " ", COALESCE(u.last_name, ""))) LIKE :searchName
            )
            ORDER BY ' . $this->tableAlias . '.created_at DESC
        ');
        
        $searchTerm = '%' . strtolower($search) . '%';
        $stmt->execute([
            'userId' => $userId,
            'status' => $status,
            'searchContent' => $searchTerm,
            'searchUsername' => $searchTerm,
            'searchName' => $searchTerm
        ]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllByStatus(string $status): array
    {
        $stmt = $this->db->prepare('SELECT * FROM ' . $this->table . ' WHERE status = :status ORDER BY created_at DESC');
        $stmt->execute(['status' => $status]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllByStatusWithSearch(string $status, string $search): array
    {
        $stmt = $this->db->prepare('
            SELECT DISTINCT ' . $this->tableAlias . '.* 
            FROM ' . $this->table . ' ' . $this->tableAlias . '
            JOIN users u ON ' . $this->tableAlias . '.user_id = u.id
            WHERE ' . $this->tableAlias . '.status = :status
            AND (
                LOWER(' . $this->tableAlias . '.content) LIKE :searchContent
                OR LOWER(u.username) LIKE :searchUsername
                OR LOWER(CONCAT(u.first_name, " ", COALESCE(u.last_name, ""))) LIKE :searchName
            )
            ORDER BY ' . $this->tableAlias . '.created_at DESC
        ');
        
        $searchTerm = '%' . strtolower($search) . '%';
        $stmt->execute([
            'status' => $status,
            'searchContent' => $searchTerm,
            'searchUsername' => $searchTerm,
            'searchName' => $searchTerm
        ]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAll(): array
    {
        $stmt = $this->db->prepare('SELECT * FROM ' . $this->table . ' ORDER BY created_at DESC');
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllWithSearch(string $search): array
    {
        $stmt = $this->db->prepare('
            SELECT DISTINCT ' . $this->tableAlias . '.* 
            FROM ' . $this->table . ' ' . $this->tableAlias . '
            JOIN users u ON ' . $this->tableAlias . '.user_id = u.id
            WHERE 
                LOWER(' . $this->tableAlias . '.content) LIKE :searchContent
                OR LOWER(u.username) LIKE :searchUsername
                OR LOWER(CONCAT(u.first_name, " ", COALESCE(u.last_name, ""))) LIKE :searchName
            ORDER BY ' . $this->tableAlias . '.created_at DESC
        ');
        
        $searchTerm = '%' . strtolower($search) . '%';
        $stmt->execute([
            'searchContent' => $searchTerm,
            'searchUsername' => $searchTerm,
            'searchName' => $searchTerm
        ]);
        
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
