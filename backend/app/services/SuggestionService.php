<?php

namespace App\Services;

use App\Core\Response;
use App\Models\Suggestion;
use App\DTO\SuggestionDTO;

class SuggestionService
{
    private Suggestion $suggestion;
    private AuthService $authService;

    public function __construct()
    {
        $this->suggestion = new Suggestion();
        $this->authService = new AuthService();
    }

    public function create(array $data): array
    {
        $user = $this->authService->getCurrentUser();
        if (!$user) {
            return Response::formatError(
                'Authentication required',
                401,
                [],
                'AUTHENTICATION_REQUIRED'
            );
        }

        if (!$data) {
            return Response::formatError(
                'Invalid request payload',
                400,
                [],
                'INVALID_PAYLOAD'
            );
        }

        $data['user_id'] = $user['id'];
        $suggestionDTO = new SuggestionDTO($data);
        
        $validationErrors = $suggestionDTO->validate();
        if ($validationErrors) {
            return Response::formatError(
                'Validation failed',
                422,
                array_map(function($field, $message) {
                    return ['field' => $field, 'issue' => $message];
                }, array_keys($validationErrors), $validationErrors),
                'VALIDATION_ERROR'
            );
        }

        $suggestionData = $suggestionDTO->toArray();
        $newSuggestionId = $this->suggestion->create($suggestionData);
        
        if (!$newSuggestionId) {
            return Response::formatError(
                'Failed to create suggestion',
                500,
                [],
                'SUGGESTION_CREATION_FAILED'
            );
        }

        // Fetch the newly created suggestion with all its data
        $newSuggestion = $this->suggestion->find($newSuggestionId);
        if (!$newSuggestion) {
            return Response::formatError(
                'Failed to retrieve created suggestion',
                500,
                [],
                'SUGGESTION_RETRIEVAL_FAILED'
            );
        }

        // Format the suggestion data with user and feedback
        $suggestionUser = $newSuggestion->getUser();
        $feedback = $newSuggestion->getFeedback();

        return Response::formatSuccess([
            'id' => $newSuggestion->getId(),
            'content' => $newSuggestion->getContent(),
            'status' => $newSuggestion->getStatus(),
            'createdAt' => $newSuggestion->getCreatedAt(),
            'user' => [
                'id' => $suggestionUser->getId(),
                'username' => $suggestionUser->getUsername(),
                'fullName' => $suggestionUser->getFullName()
            ],
            'feedback' => $feedback
        ], 'Suggestion created successfully');
    }

    public function update(array $params, array $data): array
    {
        if (!isset($params['id'])) {
            return Response::formatError(
                'Suggestion ID is required',
                400,
                [],
                'MISSING_ID'
            );
        }

        $user = $this->authService->getCurrentUser();
        if (!$user) {
            return Response::formatError(
                'Authentication required',
                401,
                [],
                'AUTHENTICATION_REQUIRED'
            );
        }

        if (!$data) {
            return Response::formatError(
                'Invalid request payload',
                400,
                [],
                'INVALID_PAYLOAD'
            );
        }

        $suggestion = $this->suggestion->find((int)$params['id']);
        if (!$suggestion) {
            return Response::formatError(
                'Suggestion not found',
                404,
                [],
                'SUGGESTION_NOT_FOUND'
            );
        }

        if ($suggestion->getUserId() !== $user['id'] && $user['role'] !== 'admin') {
            return Response::formatError(
                'Not authorized',
                403,
                [],
                'UNAUTHORIZED_ACCESS'
            );
        }

        $data['id'] = $params['id'];
        $data['user_id'] = $suggestion->getUserId();
        $suggestionDTO = new SuggestionDTO($data);
        
        $validationErrors = $suggestionDTO->validate();
        if ($validationErrors) {
            return Response::formatError(
                'Validation failed',
                422,
                array_map(function($field, $message) {
                    return ['field' => $field, 'issue' => $message];
                }, array_keys($validationErrors), $validationErrors),
                'VALIDATION_ERROR'
            );
        }

        if (!$suggestion->update($suggestionDTO->toArray())) {
            return Response::formatError(
                'Failed to update suggestion',
                500,
                [],
                'SUGGESTION_UPDATE_FAILED'
            );
        }

        // Fetch the updated suggestion with all its data
        $updatedSuggestion = $this->suggestion->find((int)$params['id']);
        if (!$updatedSuggestion) {
            return Response::formatError(
                'Failed to retrieve updated suggestion',
                500,
                [],
                'SUGGESTION_RETRIEVAL_FAILED'
            );
        }

        // Format the suggestion data with user and feedback
        $suggestionUser = $updatedSuggestion->getUser();
        $feedback = $updatedSuggestion->getFeedback();

        return Response::formatSuccess([
            'id' => $updatedSuggestion->getId(),
            'content' => $updatedSuggestion->getContent(),
            'status' => $updatedSuggestion->getStatus(),
            'createdAt' => $updatedSuggestion->getCreatedAt(),
            'user' => [
                'id' => $suggestionUser->getId(),
                'username' => $suggestionUser->getUsername(),
                'fullName' => $suggestionUser->getFullName()
            ],
            'feedback' => $feedback
        ], 'Suggestion updated successfully');
    }

