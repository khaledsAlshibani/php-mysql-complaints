<?php

namespace App\Services;

use App\Core\Response;
use App\Models\Complaint;
use App\DTO\ComplaintDTO;

class ComplaintService
{
    private Complaint $complaint;

    public function __construct()
    {
        $this->complaint = new Complaint();
    }

    public function create(array $data, int $userId): array
    {
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

        if (!$this->complaint->create($complaintDTO->toArray())) {
            return Response::formatError(
                'Failed to create complaint',
                500,
                [],
                'COMPLAINT_CREATION_FAILED'
            );
        }

        return Response::formatSuccess(null, 'Complaint created successfully');
    }

    public function update(int $id, array $data, int $userId, string $userRole): array
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

        return Response::formatSuccess(null, 'Complaint updated successfully');
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

    public function getById(int $id, int $userId, string $userRole): array
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

    public function getAll(int $userId, string $userRole, ?string $status = null): array
    {
        $complaints = [];
        
        if ($userRole === 'user') {
            $complaints = $this->complaint->getAllByUser($userId);
        } else {
            $complaints = $status ? 
                $this->complaint->getAllByStatus($status) : 
                $this->complaint->getAllByUser($userId);
        }

        return Response::formatSuccess($complaints);
    }
} 