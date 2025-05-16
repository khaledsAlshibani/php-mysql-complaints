<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Response;
use App\Services\SuggestionService;

class SuggestionController extends Controller
{
    private SuggestionService $suggestionService;

    public function __construct()
    {
        $this->suggestionService = new SuggestionService();
    }

    public function create(): void
    {
        $result = $this->suggestionService->create($this->getJsonInput());
        $this->handleResponse($result);
    }

    public function update(array $params): void
    {
        $result = $this->suggestionService->update($params, $this->getJsonInput());
        $this->handleResponse($result);
    }

    public function delete(array $params): void
    {
        $result = $this->suggestionService->delete($params);
        $this->handleResponse($result);
    }

    public function getById(array $params): void
    {
        $result = $this->suggestionService->getById($params);
        $this->handleResponse($result);
    }

    public function getAll(): void
    {
        $result = $this->suggestionService->getAll();
        $this->handleResponse($result);
    }

    public function getAllAdmin(): void
    {
        $result = $this->suggestionService->getAllAdmin();
        $this->handleResponse($result);
    }

    public function getByStatus(array $params): void
    {
        $result = $this->suggestionService->getByStatus($params);
        $this->handleResponse($result);
    }

    public function updateStatus(array $params): void
    {
        $result = $this->suggestionService->updateStatus($params, $this->getJsonInput());
        $this->handleResponse($result);
    }

    private function handleResponse(array $result): void
    {
        if ($result['status'] === 'error') {
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
}
