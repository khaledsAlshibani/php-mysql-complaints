<?php

namespace App\DTOs;

class FeedbackDTO
{
    private ?int $id;
    private int $adminId;
    private ?int $complaintId;
    private ?int $suggestionId;
    private string $content;

    public function __construct(array $data)
    {
        $this->id = isset($data['id']) ? (int)$data['id'] : null;
        $this->adminId = (int)($data['admin_id'] ?? 0);
        $this->complaintId = isset($data['complaint_id']) ? (int)$data['complaint_id'] : null;
        $this->suggestionId = isset($data['suggestion_id']) ? (int)$data['suggestion_id'] : null;
        $this->content = trim($data['content'] ?? '');
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

        if ($this->adminId <= 0) {
            $errors['admin_id'] = 'Valid admin ID is required';
        }

        if (!$this->complaintId && !$this->suggestionId) {
            $errors['reference'] = 'Either complaint_id or suggestion_id must be provided';
        }

        if ($this->complaintId && $this->suggestionId) {
            $errors['reference'] = 'Cannot provide both complaint_id and suggestion_id';
        }

        return empty($errors) ? null : $errors;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'admin_id' => $this->adminId,
            'complaint_id' => $this->complaintId,
            'suggestion_id' => $this->suggestionId,
            'content' => $this->content
        ];
    }

    public function getId(): ?int
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
} 