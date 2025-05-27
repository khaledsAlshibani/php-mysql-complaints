<?php

namespace App\DTOs;

/**
 * Abstract DTO for handling status updates of user submissions.
 *
 * Validates status values shared across multiple submission types
 * such as complaints and suggestions.
 *
 * @package App\DTOs
 */
abstract class UserSubmissionStatusDTO
{
    protected string $status;

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
