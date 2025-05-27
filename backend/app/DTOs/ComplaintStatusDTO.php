<?php

namespace App\DTOs;

class ComplaintStatusDTO
{
    private string $status;

    public function __construct(array $data)
    {
        $this->status = $data['status'] ?? '';
    }

    public function validate(): ?array
    {
        $errors = [];

        if (empty($this->status)) {
            $errors['status'] = 'Status is required';
        }

        if (!in_array($this->status, ['pending_no_feedback', 'pending_reviewed', 'resolved', 'ignored'])) {
            $errors['status'] = 'Invalid status value';
        }

        return empty($errors) ? null : $errors;
    }

    public function toArray(): array
    {
        return [
            'status' => $this->status
        ];
    }

    public function getStatus(): string
    {
        return $this->status;
    }
}