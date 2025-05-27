<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Response;
use App\Services\UserSubmissionService;
use App\Services\AuthService;

/**
 * Abstract controller for handling user submissions like complaints and suggestions
 * 
 * This controller provides common operations for user-submitted content including:
 * - Creating new submissions
 * - Retrieving submissions by various criteria
 * - Updating submission status and content
 * - Deleting submissions
 * - Feedback management
 * 
 * Child classes must provide the appropriate UserSubmissionService instance.
 * 
 * @package App\Controllers
 * @abstract
 */
abstract class UserSubmissionController extends Controller
{
    protected UserSubmissionService $service;
    private AuthService $authService;

    public function __construct(UserSubmissionService $service)
    {
        $this->service = $service;
        $this->authService = new AuthService();
    }

    public function create(): void
    {
        $user = $this->authService->getCurrentUser();
        if (!$user) {
            Response::sendAuthenticationError();
            return;
        }

        $result = $this->service->create($this->getJsonInput(), $user['id']);
        $this->sendResponse($result);
    }

    public function update(array $params): void
    {
        $user = $this->authService->getCurrentUser();
        if (!$user) {
            Response::sendAuthenticationError();
            return;
        }

        $result = $this->service->update(
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

        $result = $this->service->delete((int)$params['id'], $user['id'], $user['role']);
        $this->sendResponse($result);
    }

    public function getById(array $params): void
    {
        $user = $this->authService->getCurrentUser();
        if (!$user) {
            Response::sendAuthenticationError();
            return;
        }

        $result = $this->service->getById(
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

        $result = $this->service->getAll(
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

        $result = $this->service->updateStatus(
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

        $result = $this->service->getAllFeedback(
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

        $result = $this->service->createFeedback(
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

        $result = $this->service->getFeedbackById(
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

        $result = $this->service->updateFeedback(
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

        $result = $this->service->deleteFeedback(
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
