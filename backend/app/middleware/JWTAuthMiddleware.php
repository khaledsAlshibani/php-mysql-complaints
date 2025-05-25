<?php

namespace App\Middleware;

use App\Services\AuthService;
use App\Services\JWTService;
use App\Core\Response;

class JWTAuthMiddleware
{
    private AuthService $authService;
    private JWTService $jwtService;

    public function __construct()
    {
        $this->authService = new AuthService();
        $this->jwtService = new JWTService();
    }

    public function handle(): bool
    {
        if (!$this->jwtService->hasRequiredAuthCookies()) {
            Response::sendAuthenticationError();
            return false;
        }

        if (!$this->authService->verifyAuthentication()) {
            Response::sendAuthenticationError();
            return false;
        }

        return true;
    }
}
