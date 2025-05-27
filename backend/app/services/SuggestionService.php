<?php

namespace App\Services;

use App\Core\Response;
use App\Models\Suggestion;
use App\DTO\SuggestionDTO;
use App\DTO\SuggestionStatusDTO;
use App\Services\FeedbackService;

class SuggestionService
{
    private Suggestion $suggestion;
    private FeedbackService $feedbackService;

    public function __construct()
    {
        $this->suggestion = new Suggestion();
        $this->feedbackService = new FeedbackService();
    }

    public function create(array $data, int $userId): array
    {
        if (!$data) {
            return Response::formatError('Invalid request payload', 400);
        }

        $data['user_id'] = $userId;
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
                'COMPLAINT_CREATION_FAILED'
            );
        }

        // Fetch the newly created suggestion with all its data
        $newSuggestion = $this->suggestion->find($newSuggestionId);
        if (!$newSuggestion) {
            return Response::formatError(
                'Failed to retrieve created suggestion',
                500,
                [],
                'COMPLAINT_RETRIEVAL_FAILED'
            );
        }

        // Format the suggestion data with user and feedback
        $user = $newSuggestion->getUser();
        $feedback = $newSuggestion->getFeedback();

        return Response::formatSuccess([
            'id' => $newSuggestion->getId(),
            'content' => $newSuggestion->getContent(),
            'status' => $newSuggestion->getStatus(),
            'createdAt' => $newSuggestion->getCreatedAt(),
            'user' => [
                'id' => $user->getId(),
                'username' => $user->getUsername(),
                'fullName' => $user->getFullName()
            ],
            'feedback' => $feedback
        ], 'Suggestion created successfully');
    }

    public function update(int $id, array $data, int $userId, string $userRole): array
    {
        if (!$data) {
            return Response::formatError('Invalid request payload', 400);
        }

        $suggestion = $this->suggestion->find($id);
        if (!$suggestion) {
            return Response::formatError(
                'Suggestion not found',
                404,
                [],
                'COMPLAINT_NOT_FOUND'
            );
        }

        if ($suggestion->getUserId() !== $userId && $userRole !== 'admin') {
            return Response::formatError(
                'Not authorized',
                403,
                [],
                'UNAUTHORIZED_ACCESS'
            );
        }

        $data['id'] = $id;
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
                'COMPLAINT_UPDATE_FAILED'
            );
        }

        // Fetch the updated suggestion with all its data
        $updatedSuggestion = $this->suggestion->find($id);
        if (!$updatedSuggestion) {
            return Response::formatError(
                'Failed to retrieve updated suggestion',
                500,
                [],
                'COMPLAINT_RETRIEVAL_FAILED'
            );
        }

        // Format the suggestion data with user and feedback
        $user = $updatedSuggestion->getUser();
        $feedback = $updatedSuggestion->getFeedback();

        return Response::formatSuccess([
            'id' => $updatedSuggestion->getId(),
            'content' => $updatedSuggestion->getContent(),
            'status' => $updatedSuggestion->getStatus(),
            'createdAt' => $updatedSuggestion->getCreatedAt(),
            'user' => [
                'id' => $user->getId(),
                'username' => $user->getUsername(),
                'fullName' => $user->getFullName()
            ],
            'feedback' => $feedback
        ], 'Suggestion updated successfully');
    }

    public function updateStatus(int $id, array $data, int $userId, string $userRole): array
    {
        if (!$data || !isset($data['status'])) {
            return Response::formatError('Status is required', 400);
        }

        $suggestion = $this->suggestion->find($id);
        if (!$suggestion) {
            return Response::formatError(
                'Suggestion not found',
                404,
                [],
                'COMPLAINT_NOT_FOUND'
            );
        }

        if ($userRole !== 'admin') {
            return Response::formatError(
                'Not authorized',
                403,
                [],
                'UNAUTHORIZED_ACCESS'
            );
        }

        $statusDTO = new SuggestionStatusDTO($data);
        $validationErrors = $statusDTO->validate();
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

        if (!$suggestion->update($statusDTO->toArray())) {
            return Response::formatError(
                'Failed to update suggestion status',
                500,
                [],
                'COMPLAINT_UPDATE_FAILED'
            );
        }

        // Fetch the updated suggestion with all its data
        $updatedSuggestion = $this->suggestion->find($id);
        if (!$updatedSuggestion) {
            return Response::formatError(
                'Failed to retrieve updated suggestion',
                500,
                [],
                'COMPLAINT_RETRIEVAL_FAILED'
            );
        }

        // Format the suggestion data with user and feedback
        $user = $updatedSuggestion->getUser();
        $feedback = $updatedSuggestion->getFeedback();

        return Response::formatSuccess([
            'id' => $updatedSuggestion->getId(),
            'content' => $updatedSuggestion->getContent(),
            'status' => $updatedSuggestion->getStatus(),
            'createdAt' => $updatedSuggestion->getCreatedAt(),
            'user' => [
                'id' => $user->getId(),
                'username' => $user->getUsername(),
                'fullName' => $user->getFullName()
            ],
            'feedback' => $feedback
        ], 'Suggestion status updated successfully');
    }

    public function delete(int $id, int $userId, string $userRole): array
    {
        $suggestion = $this->suggestion->find($id);
        if (!$suggestion) {
            return Response::formatError(
                'Suggestion not found',
                404,
                [],
                'COMPLAINT_NOT_FOUND'
            );
        }

        if ($suggestion->getUserId() !== $userId && $userRole !== 'admin') {
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
                'COMPLAINT_DELETE_FAILED'
            );
        }

        return Response::formatSuccess(null, 'Suggestion deleted successfully');
    }

    public function getById(int $id, int $userId, string $userRole, ?string $status = null): array
    {
        $suggestion = $this->suggestion->find($id);
        if (!$suggestion) {
            return Response::formatError(
                'Suggestion not found',
                404,
                [],
                'COMPLAINT_NOT_FOUND'
            );
        }

        if ($suggestion->getUserId() !== $userId && !in_array($userRole, ['admin', 'staff'])) {
            return Response::formatError(
                'Not authorized',
                403,
                [],
                'UNAUTHORIZED_ACCESS'
            );
        }

        if ($status !== null && $suggestion->getStatus() !== $status) {
            return Response::formatError(
                'Suggestion not found with the specified status',
                404,
                [],
                'COMPLAINT_NOT_FOUND'
            );
        }

        $user = $suggestion->getUser();
        $feedback = $suggestion->getFeedback();

        return Response::formatSuccess([
            'id' => $suggestion->getId(),
            'content' => $suggestion->getContent(),
            'status' => $suggestion->getStatus(),
            'createdAt' => $suggestion->getCreatedAt(),
            'user' => [
                'id' => $user->getId(),
                'username' => $user->getUsername(),
                'fullName' => $user->getFullName()
            ],
            'feedback' => $feedback
        ]);
    }

    public function getAll(int $userId, string $userRole, ?string $status = null, ?string $search = null): array
    {
        $suggestions = [];
        $rawSuggestions = [];

        try {
            if ($userRole === 'admin') {
                if ($status !== null) {
                    $rawSuggestions = $search !== null && !empty($search)
                        ? $this->suggestion->getAllByStatusWithSearch($status, $search)
                        : $this->suggestion->getAllByStatus($status);
                } else {
                    $rawSuggestions = $search !== null && !empty($search)
                        ? $this->suggestion->getAllWithSearch($search)
                        : $this->suggestion->getAll();
                }
            } else {
                if ($status !== null) {
                    $rawSuggestions = $search !== null && !empty($search)
                        ? $this->suggestion->getAllByUserAndStatusWithSearch($userId, $status, $search)
                        : $this->suggestion->getAllByUserAndStatus($userId, $status);
                } else {
                    $rawSuggestions = $search !== null && !empty($search)
                        ? $this->suggestion->getAllByUserWithSearch($userId, $search)
                        : $this->suggestion->getAllByUser($userId);
                }
            }

            foreach ($rawSuggestions as $suggestionData) {
                $suggestion = $this->suggestion->find((int)$suggestionData['id']);
                if ($suggestion) {
                    $user = $suggestion->getUser();
                    $feedback = $suggestion->getFeedback();

                    $suggestions[] = [
                        'id' => $suggestion->getId(),
                        'content' => $suggestion->getContent(),
                        'status' => $suggestion->getStatus(),
                        'createdAt' => $suggestion->getCreatedAt(),
                        'user' => [
                            'id' => $user->getId(),
                            'username' => $user->getUsername(),
                            'fullName' => $user->getFullName()
                        ],
                        'feedback' => $feedback
                    ];
                }
            }

            return Response::formatSuccess($suggestions);
        } catch (\Exception $e) {
            return Response::formatError(
                'Failed to fetch suggestions',
                500,
                ['error' => $e->getMessage()],
                'COMPLAINTS_FETCH_ERROR'
            );
        }
    }

    public function getAllFeedback(int $suggestionId, int $userId, string $userRole): array
    {
        $suggestion = $this->suggestion->find($suggestionId);
        if (!$suggestion) {
            return Response::formatError(
                'Suggestion not found',
                404,
                [],
                'COMPLAINT_NOT_FOUND'
            );
        }

        if ($suggestion->getUserId() !== $userId && $userRole !== 'admin') {
            return Response::formatError(
                'Not authorized to view feedback for this suggestion',
                403,
                [],
                'UNAUTHORIZED_ACCESS'
            );
        }

        $result = $this->feedbackService->getAllForSuggestion(['id' => $suggestionId]);
        if ($result['status'] === 'error') {
            return $result;
        }

        // Format the response to only include relevant IDs
        $formattedFeedback = array_map(function($feedback) {
            return [
                'id' => $feedback['id'],
                'suggestionId' => $feedback['suggestionId'],
                'content' => $feedback['content'],
                'createdAt' => $feedback['createdAt'],
                'admin' => $feedback['admin']
            ];
        }, $result['data']);

        return Response::formatSuccess($formattedFeedback);
    }

    public function createFeedback(int $suggestionId, array $data, int $userId, string $userRole): array
    {
        if (!$data) {
            return Response::formatError('Invalid request payload', 400);
        }

        $suggestion = $this->suggestion->find($suggestionId);
        if (!$suggestion) {
            return Response::formatError(
                'Suggestion not found',
                404,
                [],
                'COMPLAINT_NOT_FOUND'
            );
        }

        if ($userRole !== 'admin') {
            return Response::formatError(
                'Access denied. Only administrators can create feedback.',
                403,
                [],
                'ACCESS_DENIED'
            );
        }

        $data['suggestion_id'] = $suggestionId;
        $result = $this->feedbackService->create($data);
        if ($result['status'] === 'error') {
            return $result;
        }

        // Format the response to only include relevant IDs
        return Response::formatSuccess([
            'id' => $result['data']['id'],
            'suggestionId' => $suggestionId,
            'content' => $result['data']['content'],
            'createdAt' => $result['data']['createdAt'],
            'admin' => $result['data']['admin']
        ], 'Feedback created successfully');
    }

    public function getFeedbackById(int $suggestionId, int $feedbackId, int $userId, string $userRole): array
    {
        $suggestion = $this->suggestion->find($suggestionId);
        if (!$suggestion) {
            return Response::formatError(
                'Suggestion not found',
                404,
                [],
                'COMPLAINT_NOT_FOUND'
            );
        }

        if ($suggestion->getUserId() !== $userId && $userRole !== 'admin') {
            return Response::formatError(
                'Not authorized to view feedback for this suggestion',
                403,
                [],
                'UNAUTHORIZED_ACCESS'
            );
        }

        $result = $this->feedbackService->getById(['id' => $feedbackId]);
        if ($result['status'] === 'error') {
            return $result;
        }

        // Verify the feedback belongs to the specified suggestion
        if ($result['data']['suggestionId'] !== $suggestionId) {
            return Response::formatError(
                'Feedback does not belong to the specified suggestion',
                404,
                [],
                'FEEDBACK_NOT_FOUND'
            );
        }

        // Format the response to only include relevant IDs
        return Response::formatSuccess([
            'id' => $result['data']['id'],
            'suggestionId' => $suggestionId,
            'content' => $result['data']['content'],
            'createdAt' => $result['data']['createdAt'],
            'admin' => $result['data']['admin']
        ]);
    }

    public function updateFeedback(int $suggestionId, int $feedbackId, array $data, int $userId, string $userRole): array
    {
        if (!$data) {
            return Response::formatError('Invalid request payload', 400);
        }

        $suggestion = $this->suggestion->find($suggestionId);
        if (!$suggestion) {
            return Response::formatError(
                'Suggestion not found',
                404,
                [],
                'COMPLAINT_NOT_FOUND'
            );
        }

        if ($userRole !== 'admin') {
            return Response::formatError(
                'Access denied. Only administrators can update feedback.',
                403,
                [],
                'ACCESS_DENIED'
            );
        }

        $data['suggestion_id'] = $suggestionId;
        $result = $this->feedbackService->update(['id' => $feedbackId], $data);
        if ($result['status'] === 'error') {
            return $result;
        }

        // Format the response to only include relevant IDs
        return Response::formatSuccess([
            'id' => $feedbackId,
            'suggestionId' => $suggestionId,
            'content' => $result['data']['content'],
            'createdAt' => $result['data']['createdAt'],
            'admin' => $result['data']['admin']
        ], 'Feedback updated successfully');
    }

    public function deleteFeedback(int $suggestionId, int $feedbackId, int $userId, string $userRole): array
    {
        $suggestion = $this->suggestion->find($suggestionId);
        if (!$suggestion) {
            return Response::formatError(
                'Suggestion not found',
                404,
                [],
                'COMPLAINT_NOT_FOUND'
            );
        }

        if ($userRole !== 'admin') {
            return Response::formatError(
                'Access denied. Only administrators can delete feedback.',
                403,
                [],
                'ACCESS_DENIED'
            );
        }

        // Get feedback before deletion to check its type
        $feedback = $this->feedbackService->getById(['id' => $feedbackId]);
        if ($feedback['status'] === 'error') {
            return $feedback;
        }

        // Verify the feedback belongs to the specified suggestion
        if ($feedback['data']['suggestionId'] !== $suggestionId) {
            return Response::formatError(
                'Feedback does not belong to the specified suggestion',
                404,
                [],
                'FEEDBACK_NOT_FOUND'
            );
        }

        $result = $this->feedbackService->delete(['id' => $feedbackId]);
        if ($result['status'] === 'error') {
            return $result;
        }

        // Return only the feedback ID and suggestion ID
        return Response::formatSuccess([
            'id' => $feedbackId,
            'suggestionId' => $suggestionId
        ], 'Feedback deleted successfully');
    }
}