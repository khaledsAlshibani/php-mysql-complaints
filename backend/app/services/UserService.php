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

    public function handlePhotoUpdate(array $photoFile): array
    {
        try {
            // Get current user
            $currentUser = $this->authService->getCurrentUser();
            if (!$currentUser) {
                return Response::formatError(
                    'Unauthorized',
                    401,
                    [],
                    'AUTHENTICATION_REQUIRED'
                );
            }

            // Validate file
            if ($photoFile['error'] !== UPLOAD_ERR_OK) {
                return Response::formatError(
                    'File upload failed',
                    400,
                    [],
                    'UPLOAD_ERROR'
                );
            }

            // Validate file type
            $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
            if (!in_array($photoFile['type'], $allowedTypes)) {
                return Response::formatError(
                    'Invalid file type. Only JPG, JPEG and PNG are allowed',
                    400,
                    [],
                    'INVALID_FILE_TYPE'
                );
            }

            // Validate file size (max 5MB)
            $maxSize = 5 * 1024 * 1024; // 5MB in bytes
            if ($photoFile['size'] > $maxSize) {
                return Response::formatError(
                    'File too large. Maximum size is 5MB',
                    400,
                    [],
                    'FILE_TOO_LARGE'
                );
            }

            // Create uploads directory if it doesn't exist
            $uploadDir = __DIR__ . '/../../storage/uploads/profiles';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            // Generate unique filename
            $extension = pathinfo($photoFile['name'], PATHINFO_EXTENSION);
            $filename = uniqid('profile_') . '.' . $extension;
            $filepath = $uploadDir . '/' . $filename;

            // Move uploaded file
            if (!move_uploaded_file($photoFile['tmp_name'], $filepath)) {
                return Response::formatError(
                    'Failed to save file',
                    500,
                    [],
                    'FILE_SAVE_ERROR'
                );
            }

            // Update user's photo path in database
            $relativePath = 'uploads/profiles/' . $filename;
            $result = $this->authService->updateProfile($currentUser['id'], ['photo_path' => $relativePath]);

            if ($result['status'] === 'error') {
                // If database update fails, delete the uploaded file
                unlink($filepath);
                return $result;
            }

            return Response::formatSuccess(
                ['photoPath' => $relativePath],
                'Profile photo updated successfully'
            );

        } catch (\Exception $e) {
            return Response::formatError(
                'Failed to update profile photo',
                500,
                [],
                'PHOTO_UPDATE_FAILED'
            );
        }
    }
}
