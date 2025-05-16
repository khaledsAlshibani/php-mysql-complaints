<?php

use App\Controllers\FeedbackController;
use App\Middleware\JWTAuthMiddleware;

$router->addRoute('GET', '/feedback/complaint/{id}', [FeedbackController::class, 'getAllForComplaint'], JWTAuthMiddleware::class);
$router->addRoute('GET', '/feedback/suggestion/{id}', [FeedbackController::class, 'getAllForSuggestion'], JWTAuthMiddleware::class);
$router->addRoute('GET', '/feedback/admin', [FeedbackController::class, 'getAllByAdmin'], JWTAuthMiddleware::class);
$router->addRoute('POST', '/feedback', [FeedbackController::class, 'create'], JWTAuthMiddleware::class);
$router->addRoute('GET', '/feedback/{id}', [FeedbackController::class, 'getById'], JWTAuthMiddleware::class);
$router->addRoute('PUT', '/feedback/{id}', [FeedbackController::class, 'update'], JWTAuthMiddleware::class);
$router->addRoute('DELETE', '/feedback/{id}', [FeedbackController::class, 'delete'], JWTAuthMiddleware::class);