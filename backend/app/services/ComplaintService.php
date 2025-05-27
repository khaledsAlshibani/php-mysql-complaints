<?php

namespace App\Services;

use App\Core\Response;
use App\Models\Complaint;
use App\DTO\ComplaintDTO;
use App\DTO\ComplaintStatusDTO;
use App\Services\FeedbackService;

class ComplaintService
{
    private Complaint $complaint;
    private FeedbackService $feedbackService;

    public function __construct()
    {
        $this->complaint = new Complaint();
        $this->feedbackService = new FeedbackService();
    }

    public function create(array $data, int $userId): array
    {
        if (!$data) {
            return Response::formatError('Invalid request payload', 400);
        }

        $data['user_id'] = $userId;
        $complaintDTO = new ComplaintDTO($data);
        
        $validationErrors = $complaintDTO->validate();
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

        $complaintData = $complaintDTO->toArray();
        $newComplaintId = $this->complaint->create($complaintData);
        
        if (!$newComplaintId) {
            return Response::formatError(
                'Failed to create complaint',
                500,
                [],
                'COMPLAINT_CREATION_FAILED'
            );
        }

        // Fetch the newly created complaint with all its data
        $newComplaint = $this->complaint->find($newComplaintId);
        if (!$newComplaint) {
            return Response::formatError(
                'Failed to retrieve created complaint',
                500,
                [],
                'COMPLAINT_RETRIEVAL_FAILED'
            );
        }

        // Format the complaint data with user and feedback
        $user = $newComplaint->getUser();
        $feedback = $newComplaint->getFeedback();

        return Response::formatSuccess([
            'id' => $newComplaint->getId(),
            'content' => $newComplaint->getContent(),
            'status' => $newComplaint->getStatus(),
            'createdAt' => $newComplaint->getCreatedAt(),
            'user' => [
                'id' => $user->getId(),
                'username' => $user->getUsername(),
                'fullName' => $user->getFullName()
            ],
            'feedback' => $feedback
        ], 'Complaint created successfully');
    }

    public function update(int $id, array $data, int $userId, string $userRole): array
    {
        if (!$data) {
            return Response::formatError('Invalid request payload', 400);
        }

        $complaint = $this->complaint->find($id);
        if (!$complaint) {
            return Response::formatError(
                'Complaint not found',
                404,
                [],
                'COMPLAINT_NOT_FOUND'
            );
        }

        if ($complaint->getUserId() !== $userId && $userRole !== 'admin') {
            return Response::formatError(
                'Not authorized',
                403,
                [],
                'UNAUTHORIZED_ACCESS'
            );
        }

        $data['id'] = $id;
        $data['user_id'] = $complaint->getUserId();
        $complaintDTO = new ComplaintDTO($data);
        
        $validationErrors = $complaintDTO->validate();
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

        if (!$complaint->update($complaintDTO->toArray())) {
            return Response::formatError(
                'Failed to update complaint',
                500,
                [],
                'COMPLAINT_UPDATE_FAILED'
            );
        }

        // Fetch the updated complaint with all its data
        $updatedComplaint = $this->complaint->find($id);
        if (!$updatedComplaint) {
            return Response::formatError(
                'Failed to retrieve updated complaint',
                500,
                [],
                'COMPLAINT_RETRIEVAL_FAILED'
            );
        }

        // Format the complaint data with user and feedback
        $user = $updatedComplaint->getUser();
        $feedback = $updatedComplaint->getFeedback();

        return Response::formatSuccess([
            'id' => $updatedComplaint->getId(),
            'content' => $updatedComplaint->getContent(),
            'status' => $updatedComplaint->getStatus(),
            'createdAt' => $updatedComplaint->getCreatedAt(),
            'user' => [
                'id' => $user->getId(),
                'username' => $user->getUsername(),
                'fullName' => $user->getFullName()
            ],
            'feedback' => $feedback
        ], 'Complaint updated successfully');
    }

