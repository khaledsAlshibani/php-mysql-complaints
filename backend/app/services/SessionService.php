<?php

namespace App\Services;

class SessionService
{
    public function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function destroy(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
    }

    public function setUserId(int $userId): void
    {
        $this->start();
        $_SESSION['user_id'] = $userId;
    }

    public function setUserRole(string $role): void
    {
        $this->start();
        $_SESSION['role'] = $role;
    }

    public function getUserId(): ?int
    {
        return $_SESSION['user_id'] ?? null;
    }

    public function getUserRole(): ?string
    {
        return $_SESSION['role'] ?? null;
    }

    public function isAuthenticated(): bool
    {
        return isset($_SESSION['user_id']);
    }

    public function clear(): void
    {
        $_SESSION = [];
    }

    public function getSessionId(): string
    {
        $this->start();
        return session_id();
    }

    public function setSessionId(string $sessionId): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }
        session_id($sessionId);
        $this->start();
    }
}
