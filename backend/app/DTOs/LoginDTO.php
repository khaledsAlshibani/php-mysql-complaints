<?php

namespace App\DTOs;

class LoginDTO
{
    private string $username;
    private string $password;

    public function __construct(array $data)
    {
        $this->username = trim($data['username'] ?? '');
        $this->password = $data['password'] ?? '';
    }

    public function validate(): ?array
    {
        $errors = [];

        if (empty($this->username)) {
            $errors['username'] = 'Username is required';
        }

        if (empty($this->password)) {
            $errors['password'] = 'Password is required';
        }

        return empty($errors) ? null : $errors;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function toArray(): array
    {
        return [
            'username' => $this->username,
            'password' => $this->password
        ];
    }
}
