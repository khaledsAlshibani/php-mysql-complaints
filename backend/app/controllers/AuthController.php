<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Response;
use App\Services\AuthService;

class AuthController extends Controller
{
    private AuthService $authService;

    public function __construct()
    {
        $this->authService = new AuthService();
    }

    public function login(): void
    {
        $data = $this->getJsonInput();
        if (!$data) {
            Response::sendError('Invalid request payload', 400);
            return;
        }

        $result = $this->authService->login($data['username'], $data['password']);
        if (isset($result['status']) && $result['status'] === 'error') {
            Response::sendError(
                $result['error']['message'],
                $result['error']['code'],
                $result['error']['details'] ?? [],
                $result['error']['errorCode']
            );
            return;
        }

        Response::sendSuccess($result['data'], $result['message']);
    }

    public function register(): void
    {
        $data = $this->getJsonInput();
        if (!$data) {
            Response::sendError('Invalid request payload', 400);
            return;
        }

        $result = $this->authService->register($data);
        if (isset($result['status']) && $result['status'] === 'error') {
            Response::sendError(
                $result['error']['message'],
                $result['error']['code'],
                $result['error']['details'] ?? [],
                $result['error']['errorCode']
            );
            return;
        }

        Response::sendSuccess($result['data'], $result['message'], 201);
    }

    public function logout(): void
    {
        $result = $this->authService->logout();
        Response::sendSuccess(null, $result['message']);
    }

    public function refreshToken(): void
    {
        $result = $this->authService->refreshToken();
        if (isset($result['status']) && $result['status'] === 'error') {
            Response::sendError(
                $result['error']['message'],
                $result['error']['code'],
                [],
                $result['error']['errorCode']
            );
            return;
        }

        Response::sendSuccess($result['data'], $result['message']);
    }
} 