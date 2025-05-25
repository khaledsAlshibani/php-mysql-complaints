<?php

namespace App\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use UnexpectedValueException;

class JWTService
{
    private string $secret;
    private int $accessTokenExpiry;
    private int $refreshTokenExpiry;
    private bool $isSecureEnvironment;

    public function __construct()
    {
        $this->secret = $_ENV['JWT_SECRET'] ?? throw new \RuntimeException('JWT_SECRET not set in environment');
        $this->accessTokenExpiry = 60 * 15; // 15 minutes
        $this->refreshTokenExpiry = 60 * 60 * 24 * 30; // 30 days
        $this->isSecureEnvironment = ($_ENV['APP_ENV'] ?? 'development') !== 'development';
    }

    private function getCookieOptions(int $expiry): array
    {
        return [
            'expires' => time() + $expiry,
            'path' => '/',
            'httponly' => true,
            'secure' => $this->isSecureEnvironment,
            'samesite' => $this->isSecureEnvironment ? 'Strict' : 'Lax'
        ];
    }

    /**
     * Validates the format of a JWT token
     * 
     * @param string|null $token The JWT token to validate
     * @return bool True if the token has valid format, false otherwise
     */
    private function isValidTokenFormat(?string $token): bool
    {
        if (!$token) {
            return false;
        }

        return (bool) preg_match('/^[a-zA-Z0-9\-\_\=]+\.[a-zA-Z0-9\-\_\=]+\.[a-zA-Z0-9\-\_\=]+$/', $token);
    }

    public function hasRequiredAuthCookies(bool $checkRefreshToken = false): bool
    {
        $hasAccessToken = isset($_COOKIE['access_token']) ||
            (isset($_SERVER['HTTP_AUTHORIZATION']) && strpos($_SERVER['HTTP_AUTHORIZATION'], 'Bearer') !== false) ||
            (isset($_SERVER['HTTP_COOKIE']) && strpos($_SERVER['HTTP_COOKIE'], 'access_token=') !== false);

        if (!$hasAccessToken) {
            return false;
        }

        // If refresh token check is required
        if ($checkRefreshToken) {
            return isset($_COOKIE['refresh_token']) ||
                (isset($_SERVER['HTTP_COOKIE']) && strpos($_SERVER['HTTP_COOKIE'], 'refresh_token=') !== false);
        }

        return true;
    }

    public function generateAccessToken(array $payload): string
    {
        $issuedAt = time();
        $expire = $issuedAt + $this->accessTokenExpiry;

        $tokenPayload = array_merge($payload, [
            'iat' => $issuedAt,
            'exp' => $expire,
            'type' => 'access'
        ]);

        return JWT::encode($tokenPayload, $this->secret, 'HS256');
    }

    public function generateRefreshToken(array $payload): string
    {
        $issuedAt = time();
        $expire = $issuedAt + $this->refreshTokenExpiry;

        $tokenPayload = array_merge($payload, [
            'iat' => $issuedAt,
            'exp' => $expire,
            'type' => 'refresh'
        ]);

        return JWT::encode($tokenPayload, $this->secret, 'HS256');
    }

    public function verifyToken(?string $token): ?array
    {
        if (!$this->isValidTokenFormat($token)) {
            return null;
        }

        try {
            $decoded = JWT::decode($token, new Key($this->secret, 'HS256'));
            return (array) $decoded;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getTokenFromHeader(): ?string
    {
        if (!$this->hasRequiredAuthCookies()) {
            return null;
        }

        if (isset($_COOKIE['access_token'])) {
            return $_COOKIE['access_token'];
        }

        return null;
    }

    public function setAccessTokenCookie(string $token): void
    {
        setcookie('access_token', $token, $this->getCookieOptions($this->accessTokenExpiry));
    }

    public function setRefreshTokenCookie(string $token): void
    {
        setcookie('refresh_token', $token, $this->getCookieOptions($this->refreshTokenExpiry));
    }

    public function clearTokenCookies(): void
    {
        setcookie('access_token', '', $this->getCookieOptions(-3600));
        setcookie('refresh_token', '', $this->getCookieOptions(-3600));
    }

    public function getRefreshTokenFromCookie(): ?string
    {
        if (!isset($_COOKIE['refresh_token'])) {
            return null;
        }

        $token = $_COOKIE['refresh_token'];

        if (!$this->isValidTokenFormat($token)) {
            return null;
        }

        return $token;
    }
}
