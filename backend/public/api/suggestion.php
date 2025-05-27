<?php

use App\Controllers\SuggestionController;
use App\Middleware\JWTAuthMiddleware;

$router->addRoute('GET', '/suggestions', [SuggestionController::class, 'getAll'], JWTAuthMiddleware::class);
$router->addRoute('POST', '/suggestions', [SuggestionController::class, 'create'], JWTAuthMiddleware::class);
$router->addRoute('GET', '/suggestions/{id}', [SuggestionController::class, 'getById'], JWTAuthMiddleware::class);
$router->addRoute('PUT', '/suggestions/{id}', [SuggestionController::class, 'update'], JWTAuthMiddleware::class);
$router->addRoute('PATCH', '/suggestions/{id}/status', [SuggestionController::class, 'updateStatus'], JWTAuthMiddleware::class);
$router->addRoute('DELETE', '/suggestions/{id}', [SuggestionController::class, 'delete'], JWTAuthMiddleware::class);

// Feedback routes
$router->addRoute('GET', '/suggestions/{id}/feedback', [SuggestionController::class, 'getAllFeedback'], JWTAuthMiddleware::class);
$router->addRoute('POST', '/suggestions/{id}/feedback', [SuggestionController::class, 'createFeedback'], JWTAuthMiddleware::class);
$router->addRoute('GET', '/suggestions/{id}/feedback/{feedbackId}', [SuggestionController::class, 'getFeedbackById'], JWTAuthMiddleware::class);
$router->addRoute('PUT', '/suggestions/{id}/feedback/{feedbackId}', [SuggestionController::class, 'updateFeedback'], JWTAuthMiddleware::class);
$router->addRoute('DELETE', '/suggestions/{id}/feedback/{feedbackId}', [SuggestionController::class, 'deleteFeedback'], JWTAuthMiddleware::class);