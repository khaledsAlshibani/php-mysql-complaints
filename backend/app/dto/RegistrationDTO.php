<?php

namespace App\DTO;

class RegistrationDTO
{
    private const PASSWORD_MIN_LENGTH = 8;
    
    private string $username;
    private string $password;
    private string $firstName;
    private string $lastName;
    private string $birthDate;
    private string $role;

    public function __construct(array $data)
    {
        $this->username = trim($data['username'] ?? '');
        $this->password = $data['password'] ?? '';
        $this->firstName = trim($data['first_name'] ?? '');
        $this->lastName = trim($data['last_name'] ?? '');
        $this->birthDate = trim($data['birth_date'] ?? '');
        $this->role = trim($data['role'] ?? 'user');
    }

    public function validate(): ?array
    {
        $errors = [];

        if (empty($this->username)) {
            $errors['username'] = 'Username is required';
        }

        $passwordErrors = $this->validatePassword();
        if ($passwordErrors) {
            $errors['password'] = $passwordErrors;
        }

        if (empty($this->firstName)) {
            $errors['first_name'] = 'First name is required';
        }

        if (empty($this->lastName)) {
            $errors['last_name'] = 'Last name is required';
        }

        if (empty($this->birthDate)) {
            $errors['birth_date'] = 'Birth date is required';
        } elseif (!strtotime($this->birthDate)) {
            $errors['birth_date'] = 'Invalid birth date format';
        }

        return empty($errors) ? null : $errors;
    }

    private function validatePassword(): ?array
    {
        $errors = [];
        
        if (strlen($this->password) < self::PASSWORD_MIN_LENGTH) {
            $errors[] = "Password must be at least " . self::PASSWORD_MIN_LENGTH . " characters long";
        }
        
        if (!preg_match('/[A-Z]/', $this->password)) {
            $errors[] = "Password must contain at least one uppercase letter";
        }
        
        if (!preg_match('/[a-z]/', $this->password)) {
            $errors[] = "Password must contain at least one lowercase letter";
        }
        
        if (!preg_match('/[0-9]/', $this->password)) {
            $errors[] = "Password must contain at least one number";
        }
        
        if (!preg_match('/[!@#$%^&*()\-_=+{};:,<.>]/', $this->password)) {
            $errors[] = "Password must contain at least one special character (!@#$%^&*()-_=+{};:,<.>)";
        }

        return empty($errors) ? null : $errors;
    }

    public function toArray(): array
    {
        return [
            'username' => $this->username,
            'password' => $this->password,
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'birth_date' => $this->birthDate,
            'role' => $this->role
        ];
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getBirthDate(): string
    {
        return $this->birthDate;
    }

    public function getRole(): string
    {
        return $this->role;
    }
}
