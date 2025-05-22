<?php

use App\Controllers\UserController;
use App\Middleware\JWTAuthMiddleware;

$router->addRoute('GET', '/users/me', [UserController::class, 'getProfile'], JWTAuthMiddleware::class);
$router->addRoute('PUT', '/users/password', [UserController::class, 'updatePassword'], JWTAuthMiddleware::class);
$router->addRoute('DELETE', '/users/me', [UserController::class, 'deleteAccount'], JWTAuthMiddleware::class);
