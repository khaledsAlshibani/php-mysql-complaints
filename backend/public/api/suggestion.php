<?php

use App\Controllers\SuggestionController;
use App\Middleware\JWTAuthMiddleware;

$router->addRoute('GET', '/suggestions/admin/all', [SuggestionController::class, 'getAllAdmin'], JWTAuthMiddleware::class);
$router->addRoute('GET', '/suggestions/admin/status/{status}', [SuggestionController::class, 'getByStatus'], JWTAuthMiddleware::class);
$router->addRoute('GET', '/suggestions', [SuggestionController::class, 'getAll'], JWTAuthMiddleware::class);
$router->addRoute('POST', '/suggestions', [SuggestionController::class, 'create'], JWTAuthMiddleware::class);
$router->addRoute('GET', '/suggestions/{id}', [SuggestionController::class, 'getById'], JWTAuthMiddleware::class);
$router->addRoute('PUT', '/suggestions/{id}', [SuggestionController::class, 'update'], JWTAuthMiddleware::class);
$router->addRoute('PUT', '/suggestions/{id}/status', [SuggestionController::class, 'updateStatus'], JWTAuthMiddleware::class);
$router->addRoute('DELETE', '/suggestions/{id}', [SuggestionController::class, 'delete'], JWTAuthMiddleware::class);