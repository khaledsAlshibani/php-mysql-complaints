<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Response;
use App\Models\Feedback;
use App\Models\Complaint;
use App\Models\Suggestion;
use App\Services\AuthService;

class FeedbackController extends Controller
{
    private Feedback $feedback;
    private Complaint $complaint;
    private Suggestion $suggestion;
    private AuthService $authService;

    public function __construct()
    {
        $this->feedback = new Feedback();
        $this->complaint = new Complaint();
        $this->suggestion = new Suggestion();
        $this->authService = new AuthService();
    }

    private function authenticate(): ?array
    {
        $user = $this->authService->getCurrentUser();
        if (!$user) {
            Response::sendAuthenticationError();
            return null;
        }
        return $user;
    }

    public function create(): void
    {
        $user = $this->authenticate();
        if (!$user) {
            return;
        }

        if ($user['role'] !== 'admin') {
            Response::sendAuthorizationError('Access denied. Only administrators can create feedback.');
            return;
        }

        $data = $this->getJsonInput();
        if (!$this->validateRequired($data, ['content'])) {
            Response::sendValidationError(['content' => 'Content is required']);
            return;
        }

        // Check if either complaint_id or suggestion_id is provided
        if (!isset($data['complaint_id']) && !isset($data['suggestion_id'])) {
            Response::sendError(
                'Either complaint_id or suggestion_id must be provided',
                400,
                [],
                'INVALID_REFERENCE'
            );
            return;
        }

        // Check if both complaint_id and suggestion_id are provided
        if (isset($data['complaint_id']) && isset($data['suggestion_id'])) {
            Response::sendError(
                'Cannot provide both complaint_id and suggestion_id',
                400,
                [],
                'INVALID_REFERENCE'
            );
            return;
        }

        // Handle complaint feedback
        if (isset($data['complaint_id'])) {
            $complaint = $this->complaint->find((int)$data['complaint_id']);
            if (!$complaint) {
                Response::sendError(
                    'Complaint not found',
                    404,
                    [],
                    'COMPLAINT_NOT_FOUND'
                );
                return;
            }
            $parentItem = $complaint;
        }
        // Handle suggestion feedback
        else {
            $suggestion = $this->suggestion->find((int)$data['suggestion_id']);
            if (!$suggestion) {
                Response::sendError(
                    'Suggestion not found',
                    404,
                    [],
                    'SUGGESTION_NOT_FOUND'
                );
                return;
            }
            $parentItem = $suggestion;
        }

        $data['admin_id'] = $user['id'];

        if (!$this->feedback->create($data)) {
            Response::sendError(
                'Failed to create feedback',
                500,
                [],
                'FEEDBACK_CREATION_FAILED'
            );
            return;
        }

        // Update parent item status
        $parentItem->update(['status' => 'pending_reviewed']);

        Response::sendSuccess(null, 'Feedback created successfully', 201);
    }

    public function update(array $params): void
    {
        $user = $this->authenticate();
        if (!$user) {
            return;
        }

        if ($user['role'] !== 'admin') {
            Response::sendAuthorizationError('Access denied. Only administrators can update feedback.');
            return;
        }

        if (!isset($params['id'])) {
            Response::sendError('Feedback ID is required', 400, [], 'MISSING_ID');
            return;
        }

        $feedback = $this->feedback->find((int)$params['id']);
        if (!$feedback) {
            Response::sendError(
                'Feedback not found',
                404,
                [],
                'FEEDBACK_NOT_FOUND'
            );
            return;
        }

        // Only allow the admin who created the feedback to update it
        if ($feedback->getAdminId() !== $user['id']) {
            Response::sendAuthorizationError('Not authorized. Only the admin who created the feedback can update it.');
            return;
        }

        $data = $this->getJsonInput();
        if (!$this->validateRequired($data, ['content'])) {
            Response::sendValidationError(['content' => 'Content is required']);
            return;
        }

        if (!$feedback->update($data)) {
            Response::sendError(
                'Failed to update feedback',
                500,
                [],
                'FEEDBACK_UPDATE_FAILED'
            );
            return;
        }

        Response::sendSuccess(null, 'Feedback updated successfully');
    }

