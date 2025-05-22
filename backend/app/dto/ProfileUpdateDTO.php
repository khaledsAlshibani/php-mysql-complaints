<?php

namespace App\DTO;

class ProfileUpdateDTO
{
    private string $firstName;
    private ?string $lastName;
    private string $birthDate;
    private ?string $photoPath;

    // List of fields that are allowed to be updated, not allowed to be updated here are: username, password, createdAt ..
    private static array $allowedFields = ['firstName', 'lastName', 'birthDate', 'photoPath'];

    public function __construct(array $data)
    {
        // Check for disallowed fields first
        $disallowedFields = array_diff(array_keys($data), self::$allowedFields);
        if (!empty($disallowedFields)) {
            throw new \InvalidArgumentException(
                'Cannot update the following field(s): ' . implode(', ', $disallowedFields)
            );
        }

        $this->firstName = $data['firstName'] ?? '';
        $this->lastName = $data['lastName'] ?? null;
        $this->birthDate = $data['birthDate'] ?? '';
        $this->photoPath = $data['photoPath'] ?? null;
    }

    public function validate(): array
    {
        $errors = [];

        if (empty($this->firstName)) {
            $errors['firstName'] = 'First name is required';
        } elseif (strlen($this->firstName) > 50) {
            $errors['firstName'] = 'First name cannot exceed 50 characters';
        }

        if (!empty($this->lastName) && strlen($this->lastName) > 50) {
            $errors['lastName'] = 'Last name cannot exceed 50 characters';
        }

        if (empty($this->birthDate)) {
            $errors['birthDate'] = 'Birth date is required';
        } elseif (!strtotime($this->birthDate)) {
            $errors['birthDate'] = 'Invalid birth date format';
        }

        return $errors;
    }

    public function toArray(): array
    {
        return [
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'birth_date' => $this->birthDate,
            'photo_path' => $this->photoPath
        ];
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function getBirthDate(): string
    {
        return $this->birthDate;
    }

    public function getPhotoPath(): ?string
    {
        return $this->photoPath;
    }
}
