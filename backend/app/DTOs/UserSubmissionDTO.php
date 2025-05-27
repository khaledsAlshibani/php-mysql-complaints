<?php

namespace App\DTOs;

/**
 * Abstract DTO for user submissions such as complaints and suggestions.
 *
 * Handles:
 * - ID mapping
 * - User ownership
 * - Content field
 * - Status validation
 *
 * @package App\DTOs
 */
abstract class UserSubmissionDTO
{
    protected ?int $id;
    protected int $userId;
    protected string $content;
    protected string $status;

    public function __construct(array $data)
    {
        $this->id = isset($data['id']) ? (int)$data['id'] : null;
        $this->userId = (int)($data['user_id'] ?? 0);
        $this->content = trim($data['content'] ?? '');
        $this->status = $data['status'] ?? 'pending_no_feedback';
    }

    public function validate(): ?array
    {
        $errors = [];

        if (empty($this->content)) {
            $errors['content'] = 'Content is required';
        }

        if (strlen($this->content) > 1000) {
            $errors['content'] = 'Content must not exceed 1000 characters';
        }

        if ($this->userId <= 0) {
            $errors['user_id'] = 'Valid user ID is required';
        }

        if (!in_array($this->status, ['pending_no_feedback', 'pending_reviewed', 'resolved', 'ignored'])) {
            $errors['status'] = 'Invalid status value';
        }

        return empty($errors) ? null : $errors;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->userId,
            'content' => $this->content,
            'status' => $this->status
        ];
    }

    public function getId(): ?int
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
}