    public function delete(array $params): void
    {
        $user = $this->authenticate();
        if (!$user) {
            return;
        }

        if ($user['role'] !== 'admin') {
            Response::sendAuthorizationError('Access denied. Only administrators can delete feedback.');
            return;
        }

        if (!isset($params['id'])) {
            Response::sendError('Feedback ID is required', 400, [], 'MISSING_ID');
            return;
        }

        $feedback = $this->feedback->find((int)$params['id']);
        if (!$feedback) {
            Response::sendError(
                'Feedback not found',
                404,
                [],
                'FEEDBACK_NOT_FOUND'
            );
            return;
        }

        // Only allow the admin who created the feedback to delete it
        if ($feedback->getAdminId() !== $user['id']) {
            Response::sendAuthorizationError('Not authorized. Only the admin who created the feedback can delete it.');
            return;
        }

        if (!$feedback->delete()) {
            Response::sendError(
                'Failed to delete feedback',
                500,
                [],
                'FEEDBACK_DELETE_FAILED'
            );
            return;
        }

        // Update parent item status if this was the last feedback
        if ($feedback->getComplaintId()) {
            $complaint = $this->complaint->find($feedback->getComplaintId());
            if ($complaint && empty($complaint->getFeedback())) {
                $complaint->update(['status' => 'pending_no_feedback']);
            }
        } elseif ($feedback->getSuggestionId()) {
            $suggestion = $this->suggestion->find($feedback->getSuggestionId());
            if ($suggestion && empty($suggestion->getFeedback())) {
                $suggestion->update(['status' => 'pending_no_feedback']);
            }
        }

        Response::sendSuccess(null, 'Feedback deleted successfully');
    }

    public function getById(array $params): void
    {
        $user = $this->authenticate();
        if (!$user) {
            return;
        }

        if (!isset($params['id'])) {
            Response::sendError('Feedback ID is required', 400, [], 'MISSING_ID');
            return;
        }

        $feedback = $this->feedback->find((int)$params['id']);
        if (!$feedback) {
            Response::sendError(
                'Feedback not found',
                404,
                [],
                'FEEDBACK_NOT_FOUND'
            );
            return;
        }

        // Check if user has access to the parent item
        $hasAccess = false;
        if ($feedback->getComplaintId()) {
            $complaint = $this->complaint->find($feedback->getComplaintId());
            $hasAccess = $complaint && ($complaint->getUserId() === $user['id'] || 
                        $user['role'] === 'admin');
        } elseif ($feedback->getSuggestionId()) {
            $suggestion = $this->suggestion->find($feedback->getSuggestionId());
            $hasAccess = $suggestion && ($suggestion->getUserId() === $user['id'] || 
                        $user['role'] === 'admin');
        }

        if (!$hasAccess) {
            Response::sendAuthorizationError('Not authorized to view this feedback');
            return;
        }

        $admin = $feedback->getAdmin();
        Response::sendSuccess([
            'id' => $feedback->getId(),
            'content' => $feedback->getContent(),
            'complaintId' => $feedback->getComplaintId(),
            'suggestionId' => $feedback->getSuggestionId(),
            'createdAt' => $feedback->getCreatedAt(),
            'admin' => [
                'id' => $admin->getId(),
                'username' => $admin->getUsername(),
                'fullName' => $admin->getFullName()
            ]
        ]);
    }

    public function getAllByAdmin(): void
    {
        $user = $this->authenticate();
        if (!$user) {
            return;
        }

        if ($user['role'] !== 'admin') {
            Response::sendAuthorizationError('Access denied. Only administrators can view all feedback.');
            return;
        }

        $feedback = $this->feedback->getAllByAdmin($user['id']);
        Response::sendSuccess($feedback);
    }

    public function getAllForComplaint(array $params): void
    {
        $user = $this->authenticate();
        if (!$user) {
            return;
        }

        if (!isset($params['id'])) {
            Response::sendError('Complaint ID is required', 400, [], 'MISSING_COMPLAINT_ID');
            return;
        }

        $complaint = $this->complaint->find((int)$params['id']);
        if (!$complaint) {
            Response::sendError(
                'Complaint not found',
                404,
                [],
                'COMPLAINT_NOT_FOUND'
            );
            return;
        }

        // Allow access if user owns the complaint or is an admin
        if ($complaint->getUserId() !== $user['id'] && $user['role'] !== 'admin') {
            Response::sendAuthorizationError('Not authorized to view feedback for this complaint');
            return;
        }

        $feedback = $this->feedback->getAllForComplaint((int)$params['id']);
        Response::sendSuccess($feedback);
    }

    public function getAllForSuggestion(array $params): void
    {
        $user = $this->authenticate();
        if (!$user) {
            return;
        }

        if (!isset($params['id'])) {
            Response::sendError('Suggestion ID is required', 400, [], 'MISSING_SUGGESTION_ID');
            return;
        }

        $suggestion = $this->suggestion->find((int)$params['id']);
        if (!$suggestion) {
            Response::sendError(
                'Suggestion not found',
                404,
                [],
                'SUGGESTION_NOT_FOUND'
            );
            return;
        }

        // Allow access if user owns the suggestion or is an admin
        if ($suggestion->getUserId() !== $user['id'] && $user['role'] !== 'admin') {
            Response::sendAuthorizationError('Not authorized to view feedback for this suggestion');
            return;
        }

        $feedback = $this->feedback->getAllForSuggestion((int)$params['id']);
        Response::sendSuccess($feedback);
    }
}
