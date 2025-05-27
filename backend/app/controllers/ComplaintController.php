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
        $user = $this->authService->getCurrentUser();
        if (!$user) {
            Response::sendAuthenticationError();
            return;
        }

        $result = $this->complaintService->create($this->getJsonInput(), $user['id']);
        $this->sendResponse($result);
    }

    public function update(array $params): void
    {
        $user = $this->authService->getCurrentUser();
        if (!$user) {
            Response::sendAuthenticationError();
            return;
        }

        $result = $this->complaintService->update(
            (int)$params['id'],
            $this->getJsonInput(),
            $user['id'],
            $user['role']
        );
        $this->sendResponse($result);
    }

    public function delete(array $params): void
    {
        $user = $this->authService->getCurrentUser();
        if (!$user) {
            Response::sendAuthenticationError();
            return;
        }

        $result = $this->complaintService->delete((int)$params['id'], $user['id'], $user['role']);
        $this->sendResponse($result);
    }

    public function getById(array $params): void
    {
        $user = $this->authService->getCurrentUser();
        if (!$user) {
            Response::sendAuthenticationError();
            return;
        }

        $result = $this->complaintService->getById(
            (int)$params['id'],
            $user['id'],
            $user['role'],
            $_GET['status'] ?? null
        );
        $this->sendResponse($result);
    }

    public function getAll(): void
    {
        $user = $this->authService->getCurrentUser();
        if (!$user) {
            Response::sendAuthenticationError();
            return;
        }

        $result = $this->complaintService->getAll(
            $user['id'],
            $user['role'],
            $_GET['status'] ?? null,
            $_GET['search'] ?? null
        );
        $this->sendResponse($result);
    }

    public function updateStatus(array $params): void
    {
        $user = $this->authService->getCurrentUser();
        if (!$user) {
            Response::sendAuthenticationError();
            return;
        }

        $result = $this->complaintService->updateStatus(
            (int)$params['id'],
            $this->getJsonInput(),
            $user['id'],
            $user['role']
        );
        $this->sendResponse($result);
    }

    public function getAllFeedback(array $params): void
    {
        $user = $this->authService->getCurrentUser();
        if (!$user) {
            Response::sendAuthenticationError();
            return;
        }

        $result = $this->complaintService->getAllFeedback(
            (int)$params['id'],
            $user['id'],
            $user['role']
        );
        $this->sendResponse($result);
    }

    public function createFeedback(array $params): void
    {
        $user = $this->authService->getCurrentUser();
        if (!$user) {
            Response::sendAuthenticationError();
            return;
        }

        $result = $this->complaintService->createFeedback(
            (int)$params['id'],
            $this->getJsonInput(),
            $user['id'],
            $user['role']
        );
        $this->sendResponse($result, 201);
    }

    public function getFeedbackById(array $params): void
    {
        $user = $this->authService->getCurrentUser();
        if (!$user) {
            Response::sendAuthenticationError();
            return;
        }

        $result = $this->complaintService->getFeedbackById(
            (int)$params['id'],
            (int)$params['feedbackId'],
            $user['id'],
            $user['role']
        );
        $this->sendResponse($result);
    }

    public function updateFeedback(array $params): void
    {
        $user = $this->authService->getCurrentUser();
        if (!$user) {
            Response::sendAuthenticationError();
            return;
        }

        $result = $this->complaintService->updateFeedback(
            (int)$params['id'],
            (int)$params['feedbackId'],
            $this->getJsonInput(),
            $user['id'],
            $user['role']
        );
        $this->sendResponse($result);
    }

    public function deleteFeedback(array $params): void
    {
        $user = $this->authService->getCurrentUser();
        if (!$user) {
            Response::sendAuthenticationError();
            return;
        }

        $result = $this->complaintService->deleteFeedback(
            (int)$params['id'],
            (int)$params['feedbackId'],
            $user['id'],
            $user['role']
        );
        $this->sendResponse($result);
    }

    private function sendResponse(array $result, int $successCode = 200): void
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

        Response::sendSuccess($result['data'], $result['message'], $successCode);
    }
}
