<?php

namespace App\Services;

use PDO;
use App\Core\Database;
use App\Core\Response;
use App\Core\Logger;

class AuthService
{
    private PDO $db;
    private JWTService $jwtService;
    private SessionService $sessionService;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->jwtService = new JWTService();
        $this->sessionService = new SessionService();
    }

    public function login(string $username, string $password): array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE username = ?');
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password'])) {
            return Response::formatError(
                'Invalid username or password',
                401,
                [],
                'INVALID_CREDENTIALS'
            );
        }

        $tokenPayload = [
            'sub' => $user['id'],
            'username' => $user['username'],
            'role' => $user['role']
        ];

        $accessToken = $this->jwtService->generateAccessToken($tokenPayload);
        $refreshToken = $this->jwtService->generateRefreshToken($tokenPayload);

        $this->jwtService->setAccessTokenCookie($accessToken);
        $this->jwtService->setRefreshTokenCookie($refreshToken);

        return Response::formatSuccess($this->createAuthRes($user), 'Login successful');
    }

    public function register(array $userData): array
    {
        try {
            $this->db->beginTransaction();

            // prevent registration with admin role
            if (isset($userData['role']) && strtolower($userData['role']) === 'admin') {
                return Response::formatError(
                    'Cannot register as admin',
                    403,
                    [],
                    'ADMIN_REGISTRATION_FORBIDDEN'
                );
            }

            if ($this->isUsernameExists($userData['username'])) {
                return Response::formatError(
                    'Username already exists',
                    400,
                    [],
                    'USERNAME_EXISTS'
                );
            }

            // force role to be 'user' by default
            $userData['role'] = 'user';
            $userData['password'] = password_hash($userData['password'], PASSWORD_DEFAULT);

            $userData['last_name'] = empty($userData['last_name']) ? null : $userData['last_name'];
            $userData['photo_path'] = 'uploads/profiles/defaults/user.webp';

            $stmt = $this->db->prepare('
                INSERT INTO users (username, password, first_name, last_name, birth_date, role, photo_path)
                VALUES (:username, :password, :first_name, :last_name, :birth_date, :role, :photo_path)
            ');

            $stmt->execute($userData);
            $userId = $this->db->lastInsertId();

            $newUser = $this->getUserById($userId);

            $tokenPayload = [
                'sub' => $newUser['id'],
                'username' => $newUser['username'],
                'role' => $newUser['role']
            ];

            $accessToken = $this->jwtService->generateAccessToken($tokenPayload);
            $refreshToken = $this->jwtService->generateRefreshToken($tokenPayload);

            $this->jwtService->setAccessTokenCookie($accessToken);
            $this->jwtService->setRefreshTokenCookie($refreshToken);

            $this->db->commit();

            return Response::formatSuccess($this->createAuthRes($newUser), 'User registered successfully');
        } catch (\PDOException $e) {
            $this->db->rollBack();
            return Response::formatError(
                'Registration failed',
                500,
                ['details' => $e->getMessage()],
                'REGISTRATION_FAILED'
            );
        }
    }

    public function updatePassword(int $userId, string $currentPassword, string $newPassword): array
    {
        try {
            $this->db->beginTransaction();

            $user = $this->getUserById($userId);
            if (!$user || !password_verify($currentPassword, $user['password'])) {
                return Response::formatError(
                    'Current password is incorrect',
                    400,
                    [],
                    'INVALID_CURRENT_PASSWORD'
                );
            }

            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $this->db->prepare('UPDATE users SET password = ? WHERE id = ?');
            $stmt->execute([$hashedPassword, $userId]);

            $this->db->commit();

            return Response::formatSuccess(null, 'Password updated successfully');
        } catch (\PDOException $e) {
            $this->db->rollBack();
            return Response::formatError(
                'Failed to update password',
                500,
                ['details' => $e->getMessage()],
                'PASSWORD_UPDATE_FAILED'
            );
        }
    }

    public function refreshToken(): array
    {
        $refreshToken = $this->jwtService->getRefreshTokenFromCookie();
        if (!$refreshToken) {
            return Response::formatError(
                'No refresh token provided',
                401,
                [],
                'REFRESH_TOKEN_MISSING'
            );
        }

        $payload = $this->jwtService->verifyToken($refreshToken);
        if (!$payload || $payload['type'] !== 'refresh') {
            return Response::formatError(
                'Invalid refresh token',
                401,
                [],
                'INVALID_REFRESH_TOKEN'
            );
        }

        $tokenPayload = [
            'sub' => $payload['sub'],
            'username' => $payload['username'],
            'role' => $payload['role']
        ];

        $user = $this->getUserById($payload['sub']);

        $accessToken = $this->jwtService->generateAccessToken($tokenPayload);
        $this->jwtService->setAccessTokenCookie($accessToken);
        Logger::getInstance()->info('user ' . print_r($this->createAuthRes($user), true));
        return Response::formatSuccess($this->createAuthRes($user), 'Token refreshed successfully');
    }

    public function logout(): array
    {
        $this->jwtService->clearTokenCookies();
        $this->sessionService->clear();
        $this->sessionService->destroy();

        return Response::formatSuccess(null, 'Logged out successfully');
    }

    private function createAuthRes($newUser): ?array
    {
        return [
            'id' => $newUser['id'],
            'username' => $newUser['username'],
            'firstName' => $newUser['first_name'],
            'lastName' => $newUser['last_name'],
            'role' => $newUser['role']
        ];
    }

    public function getCurrentUser(): ?array
    {
        $token = $this->jwtService->getTokenFromHeader();
        if (!$token) {
            return null;
        }

        $payload = $this->jwtService->verifyToken($token);
        if (!$payload) {
            return null;
        }

        $user = $this->getUserById($payload['sub']);
        if (!$user) {
            return null;
        }

        return [
            'id' => $user['id'],
            'username' => $user['username'],
            'firstName' => $user['first_name'],
            'lastName' => $user['last_name'],
            'birthDate' => $user['birth_date'],
            'photoPath' => $user['photo_path'],
            'role' => $user['role'],
            'createdAt' => $user['created_at']
        ];
    }

    public function verifyAuthentication(): bool
    {
        $token = $this->jwtService->getTokenFromHeader();
        if (!$token) {
            return false;
        }

        $payload = $this->jwtService->verifyToken($token);
        return $payload !== null;
    }

    private function isUsernameExists(string $username): bool
    {
        $stmt = $this->db->prepare('SELECT id FROM users WHERE username = ?');
        $stmt->execute([$username]);
        return (bool)$stmt->fetch();
    }

    private function getUserById(int $userId): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE id = ?');
        $stmt->execute([$userId]);
        return $stmt->fetch() ?: null;
    }

    public function verifyPassword(int $userId, string $password): bool
    {
        $user = $this->getUserById($userId);
        if (!$user) {
            return false;
        }

        return password_verify($password, $user['password']);
    }

    public function deleteAccount(int $userId): bool
    {
        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare('DELETE FROM users WHERE id = ?');
            $success = $stmt->execute([$userId]);

            if ($success) {
                $this->db->commit();
                $this->jwtService->clearTokenCookies();
                $this->sessionService->clear();
                $this->sessionService->destroy();
                return true;
            }

            $this->db->rollBack();
            return false;
        } catch (\PDOException $e) {
            $this->db->rollBack();
            Logger::getInstance()->error('Failed to delete account', [
                'userId' => $userId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    public function updateProfile(int $userId, array $data): array
    {
        try {
            $this->db->beginTransaction();

            $updateFields = [];
            $params = [];

            if (isset($data['first_name'])) {
                $updateFields[] = 'first_name = ?';
                $params[] = $data['first_name'];
            }
            if (isset($data['last_name'])) {
                $updateFields[] = 'last_name = ?';
                $params[] = $data['last_name'];
            }
            if (isset($data['birth_date'])) {
                $updateFields[] = 'birth_date = ?';
                $params[] = $data['birth_date'];
            }
            if (isset($data['photo_path'])) {
                $updateFields[] = 'photo_path = ?';
                $params[] = $data['photo_path'];
            }

            if (empty($updateFields)) {
                return Response::formatError(
                    'No fields to update',
                    400,
                    [],
                    'NO_FIELDS_TO_UPDATE'
                );
            }

            $params[] = $userId;
            $sql = 'UPDATE users SET ' . implode(', ', $updateFields) . ' WHERE id = ?';

            $stmt = $this->db->prepare($sql);
            $success = $stmt->execute($params);

            if (!$success) {
                $this->db->rollBack();
                return Response::formatError(
                    'Failed to update profile',
                    500,
                    [],
                    'PROFILE_UPDATE_FAILED'
                );
            }

            $this->db->commit();

            // Get updated user data
            $updatedUser = $this->getUserById($userId);
            if (!$updatedUser) {
                return Response::formatError(
                    'Failed to retrieve updated profile',
                    500,
                    [],
                    'PROFILE_RETRIEVAL_FAILED'
                );
            }

            return Response::formatSuccess(
                [
                    'id' => $updatedUser['id'],
                    'username' => $updatedUser['username'],
                    'firstName' => $updatedUser['first_name'],
                    'lastName' => $updatedUser['last_name'],
                    'birthDate' => $updatedUser['birth_date'],
                    'photoPath' => $updatedUser['photo_path'],
                    'role' => $updatedUser['role'],
                    'createdAt' => $updatedUser['created_at']
                ],
                'Profile updated successfully'
            );
        } catch (\PDOException $e) {
            $this->db->rollBack();
            Logger::getInstance()->error('Failed to update profile', [
                'userId' => $userId,
                'error' => $e->getMessage()
            ]);
            return Response::formatError(
                'Failed to update profile',
                500,
                [],
                'PROFILE_UPDATE_FAILED'
            );
        }
    }
}