    public function updateStatus(int $id, array $data, int $userId, string $userRole): array
    {
        if (!$data || !isset($data['status'])) {
            return Response::formatError('Status is required', 400);
        }

        $complaint = $this->complaint->find($id);
        if (!$complaint) {
            return Response::formatError(
                'Complaint not found',
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

        $statusDTO = new ComplaintStatusDTO($data);
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

        if (!$complaint->update($statusDTO->toArray())) {
            return Response::formatError(
                'Failed to update complaint status',
                500,
                [],
                'COMPLAINT_UPDATE_FAILED'
            );
        }

        // Fetch the updated complaint with all its data
        $updatedComplaint = $this->complaint->find($id);
        if (!$updatedComplaint) {
            return Response::formatError(
                'Failed to retrieve updated complaint',
                500,
                [],
                'COMPLAINT_RETRIEVAL_FAILED'
            );
        }

        // Format the complaint data with user and feedback
        $user = $updatedComplaint->getUser();
        $feedback = $updatedComplaint->getFeedback();

        return Response::formatSuccess([
            'id' => $updatedComplaint->getId(),
            'content' => $updatedComplaint->getContent(),
            'status' => $updatedComplaint->getStatus(),
            'createdAt' => $updatedComplaint->getCreatedAt(),
            'user' => [
                'id' => $user->getId(),
                'username' => $user->getUsername(),
                'fullName' => $user->getFullName()
            ],
            'feedback' => $feedback
        ], 'Complaint status updated successfully');
    }

    public function delete(int $id, int $userId, string $userRole): array
    {
        $complaint = $this->complaint->find($id);
        if (!$complaint) {
            return Response::formatError(
                'Complaint not found',
                404,
                [],
                'COMPLAINT_NOT_FOUND'
            );
        }

        if ($complaint->getUserId() !== $userId && $userRole !== 'admin') {
            return Response::formatError(
                'Not authorized',
                403,
                [],
                'UNAUTHORIZED_ACCESS'
            );
        }

        if (!$complaint->delete()) {
            return Response::formatError(
                'Failed to delete complaint',
                500,
                [],
                'COMPLAINT_DELETE_FAILED'
            );
        }

        return Response::formatSuccess(null, 'Complaint deleted successfully');
    }

    public function getById(int $id, int $userId, string $userRole, ?string $status = null): array
    {
        $complaint = $this->complaint->find($id);
        if (!$complaint) {
            return Response::formatError(
                'Complaint not found',
                404,
                [],
                'COMPLAINT_NOT_FOUND'
            );
        }

        if ($complaint->getUserId() !== $userId && !in_array($userRole, ['admin', 'staff'])) {
            return Response::formatError(
                'Not authorized',
                403,
                [],
                'UNAUTHORIZED_ACCESS'
            );
        }

        if ($status !== null && $complaint->getStatus() !== $status) {
            return Response::formatError(
                'Complaint not found with the specified status',
                404,
                [],
                'COMPLAINT_NOT_FOUND'
            );
        }

        $user = $complaint->getUser();
        $feedback = $complaint->getFeedback();

        return Response::formatSuccess([
            'id' => $complaint->getId(),
            'content' => $complaint->getContent(),
            'status' => $complaint->getStatus(),
            'createdAt' => $complaint->getCreatedAt(),
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
        $complaints = [];
        $rawComplaints = [];

        try {
            if ($userRole === 'admin') {
                if ($status !== null) {
                    $rawComplaints = $search !== null && !empty($search)
                        ? $this->complaint->getAllByStatusWithSearch($status, $search)
                        : $this->complaint->getAllByStatus($status);
                } else {
                    $rawComplaints = $search !== null && !empty($search)
                        ? $this->complaint->getAllWithSearch($search)
                        : $this->complaint->getAll();
                }
            } else {
                if ($status !== null) {
                    $rawComplaints = $search !== null && !empty($search)
                        ? $this->complaint->getAllByUserAndStatusWithSearch($userId, $status, $search)
                        : $this->complaint->getAllByUserAndStatus($userId, $status);
                } else {
                    $rawComplaints = $search !== null && !empty($search)
                        ? $this->complaint->getAllByUserWithSearch($userId, $search)
                        : $this->complaint->getAllByUser($userId);
                }
            }

            foreach ($rawComplaints as $complaintData) {
                $complaint = $this->complaint->find((int)$complaintData['id']);
                if ($complaint) {
                    $user = $complaint->getUser();
                    $feedback = $complaint->getFeedback();

                    $complaints[] = [
                        'id' => $complaint->getId(),
                        'content' => $complaint->getContent(),
                        'status' => $complaint->getStatus(),
                        'createdAt' => $complaint->getCreatedAt(),
                        'user' => [
                            'id' => $user->getId(),
                            'username' => $user->getUsername(),
                            'fullName' => $user->getFullName()
                        ],
                        'feedback' => $feedback
                    ];
                }
            }

            return Response::formatSuccess($complaints);
        } catch (\Exception $e) {
            return Response::formatError(
                'Failed to fetch complaints',
                500,
                ['error' => $e->getMessage()],
                'COMPLAINTS_FETCH_ERROR'
            );
        }
    }

    public function getAllFeedback(int $complaintId, int $userId, string $userRole): array
    {
        $complaint = $this->complaint->find($complaintId);
        if (!$complaint) {
            return Response::formatError(
                'Complaint not found',
                404,
                [],
                'COMPLAINT_NOT_FOUND'
            );
        }

        if ($complaint->getUserId() !== $userId && $userRole !== 'admin') {
            return Response::formatError(
                'Not authorized to view feedback for this complaint',
                403,
                [],
                'UNAUTHORIZED_ACCESS'
            );
        }

        $result = $this->feedbackService->getAllForComplaint(['id' => $complaintId]);
        if ($result['status'] === 'error') {
            return $result;
        }

        // Format the response to only include relevant IDs
        $formattedFeedback = array_map(function($feedback) {
            return [
                'id' => $feedback['id'],
                'complaintId' => $feedback['complaintId'],
                'content' => $feedback['content'],
                'createdAt' => $feedback['createdAt'],
                'admin' => $feedback['admin']
            ];
        }, $result['data']);

        return Response::formatSuccess($formattedFeedback);
    }

    public function createFeedback(int $complaintId, array $data, int $userId, string $userRole): array
    {
        if (!$data) {
            return Response::formatError('Invalid request payload', 400);
        }

        $complaint = $this->complaint->find($complaintId);
        if (!$complaint) {
            return Response::formatError(
                'Complaint not found',
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

        $data['complaint_id'] = $complaintId;
        $result = $this->feedbackService->create($data);
        if ($result['status'] === 'error') {
            return $result;
        }

        // Format the response to only include relevant IDs
        return Response::formatSuccess([
            'id' => $result['data']['id'],
            'complaintId' => $complaintId,
            'content' => $result['data']['content'],
            'createdAt' => $result['data']['createdAt'],
            'admin' => $result['data']['admin']
        ], 'Feedback created successfully');
    }

    public function getFeedbackById(int $complaintId, int $feedbackId, int $userId, string $userRole): array
    {
        $complaint = $this->complaint->find($complaintId);
        if (!$complaint) {
            return Response::formatError(
                'Complaint not found',
                404,
                [],
                'COMPLAINT_NOT_FOUND'
            );
        }

        if ($complaint->getUserId() !== $userId && $userRole !== 'admin') {
            return Response::formatError(
                'Not authorized to view feedback for this complaint',
                403,
                [],
                'UNAUTHORIZED_ACCESS'
            );
        }

        $result = $this->feedbackService->getById(['id' => $feedbackId]);
        if ($result['status'] === 'error') {
            return $result;
        }

        // Verify the feedback belongs to the specified complaint
        if ($result['data']['complaintId'] !== $complaintId) {
            return Response::formatError(
                'Feedback does not belong to the specified complaint',
                404,
                [],
                'FEEDBACK_NOT_FOUND'
            );
        }

        // Format the response to only include relevant IDs
        return Response::formatSuccess([
            'id' => $result['data']['id'],
            'complaintId' => $complaintId,
            'content' => $result['data']['content'],
            'createdAt' => $result['data']['createdAt'],
            'admin' => $result['data']['admin']
        ]);
    }

    public function updateFeedback(int $complaintId, int $feedbackId, array $data, int $userId, string $userRole): array
    {
        if (!$data) {
            return Response::formatError('Invalid request payload', 400);
        }

        $complaint = $this->complaint->find($complaintId);
        if (!$complaint) {
            return Response::formatError(
                'Complaint not found',
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

        $data['complaint_id'] = $complaintId;
        $result = $this->feedbackService->update(['id' => $feedbackId], $data);
        if ($result['status'] === 'error') {
            return $result;
        }

        // Format the response to only include relevant IDs
        return Response::formatSuccess([
            'id' => $feedbackId,
            'complaintId' => $complaintId,
            'content' => $result['data']['content'],
            'createdAt' => $result['data']['createdAt'],
            'admin' => $result['data']['admin']
        ], 'Feedback updated successfully');
    }

    public function deleteFeedback(int $complaintId, int $feedbackId, int $userId, string $userRole): array
    {
        $complaint = $this->complaint->find($complaintId);
        if (!$complaint) {
            return Response::formatError(
                'Complaint not found',
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

        // Verify the feedback belongs to the specified complaint
        if ($feedback['data']['complaintId'] !== $complaintId) {
            return Response::formatError(
                'Feedback does not belong to the specified complaint',
                404,
                [],
                'FEEDBACK_NOT_FOUND'
            );
        }

        $result = $this->feedbackService->delete(['id' => $feedbackId]);
        if ($result['status'] === 'error') {
            return $result;
        }

        // Return only the feedback ID and complaint ID
        return Response::formatSuccess([
            'id' => $feedbackId,
            'complaintId' => $complaintId
        ], 'Feedback deleted successfully');
    }
}