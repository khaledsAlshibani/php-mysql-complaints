<?php

namespace App\Services;

use App\Core\Response;
use App\DTO\LoginDTO;
use App\DTO\RegistrationDTO;
use App\DTO\PasswordUpdateDTO;
use App\DTO\ProfileUpdateDTO;

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

    public function handleDeleteAccount(array $data): array
    {
        if (!isset($data['password'])) {
            return Response::formatError(
                'Password is required',
                422,
                [['field' => 'password', 'issue' => 'Password is required']],
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

        // Verify password
        $user = $this->authService->verifyPassword($currentUser['id'], $data['password']);
        if (!$user) {
            return Response::formatError(
                'Current password is incorrect',
                400,
                [['field' => 'password', 'issue' => 'Current password is incorrect']],
                'INVALID_PASSWORD'
            );
        }

        // Delete the account
        $result = $this->authService->deleteAccount($currentUser['id']);
        if (!$result) {
            return Response::formatError(
                'Unable to delete your account. Please try again later.',
                500,
                [],
                'DELETE_ACCOUNT_FAILED'
            );
        }

        return Response::formatSuccess(null, 'Account deleted successfully');
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

    public function handleProfileUpdate(array $data): array
    {
        try {
            $profileUpdateDTO = new ProfileUpdateDTO($data);
            $validationErrors = $profileUpdateDTO->validate();
            
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

            return $this->authService->updateProfile($currentUser['id'], $profileUpdateDTO->toArray());
        } catch (\InvalidArgumentException $e) {
            return Response::formatError(
                $e->getMessage(),
                400,
                [],
                'INVALID_UPDATE_FIELDS'
            );
        }
    }
}