    public function delete(array $params): array
    {
        if (!isset($params['id'])) {
            return Response::formatError(
                'Suggestion ID is required',
                400,
                [],
                'MISSING_ID'
            );
        }

        $user = $this->authService->getCurrentUser();
        if (!$user) {
            return Response::formatError(
                'Authentication required',
                401,
                [],
                'AUTHENTICATION_REQUIRED'
            );
        }

        $suggestion = $this->suggestion->find((int)$params['id']);
        if (!$suggestion) {
            return Response::formatError(
                'Suggestion not found',
                404,
                [],
                'SUGGESTION_NOT_FOUND'
            );
        }

        if ($suggestion->getUserId() !== $user['id'] && $user['role'] !== 'admin') {
            return Response::formatError(
                'Not authorized',
                403,
                [],
                'UNAUTHORIZED_ACCESS'
            );
        }

        if (!$suggestion->delete()) {
            return Response::formatError(
                'Failed to delete suggestion',
                500,
                [],
                'SUGGESTION_DELETE_FAILED'
            );
        }

        return Response::formatSuccess(null, 'Suggestion deleted successfully');
    }

    public function getById(array $params): array
    {
        if (!isset($params['id'])) {
            return Response::formatError(
                'Suggestion ID is required',
                400,
                [],
                'MISSING_ID'
            );
        }

        $user = $this->authService->getCurrentUser();
        if (!$user) {
            return Response::formatError(
                'Authentication required',
                401,
                [],
                'AUTHENTICATION_REQUIRED'
            );
        }

        $suggestion = $this->suggestion->find((int)$params['id']);
        if (!$suggestion) {
            return Response::formatError(
                'Suggestion not found',
                404,
                [],
                'SUGGESTION_NOT_FOUND'
            );
        }

        if ($suggestion->getUserId() !== $user['id'] && !in_array($user['role'], ['admin', 'staff'])) {
            return Response::formatError(
                'Not authorized',
                403,
                [],
                'UNAUTHORIZED_ACCESS'
            );
        }

        $suggestionUser = $suggestion->getUser();
        $feedback = $suggestion->getFeedback();

        return Response::formatSuccess([
            'id' => $suggestion->getId(),
            'content' => $suggestion->getContent(),
            'status' => $suggestion->getStatus(),
            'createdAt' => $suggestion->getCreatedAt(),
            'user' => [
                'id' => $suggestionUser->getId(),
                'username' => $suggestionUser->getUsername(),
                'fullName' => $suggestionUser->getFullName()
            ],
            'feedback' => $feedback
        ]);
    }

