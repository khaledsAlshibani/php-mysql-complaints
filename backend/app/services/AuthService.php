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

        return Response::formatSuccess(
            [
                'id' => $user['id'],
                'username' => $user['username'],
                'firstName' => $user['first_name'],
                'lastName' => $user['last_name'],
                'role' => $user['role']
            ],
            'Login successful'
        );
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
            
            // Handle empty last_name
            $userData['last_name'] = empty($userData['last_name']) ? null : $userData['last_name'];
            
            $stmt = $this->db->prepare('
                INSERT INTO users (username, password, first_name, last_name, birth_date, role)
                VALUES (:username, :password, :first_name, :last_name, :birth_date, :role)
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

            $responseData = [
                'id' => $newUser['id'],
                'username' => $newUser['username'],
                'firstName' => $newUser['first_name'],
                'lastName' => $newUser['last_name'],
                'role' => $newUser['role']
            ];
            
            Logger::getInstance()->debug('User registration - final response data', ['responseData' => $responseData]);

            $this->db->commit();

            return Response::formatSuccess($responseData, 'User registered successfully');
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

        $accessToken = $this->jwtService->generateAccessToken($tokenPayload);
        $this->jwtService->setAccessTokenCookie($accessToken);

        return Response::formatSuccess(null, 'Token refreshed successfully');
    }

    public function logout(): array
    {
        $this->jwtService->clearTokenCookies();
        $this->sessionService->clear();
        $this->sessionService->destroy();

        return Response::formatSuccess(null, 'Logged out successfully');
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
}
