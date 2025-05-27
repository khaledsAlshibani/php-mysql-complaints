<?php

namespace App\Services;

use App\Core\Response;
use App\DTOs\UserSubmissionDTO;
use App\DTOs\UserSubmissionStatusDTO;
use App\Services\FeedbackService;
use App\Models\UserSubmissionModel;
use App\Models\User;

/**
 * Abstract service class for handling user submissions like complaints and suggestions
 * 
 * This service provides common operations for user-submitted content including:
 * - Creating new submissions
 * - Retrieving submissions by various criteria
 * - Updating submission status and content
 * - Deleting submissions
 * - Search functionality
 * 
 * Child classes must implement:
 * - getModel(): UserSubmissionModel — returns the appropriate model instance
 * - getDTO(): UserSubmissionDTO — returns the appropriate DTO instance
 * - getStatusDTO(): UserSubmissionStatusDTO — returns the appropriate status DTO instance
 * 
 * @package App\Services
 * @abstract
 */
abstract class UserSubmissionService
{
    protected UserSubmissionModel $model;
    private FeedbackService $feedbackService;

    public function __construct()
    {
        $this->model = $this->getModel();
        $this->feedbackService = new FeedbackService();
    }

    abstract protected function getModel(): UserSubmissionModel;
    abstract protected function getDTO(array $data): UserSubmissionDTO;
    abstract protected function getStatusDTO(array $data): UserSubmissionStatusDTO;

    public function create(array $data, int $userId): array
    {
        if (!$data) {
            return Response::formatError('Invalid request payload', 400);
        }

        $data['user_id'] = $userId;
        $dto = $this->getDTO($data);
        
        $validationErrors = $dto->validate();
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

        $submissionData = $dto->toArray();
        $newSubmissionId = $this->model->create($submissionData);
        
        if (!$newSubmissionId) {
            return Response::formatError(
                'Failed to create submission',
                500,
                [],
                'SUBMISSION_CREATION_FAILED'
            );
        }

        // Fetch the newly created submission with all its data
        $newSubmission = $this->model->find($newSubmissionId);
        if (!$newSubmission) {
            return Response::formatError(
                'Failed to retrieve created submission',
                500,
                [],
                'SUBMISSION_RETRIEVAL_FAILED'
            );
        }

        // Format the submission data with user and feedback
        $user = $newSubmission->getUser();
        $feedback = $newSubmission->getFeedback();

        return Response::formatSuccess([
            'id' => $newSubmission->getId(),
            'content' => $newSubmission->getContent(),
            'status' => $newSubmission->getStatus(),
            'createdAt' => $newSubmission->getCreatedAt(),
            'user' => [
                'id' => $user->getId(),
                'username' => $user->getUsername(),
                'fullName' => $user->getFullName()
            ],
            'feedback' => $feedback
        ], 'Submission created successfully');
    }

    public function update(int $id, array $data, int $userId, string $userRole): array
    {
        if (!$data) {
            return Response::formatError('Invalid request payload', 400);
        }

        $submission = $this->model->find($id);
        if (!$submission) {
            return Response::formatError(
                'Submission not found',
                404,
                [],
                'SUBMISSION_NOT_FOUND'
            );
        }

        if ($submission->getUserId() !== $userId && $userRole !== 'admin') {
            return Response::formatError(
                'Not authorized',
                403,
                [],
                'UNAUTHORIZED_ACCESS'
            );
        }

        $data['id'] = $id;
        $data['user_id'] = $submission->getUserId();
        $dto = $this->getDTO($data);
        
        $validationErrors = $dto->validate();
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

        if (!$submission->update($dto->toArray())) {
            return Response::formatError(
                'Failed to update submission',
                500,
                [],
                'SUBMISSION_UPDATE_FAILED'
            );
        }

        // Fetch the updated submission with all its data
        $updatedSubmission = $this->model->find($id);
        if (!$updatedSubmission) {
            return Response::formatError(
                'Failed to retrieve updated submission',
                500,
                [],
                'SUBMISSION_RETRIEVAL_FAILED'
            );
        }

        // Format the submission data with user and feedback
        $user = $updatedSubmission->getUser();
        $feedback = $updatedSubmission->getFeedback();

        return Response::formatSuccess([
            'id' => $updatedSubmission->getId(),
            'content' => $updatedSubmission->getContent(),
            'status' => $updatedSubmission->getStatus(),
            'createdAt' => $updatedSubmission->getCreatedAt(),
            'user' => [
                'id' => $user->getId(),
                'username' => $user->getUsername(),
                'fullName' => $user->getFullName()
            ],
            'feedback' => $feedback
        ], 'Submission updated successfully');
    }

