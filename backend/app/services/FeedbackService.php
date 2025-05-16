<?php

namespace App\Services;

use App\Core\Response;
use App\Models\Feedback;
use App\Models\Complaint;
use App\Models\Suggestion;
use App\DTO\FeedbackDTO;

class FeedbackService
{
    private Feedback $feedback;
    private AuthService $authService;
    private Complaint $complaint;
    private Suggestion $suggestion;

    public function __construct()
    {
        $this->feedback = new Feedback();
        $this->authService = new AuthService();
        $this->complaint = new Complaint();
        $this->suggestion = new Suggestion();
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

        if ($user['role'] !== 'admin') {
            return Response::formatError(
                'Access denied. Only administrators can create feedback.',
                403,
                [],
                'ACCESS_DENIED'
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

        $data['admin_id'] = $user['id'];
        $feedbackDTO = new FeedbackDTO($data);
        
        $validationErrors = $feedbackDTO->validate();
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

        if (!$this->feedback->create($feedbackDTO->toArray())) {
            return Response::formatError(
                'Failed to create feedback',
                500,
                [],
                'FEEDBACK_CREATION_FAILED'
            );
        }

        return Response::formatSuccess(null, 'Feedback created successfully');
    }

    public function update(array $params, array $data): array
    {
        if (!isset($params['id'])) {
            return Response::formatError(
                'Feedback ID is required',
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

        if ($user['role'] !== 'admin') {
            return Response::formatError(
                'Access denied. Only administrators can update feedback.',
                403,
                [],
                'ACCESS_DENIED'
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

        $feedback = $this->feedback->find((int)$params['id']);
        if (!$feedback) {
            return Response::formatError(
                'Feedback not found',
                404,
                [],
                'FEEDBACK_NOT_FOUND'
            );
        }

        if ($feedback->getAdminId() !== $user['id']) {
            return Response::formatError(
                'Not authorized. Only the admin who created the feedback can update it.',
                403,
                [],
                'UNAUTHORIZED_ACCESS'
            );
        }

        $data['id'] = $params['id'];
        $data['admin_id'] = $feedback->getAdminId();
        $feedbackDTO = new FeedbackDTO($data);
        
        $validationErrors = $feedbackDTO->validate();
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

        if (!$feedback->update($feedbackDTO->toArray())) {
            return Response::formatError(
                'Failed to update feedback',
                500,
                [],
                'FEEDBACK_UPDATE_FAILED'
            );
        }

        return Response::formatSuccess(null, 'Feedback updated successfully');
    }

    public function delete(array $params): array
    {
        if (!isset($params['id'])) {
            return Response::formatError(
                'Feedback ID is required',
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

        if ($user['role'] !== 'admin') {
            return Response::formatError(
                'Access denied. Only administrators can delete feedback.',
                403,
                [],
                'ACCESS_DENIED'
            );
        }

        $feedback = $this->feedback->find((int)$params['id']);
        if (!$feedback) {
            return Response::formatError(
                'Feedback not found',
                404,
                [],
                'FEEDBACK_NOT_FOUND'
            );
        }

        if ($feedback->getAdminId() !== $user['id']) {
            return Response::formatError(
                'Not authorized. Only the admin who created the feedback can delete it.',
                403,
                [],
                'UNAUTHORIZED_ACCESS'
            );
        }

        if (!$feedback->delete()) {
            return Response::formatError(
                'Failed to delete feedback',
                500,
                [],
                'FEEDBACK_DELETE_FAILED'
            );
        }

        return Response::formatSuccess(null, 'Feedback deleted successfully');
    }

    public function getById(array $params): array
    {
        if (!isset($params['id'])) {
            return Response::formatError(
                'Feedback ID is required',
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

        $feedback = $this->feedback->find((int)$params['id']);
        if (!$feedback) {
            return Response::formatError(
                'Feedback not found',
                404,
                [],
                'FEEDBACK_NOT_FOUND'
            );
        }

        $hasAccess = false;
        if ($feedback->getComplaintId()) {
            $complaint = $this->complaint->find($feedback->getComplaintId());
            $hasAccess = $complaint && ($complaint->getUserId() === $user['id'] || $user['role'] === 'admin');
        } elseif ($feedback->getSuggestionId()) {
            $suggestion = $this->suggestion->find($feedback->getSuggestionId());
            $hasAccess = $suggestion && ($suggestion->getUserId() === $user['id'] || $user['role'] === 'admin');
        }

        if (!$hasAccess) {
            return Response::formatError(
                'Not authorized to view this feedback',
                403,
                [],
                'UNAUTHORIZED_ACCESS'
            );
        }

        $admin = $feedback->getAdmin();
        return Response::formatSuccess([
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

    public function getAllForComplaint(array $params): array
    {
        if (!isset($params['id'])) {
            return Response::formatError(
                'Complaint ID is required',
                400,
                [],
                'MISSING_COMPLAINT_ID'
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

        $complaint = $this->complaint->find((int)$params['id']);
        if (!$complaint) {
            return Response::formatError(
                'Complaint not found',
                404,
                [],
                'COMPLAINT_NOT_FOUND'
            );
        }

        if ($complaint->getUserId() !== $user['id'] && $user['role'] !== 'admin') {
            return Response::formatError(
                'Not authorized to view feedback for this complaint',
                403,
                [],
                'UNAUTHORIZED_ACCESS'
            );
        }

        $feedback = $this->feedback->getAllForComplaint((int)$params['id']);
        return Response::formatSuccess($feedback);
    }

    public function getAllForSuggestion(array $params): array
    {
        if (!isset($params['id'])) {
            return Response::formatError(
                'Suggestion ID is required',
                400,
                [],
                'MISSING_SUGGESTION_ID'
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
                'Not authorized to view feedback for this suggestion',
                403,
                [],
                'UNAUTHORIZED_ACCESS'
            );
        }

        $feedback = $this->feedback->getAllForSuggestion((int)$params['id']);
        return Response::formatSuccess($feedback);
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

        if ($user['role'] !== 'admin') {
            return Response::formatError(
                'Access denied. Only administrators can view all feedback.',
                403,
                [],
                'ACCESS_DENIED'
            );
        }

        $feedback = $this->feedback->getAllByAdmin($user['id']);
        return Response::formatSuccess($feedback);
    }
} 