<?php

use App\Controllers\AuthController;
use App\Middleware\JWTAuthMiddleware;

$router->addRoute('POST', '/auth/login', [AuthController::class, 'login']);
$router->addRoute('POST', '/auth/register', [AuthController::class, 'register']);
$router->addRoute('POST', '/auth/refresh', [AuthController::class, 'refreshToken']);
$router->addRoute('POST', '/auth/logout', [AuthController::class, 'logout'], JWTAuthMiddleware::class);