<?php

namespace App\Core;

use PDO;

abstract class Model
{
    protected PDO $db;
    protected string $table;
    protected array $fillable = [];
    protected string $primaryKey = 'id';

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function find(int $id): ?static
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id");
        $stmt->execute(['id' => $id]);

        if ($record = $stmt->fetch(PDO::FETCH_ASSOC)) {
            return $this->mapToObject($record);
        }

        return null;
    }

    public function create(array $data): bool|int
    {
        $fields = array_intersect_key($data, array_flip($this->fillable));
        $columns = implode(', ', array_keys($fields));
        $values = implode(', ', array_map(fn($field) => ":$field", array_keys($fields)));

        $stmt = $this->db->prepare("INSERT INTO {$this->table} ($columns) VALUES ($values)");

        $success = $stmt->execute($fields);
        return $success ? (int)$this->db->lastInsertId() : false;
    }

    public function update(array $data): bool
    {
        $fields = array_intersect_key($data, array_flip($this->fillable));
        $setClause = implode(', ', array_map(fn($field) => "$field = :$field", array_keys($fields)));
        
        $stmt = $this->db->prepare("UPDATE {$this->table} SET $setClause WHERE {$this->primaryKey} = :id");
        
        return $stmt->execute(array_merge($fields, ['id' => $this->getId()]));
    }

    public function delete(): bool
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id");
        return $stmt->execute(['id' => $this->getId()]);
    }

    public function all(): array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} ORDER BY created_at DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findBy(string $field, $value): array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE $field = :value ORDER BY created_at DESC");
        $stmt->execute(['value' => $value]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findOneBy(string $field, $value): ?static
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE $field = :value");
        $stmt->execute(['value' => $value]);

        if ($record = $stmt->fetch(PDO::FETCH_ASSOC)) {
            return $this->mapToObject($record);
        }

        return null;
    }

    abstract protected function mapToObject(array $data): static;

    abstract public function getId(): int;
}
