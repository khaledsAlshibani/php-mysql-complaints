<?php

use App\Controllers\ComplaintController;
use App\Middleware\JWTAuthMiddleware;

$router->addRoute('GET', '/complaints', [ComplaintController::class, 'getAll'], JWTAuthMiddleware::class);
$router->addRoute('POST', '/complaints', [ComplaintController::class, 'create'], JWTAuthMiddleware::class);
$router->addRoute('GET', '/complaints/{id}', [ComplaintController::class, 'getById'], JWTAuthMiddleware::class);
$router->addRoute('PUT', '/complaints/{id}', [ComplaintController::class, 'update'], JWTAuthMiddleware::class);
$router->addRoute('PATCH', '/complaints/{id}/status', [ComplaintController::class, 'updateStatus'], JWTAuthMiddleware::class);
$router->addRoute('DELETE', '/complaints/{id}', [ComplaintController::class, 'delete'], JWTAuthMiddleware::class);

// Feedback routes
$router->addRoute('GET', '/complaints/{id}/feedback', [ComplaintController::class, 'getAllFeedback'], JWTAuthMiddleware::class);
$router->addRoute('POST', '/complaints/{id}/feedback', [ComplaintController::class, 'createFeedback'], JWTAuthMiddleware::class);
$router->addRoute('GET', '/complaints/{id}/feedback/{feedbackId}', [ComplaintController::class, 'getFeedbackById'], JWTAuthMiddleware::class);
$router->addRoute('PUT', '/complaints/{id}/feedback/{feedbackId}', [ComplaintController::class, 'updateFeedback'], JWTAuthMiddleware::class);
$router->addRoute('DELETE', '/complaints/{id}/feedback/{feedbackId}', [ComplaintController::class, 'deleteFeedback'], JWTAuthMiddleware::class);