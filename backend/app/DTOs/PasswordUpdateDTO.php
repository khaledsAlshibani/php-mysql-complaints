<?php

namespace App\DTOs;

class PasswordUpdateDTO
{
    private const PASSWORD_MIN_LENGTH = 8;
    
    private string $currentPassword;
    private string $newPassword;

    public function __construct(array $data)
    {
        $this->currentPassword = $data['current_password'] ?? '';
        $this->newPassword = $data['new_password'] ?? '';
    }

    public function validate(): ?array
    {
        $errors = [];

        if (empty($this->currentPassword)) {
            $errors['current_password'] = 'Current password is required';
        }

        if (empty($this->newPassword)) {
            $errors['new_password'] = 'New password is required';
        } else {
            $passwordErrors = $this->validateNewPassword();
            if ($passwordErrors) {
                $errors['new_password'] = $passwordErrors;
            }
        }

        return empty($errors) ? null : $errors;
    }

    private function validateNewPassword(): ?array
    {
        $errors = [];
        
        if (strlen($this->newPassword) < self::PASSWORD_MIN_LENGTH) {
            $errors[] = "Password must be at least " . self::PASSWORD_MIN_LENGTH . " characters long";
        }
        
        if (!preg_match('/[A-Z]/', $this->newPassword)) {
            $errors[] = "Password must contain at least one uppercase letter";
        }
        
        if (!preg_match('/[a-z]/', $this->newPassword)) {
            $errors[] = "Password must contain at least one lowercase letter";
        }
        
        if (!preg_match('/[0-9]/', $this->newPassword)) {
            $errors[] = "Password must contain at least one number";
        }
        
        if (!preg_match('/[!@#$%^&*()\-_=+{};:,<.>]/', $this->newPassword)) {
            $errors[] = "Password must contain at least one special character (!@#$%^&*()-_=+{};:,<.>)";
        }

        return empty($errors) ? null : $errors;
    }

    public function getCurrentPassword(): string
    {
        return $this->currentPassword;
    }

    public function getNewPassword(): string
    {
        return $this->newPassword;
    }

    public function toArray(): array
    {
        return [
            'current_password' => $this->currentPassword,
            'new_password' => $this->newPassword
        ];
    }
}
