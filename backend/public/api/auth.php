<?php

use App\Controllers\UserController;
use App\Middleware\JWTAuthMiddleware;

$router->addRoute('POST', '/auth/login', [UserController::class, 'login']);
$router->addRoute('POST', '/auth/register', [UserController::class, 'register']);
$router->addRoute('POST', '/auth/refresh', [UserController::class, 'refreshToken']);
$router->addRoute('POST', '/auth/logout', [UserController::class, 'logout'], JWTAuthMiddleware::class);