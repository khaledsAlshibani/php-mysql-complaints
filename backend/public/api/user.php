<?php

use App\Controllers\UserController;
use App\Middleware\JWTAuthMiddleware;

$router->addRoute('GET', '/users/profile', [UserController::class, 'getProfile'], JWTAuthMiddleware::class);
$router->addRoute('PUT', '/users/password', [UserController::class, 'updatePassword'], JWTAuthMiddleware::class);
