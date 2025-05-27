<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Response;
use App\Services\ComplaintService;
use App\Services\AuthService;

class ComplaintController extends Controller
{
    private ComplaintService $complaintService;
    private AuthService $authService;

    public function __construct()
    {
        $this->complaintService = new ComplaintService();
        $this->authService = new AuthService();
    }

    public function create(): void
    {
        $data = $this->getJsonInput();
        if (!$data) {
            Response::sendError('Invalid request payload', 400);
            return;
        }

        $user = $this->authService->getCurrentUser();
        if (!$user) {
            Response::sendAuthenticationError();
            return;
        }

        $result = $this->complaintService->create($data, $user['id']);
        if ($result['status'] === 'error') {
            Response::sendError(
                $result['error']['message'],
                $result['error']['code'],
                $result['error']['details'] ?? [],
                $result['error']['errorCode']
            );
            return;
        }

        Response::sendSuccess($result['data'], $result['message'], 201);
    }

    public function update(array $params): void
    {
        if (!isset($params['id'])) {
            Response::sendError('Complaint ID is required', 400);
            return;
        }

        $data = $this->getJsonInput();
        if (!$data) {
            Response::sendError('Invalid request payload', 400);
            return;
        }

        $user = $this->authService->getCurrentUser();
        if (!$user) {
            Response::sendAuthenticationError();
            return;
        }

        $result = $this->complaintService->update((int)$params['id'], $data, $user['id'], $user['role']);
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

    public function delete(array $params): void
    {
        if (!isset($params['id'])) {
            Response::sendError('Complaint ID is required', 400);
            return;
        }

        $user = $this->authService->getCurrentUser();
        if (!$user) {
            Response::sendAuthenticationError();
            return;
        }

        $result = $this->complaintService->delete((int)$params['id'], $user['id'], $user['role']);
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

    public function getById(array $params): void
    {
        if (!isset($params['id'])) {
            Response::sendError('Complaint ID is required', 400);
            return;
        }

        $user = $this->authService->getCurrentUser();
        if (!$user) {
            Response::sendAuthenticationError();
            return;
        }

        $status = $_GET['status'] ?? null;

        $result = $this->complaintService->getById((int)$params['id'], $user['id'], $user['role'], $status);
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

    public function getAll(): void
    {
        $user = $this->authService->getCurrentUser();
        if (!$user) {
            Response::sendAuthenticationError();
            return;
        }

        $search = $_GET['search'] ?? null;
        $status = $_GET['status'] ?? null;

        $result = $this->complaintService->getAll($user['id'], $user['role'], $status, $search);
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

    public function updateStatus(array $params): void
    {
        if (!isset($params['id'])) {
            Response::sendError('Complaint ID is required', 400);
            return;
        }

        $data = $this->getJsonInput();
        if (!$data || !isset($data['status'])) {
            Response::sendError('Status is required', 400);
            return;
        }

        $user = $this->authService->getCurrentUser();
        if (!$user) {
            Response::sendAuthenticationError();
            return;
        }

        $result = $this->complaintService->updateStatus((int)$params['id'], $data, $user['id'], $user['role']);
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