    public function updateStatus(int $id, array $data, int $userId, string $userRole): array
    {
        if (!$data || !isset($data['status'])) {
            return Response::formatError('Status is required', 400);
        }

        $submission = $this->model->find($id);
        if (!$submission) {
            return Response::formatError(
                'Submission not found',
                404,
                [],
                'SUBMISSION_NOT_FOUND'
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

        $statusDTO = $this->getStatusDTO($data);
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

        if (!$submission->update($statusDTO->toArray())) {
            return Response::formatError(
                'Failed to update submission status',
                500,
                [],
                'SUBMISSION_UPDATE_FAILED'
            );
        }

        // Fetch the updated submission with all its data
        $updatedSubmission = $this->model->find($id);
        if (!$updatedSubmission) {
            return Response::formatError(
                'Failed to retrieve updated submission',
                500,
                [],
                'SUBMISSION_RETRIEVAL_FAILED'
            );
        }

        // Format the submission data with user and feedback
        $user = $updatedSubmission->getUser();
        $feedback = $updatedSubmission->getFeedback();

        return Response::formatSuccess([
            'id' => $updatedSubmission->getId(),
            'content' => $updatedSubmission->getContent(),
            'status' => $updatedSubmission->getStatus(),
            'createdAt' => $updatedSubmission->getCreatedAt(),
            'user' => [
                'id' => $user->getId(),
                'username' => $user->getUsername(),
                'fullName' => $user->getFullName()
            ],
            'feedback' => $feedback
        ], 'Submission status updated successfully');
    }

    public function delete(int $id, int $userId, string $userRole): array
    {
        $submission = $this->model->find($id);
        if (!$submission) {
            return Response::formatError(
                'Submission not found',
                404,
                [],
                'SUBMISSION_NOT_FOUND'
            );
        }

        if ($submission->getUserId() !== $userId && $userRole !== 'admin') {
            return Response::formatError(
                'Not authorized',
                403,
                [],
                'UNAUTHORIZED_ACCESS'
            );
        }

        if (!$submission->delete()) {
            return Response::formatError(
                'Failed to delete submission',
                500,
                [],
                'SUBMISSION_DELETE_FAILED'
            );
        }

        return Response::formatSuccess(null, 'Submission deleted successfully');
    }

    public function getById(int $id, int $userId, string $userRole, ?string $status = null): array
    {
        $submission = $this->model->find($id);
        if (!$submission) {
            return Response::formatError(
                'Submission not found',
                404,
                [],
                'SUBMISSION_NOT_FOUND'
            );
        }

        if ($submission->getUserId() !== $userId && !in_array($userRole, ['admin', 'staff'])) {
            return Response::formatError(
                'Not authorized',
                403,
                [],
                'UNAUTHORIZED_ACCESS'
            );
        }

        if ($status !== null && $submission->getStatus() !== $status) {
            return Response::formatError(
                'Submission not found with the specified status',
                404,
                [],
                'SUBMISSION_NOT_FOUND'
            );
        }

        $user = $submission->getUser();
        $feedback = $submission->getFeedback();

        return Response::formatSuccess([
            'id' => $submission->getId(),
            'content' => $submission->getContent(),
            'status' => $submission->getStatus(),
            'createdAt' => $submission->getCreatedAt(),
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
        $submissions = [];
        $rawSubmissions = [];

        try {
            if ($userRole === 'admin') {
                if ($status !== null) {
                    $rawSubmissions = $search !== null && !empty($search)
                        ? $this->model->getAllByStatusWithSearch($status, $search)
                        : $this->model->getAllByStatus($status);
                } else {
                    $rawSubmissions = $search !== null && !empty($search)
                        ? $this->model->getAllWithSearch($search)
                        : $this->model->getAll();
                }
            } else {
                if ($status !== null) {
                    $rawSubmissions = $search !== null && !empty($search)
                        ? $this->model->getAllByUserAndStatusWithSearch($userId, $status, $search)
                        : $this->model->getAllByUserAndStatus($userId, $status);
                } else {
                    $rawSubmissions = $search !== null && !empty($search)
                        ? $this->model->getAllByUserWithSearch($userId, $search)
                        : $this->model->getAllByUser($userId);
                }
            }

            foreach ($rawSubmissions as $submissionData) {
                $submission = $this->model->find((int)$submissionData['id']);
                if ($submission) {
                    $user = $submission->getUser();
                    $feedback = $submission->getFeedback();

                    $submissions[] = [
                        'id' => $submission->getId(),
                        'content' => $submission->getContent(),
                        'status' => $submission->getStatus(),
                        'createdAt' => $submission->getCreatedAt(),
                        'user' => [
                            'id' => $user->getId(),
                            'username' => $user->getUsername(),
                            'fullName' => $user->getFullName()
                        ],
                        'feedback' => $feedback
                    ];
                }
            }

            return Response::formatSuccess($submissions);
        } catch (\Exception $e) {
            return Response::formatError(
                'Failed to fetch submissions',
                500,
                ['error' => $e->getMessage()],
                'SUBMISSIONS_FETCH_ERROR'
            );
        }
    }

    public function getAllFeedback(int $submissionId, int $userId, string $userRole): array
    {
        $submission = $this->model->find($submissionId);
        if (!$submission) {
            return Response::formatError(
                'Submission not found',
                404,
                [],
                'SUBMISSION_NOT_FOUND'
            );
        }

        if ($submission->getUserId() !== $userId && $userRole !== 'admin') {
            return Response::formatError(
                'Not authorized to view feedback for this submission',
                403,
                [],
                'UNAUTHORIZED_ACCESS'
            );
        }

        $result = $this->feedbackService->getAllForSubmission(['id' => $submissionId]);
        if ($result['status'] === 'error') {
            return $result;
        }

        // Format the response to only include relevant IDs
        $formattedFeedback = array_map(function($feedback) {
            return [
                'id' => $feedback['id'],
                'submissionId' => $feedback['submissionId'],
                'content' => $feedback['content'],
                'createdAt' => $feedback['createdAt'],
                'admin' => $feedback['admin']
            ];
        }, $result['data']);

        return Response::formatSuccess($formattedFeedback);
    }

    public function createFeedback(int $submissionId, array $data, int $userId, string $userRole): array
    {
        if (!$data) {
            return Response::formatError('Invalid request payload', 400);
        }

        $submission = $this->model->find($submissionId);
        if (!$submission) {
            return Response::formatError(
                'Submission not found',
                404,
                [],
                'SUBMISSION_NOT_FOUND'
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

        $data['submission_id'] = $submissionId;
        $result = $this->feedbackService->create($data);
        if ($result['status'] === 'error') {
            return $result;
        }

        // Format the response to only include relevant IDs
        return Response::formatSuccess([
            'id' => $result['data']['id'],
            'submissionId' => $submissionId,
            'content' => $result['data']['content'],
            'createdAt' => $result['data']['createdAt'],
            'admin' => $result['data']['admin']
        ], 'Feedback created successfully');
    }

    public function getFeedbackById(int $submissionId, int $feedbackId, int $userId, string $userRole): array
    {
        $submission = $this->model->find($submissionId);
        if (!$submission) {
            return Response::formatError(
                'Submission not found',
                404,
                [],
                'SUBMISSION_NOT_FOUND'
            );
        }

        if ($submission->getUserId() !== $userId && $userRole !== 'admin') {
            return Response::formatError(
                'Not authorized to view feedback for this submission',
                403,
                [],
                'UNAUTHORIZED_ACCESS'
            );
        }

        $result = $this->feedbackService->getById(['id' => $feedbackId]);
        if ($result['status'] === 'error') {
            return $result;
        }

        // Verify the feedback belongs to the specified submission
        if ($result['data']['submissionId'] !== $submissionId) {
            return Response::formatError(
                'Feedback does not belong to the specified submission',
                404,
                [],
                'FEEDBACK_NOT_FOUND'
            );
        }

        // Format the response to only include relevant IDs
        return Response::formatSuccess([
            'id' => $result['data']['id'],
            'submissionId' => $submissionId,
            'content' => $result['data']['content'],
            'createdAt' => $result['data']['createdAt'],
            'admin' => $result['data']['admin']
        ]);
    }

    public function updateFeedback(int $submissionId, int $feedbackId, array $data, int $userId, string $userRole): array
    {
        if (!$data) {
            return Response::formatError('Invalid request payload', 400);
        }

        $submission = $this->model->find($submissionId);
        if (!$submission) {
            return Response::formatError(
                'Submission not found',
                404,
                [],
                'SUBMISSION_NOT_FOUND'
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

        $data['submission_id'] = $submissionId;
        $result = $this->feedbackService->update(['id' => $feedbackId], $data);
        if ($result['status'] === 'error') {
            return $result;
        }

        // Format the response to only include relevant IDs
        return Response::formatSuccess([
            'id' => $feedbackId,
            'submissionId' => $submissionId,
            'content' => $result['data']['content'],
            'createdAt' => $result['data']['createdAt'],
            'admin' => $result['data']['admin']
        ], 'Feedback updated successfully');
    }

    public function deleteFeedback(int $submissionId, int $feedbackId, int $userId, string $userRole): array
    {
        $submission = $this->model->find($submissionId);
        if (!$submission) {
            return Response::formatError(
                'Submission not found',
                404,
                [],
                'SUBMISSION_NOT_FOUND'
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

        // Verify the feedback belongs to the specified submission
        if ($feedback['data']['submissionId'] !== $submissionId) {
            return Response::formatError(
                'Feedback does not belong to the specified submission',
                404,
                [],
                'FEEDBACK_NOT_FOUND'
            );
        }

        $result = $this->feedbackService->delete(['id' => $feedbackId]);
        if ($result['status'] === 'error') {
            return $result;
        }

        // Return only the feedback ID and submission ID
        return Response::formatSuccess([
            'id' => $feedbackId,
            'submissionId' => $submissionId
        ], 'Feedback deleted successfully');
    }

    public function getUser(int $id): ?User
    {
        $submission = $this->model->find($id);
        return $submission ? $submission->getUser() : null;
    }

    public function getFeedback(int $id): array
    {
        $submission = $this->model->find($id);
        return $submission ? $submission->getFeedback() : [];
    }
}