    public function getAll(): array
    {
        $user = $this->authService->getCurrentUser();
        if (!$user) {
            return Response::formatError(
                'Authentication required',
                401,
                [],
                'AUTHENTICATION_REQUIRED'
            );
        }

        $rawSuggestions = $user['role'] === 'admin' ? 
            $this->suggestion->getAll() :
            $this->suggestion->getAllByUser($user['id']);

        $suggestions = [];
        foreach ($rawSuggestions as $suggestionData) {
            $suggestion = $this->suggestion->find((int)$suggestionData['id']);
            if ($suggestion) {
                $suggestionUser = $suggestion->getUser();
                $feedback = $suggestion->getFeedback();

                $suggestions[] = [
                    'id' => $suggestion->getId(),
                    'content' => $suggestion->getContent(),
                    'status' => $suggestion->getStatus(),
                    'createdAt' => $suggestion->getCreatedAt(),
                    'user' => [
                        'id' => $suggestionUser->getId(),
                        'username' => $suggestionUser->getUsername(),
                        'fullName' => $suggestionUser->getFullName()
                    ],
                    'feedback' => $feedback
                ];
            }
        }

        return Response::formatSuccess($suggestions);
    }

    public function getAllAdmin(): array
    {
        $user = $this->authService->getCurrentUser();
        if (!$user) {
            return Response::formatError(
                'Authentication required',
                401,
                [],
                'AUTHENTICATION_REQUIRED'
            );
        }

        if ($user['role'] !== 'admin') {
            return Response::formatError(
                'Access denied',
                403,
                [],
                'ACCESS_DENIED'
            );
        }

        $rawSuggestions = $this->suggestion->getAll();
        $suggestions = [];
        
        foreach ($rawSuggestions as $suggestionData) {
            $suggestion = $this->suggestion->find((int)$suggestionData['id']);
            if ($suggestion) {
                $suggestionUser = $suggestion->getUser();
                $feedback = $suggestion->getFeedback();

                $suggestions[] = [
                    'id' => $suggestion->getId(),
                    'content' => $suggestion->getContent(),
                    'status' => $suggestion->getStatus(),
                    'createdAt' => $suggestion->getCreatedAt(),
                    'user' => [
                        'id' => $suggestionUser->getId(),
                        'username' => $suggestionUser->getUsername(),
                        'fullName' => $suggestionUser->getFullName()
                    ],
                    'feedback' => $feedback
                ];
            }
        }

        return Response::formatSuccess($suggestions);
    }

    public function getByStatus(array $params): array
    {
        if (!isset($params['status'])) {
            return Response::formatError(
                'Status parameter is required',
                400,
                [],
                'MISSING_STATUS'
            );
        }

        $user = $this->authService->getCurrentUser();
        if (!$user) {
            return Response::formatError(
                'Authentication required',
                401,
                [],
                'AUTHENTICATION_REQUIRED'
            );
        }

        if ($user['role'] !== 'admin') {
            return Response::formatError(
                'Access denied',
                403,
                [],
                'ACCESS_DENIED'
            );
        }

        $rawSuggestions = $this->suggestion->getAllByStatus($params['status']);
        $suggestions = [];
        
        foreach ($rawSuggestions as $suggestionData) {
            $suggestion = $this->suggestion->find((int)$suggestionData['id']);
            if ($suggestion) {
                $suggestionUser = $suggestion->getUser();
                $feedback = $suggestion->getFeedback();

                $suggestions[] = [
                    'id' => $suggestion->getId(),
                    'content' => $suggestion->getContent(),
                    'status' => $suggestion->getStatus(),
                    'createdAt' => $suggestion->getCreatedAt(),
                    'user' => [
                        'id' => $suggestionUser->getId(),
                        'username' => $suggestionUser->getUsername(),
                        'fullName' => $suggestionUser->getFullName()
                    ],
                    'feedback' => $feedback
                ];
            }
        }

        return Response::formatSuccess($suggestions);
    }

    public function updateStatus(array $params, array $data): array
    {
        if (!isset($params['id'])) {
            return Response::formatError(
                'Suggestion ID is required',
                400,
                [],
                'MISSING_ID'
            );
        }

        if (!$data || !isset($data['status'])) {
            return Response::formatError(
                'Status is required',
                400,
                [],
                'MISSING_STATUS'
            );
        }

        $user = $this->authService->getCurrentUser();
        if (!$user) {
            return Response::formatError(
                'Authentication required',
                401,
                [],
                'AUTHENTICATION_REQUIRED'
            );
        }

        if ($user['role'] !== 'admin') {
            return Response::formatError(
                'Access denied',
                403,
                [],
                'ACCESS_DENIED'
            );
        }

        return $this->update($params, ['status' => $data['status']]);
    }
}
