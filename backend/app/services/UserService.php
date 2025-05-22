<?php

namespace App\Services;

use App\Core\Response;
use App\DTO\LoginDTO;
use App\DTO\RegistrationDTO;
use App\DTO\PasswordUpdateDTO;

class UserService
{
    private AuthService $authService;

    public function __construct()
    {
        $this->authService = new AuthService();
    }

    public function handleLogin(array $data): array
    {
        $loginDTO = new LoginDTO($data);
        $validationErrors = $loginDTO->validate();
        
        if ($validationErrors) {
            return Response::formatError(
                'Validation failed',
                422,
                array_map(function($field, $message) {
                    return ['field' => $field, 'issue' => $message];
                }, array_keys($validationErrors), $validationErrors),
                'VALIDATION_ERROR'
            );
        }

        return $this->authService->login($loginDTO->getUsername(), $loginDTO->getPassword());
    }

    public function handleRegistration(array $data): array
    {
        $registrationDTO = new RegistrationDTO($data);
        $validationErrors = $registrationDTO->validate();
        
        if ($validationErrors) {
            return Response::formatError(
                'Validation failed',
                422,
                array_map(function($field, $message) {
                    return ['field' => $field, 'issue' => $message];
                }, array_keys($validationErrors), $validationErrors),
                'VALIDATION_ERROR'
            );
        }

        return $this->authService->register($registrationDTO->toArray());
    }

    public function handlePasswordUpdate(array $data): array
    {
        $passwordUpdateDTO = new PasswordUpdateDTO($data);
        $validationErrors = $passwordUpdateDTO->validate();
        
        if ($validationErrors) {
            return Response::formatError(
                'Validation failed',
                422,
                array_map(function($field, $message) {
                    return ['field' => $field, 'issue' => $message];
                }, array_keys($validationErrors), $validationErrors),
                'VALIDATION_ERROR'
            );
        }

        $currentUser = $this->authService->getCurrentUser();
        if (!$currentUser) {
            return Response::formatError(
                'Unauthorized',
                401,
                [],
                'AUTHENTICATION_REQUIRED'
            );
        }

        return $this->authService->updatePassword(
            $currentUser['id'],
            $passwordUpdateDTO->getCurrentPassword(),
            $passwordUpdateDTO->getNewPassword()
        );
    }

    public function handleLogout(): array
    {
        return $this->authService->logout();
    }

    public function getUserProfile(): array
    {
        $user = $this->authService->getCurrentUser();
        if (!$user) {
            return Response::formatError(
                'Unauthorized',
                401,
                [],
                'AUTHENTICATION_REQUIRED'
            );
        }

        // Format user data to include all fields
        $userData = [
            'id' => $user['id'],
            'username' => $user['username'],
            'firstName' => $user['firstName'],
            'lastName' => $user['lastName'],
            'birthDate' => $user['birthDate'],
            'photoPath' => $user['photoPath'] ?? null,
            'role' => $user['role'],
            'createdAt' => $user['createdAt'] ?? null
        ];

        return Response::formatSuccess($userData, 'Profile retrieved successfully');
    }

    public function handleTokenRefresh(): array
    {
        return $this->authService->refreshToken();
    }
}
