<?php

namespace App\Core;

class Response
{
    public static function sendSuccess($data = null, string $message = '', int $statusCode = 200): void
    {
        Logger::getInstance()->debug('Response::sendSuccess called', [
            'data' => $data,
            'message' => $message,
            'statusCode' => $statusCode
        ]);

        self::setHeaders($statusCode);
        $response = [
            'status' => 'success',
            'data' => $data,
            'message' => $message
        ];

        Logger::getInstance()->debug('Response::sendSuccess final response', ['response' => $response]);
        
        echo json_encode($response);
        exit;
    }

    public static function sendError(
        string $message,
        int $statusCode = 400,
        array $details = [],
        string $errorCode = ''
    ): void {
        $error = [
            'message' => $message,
            'code' => $statusCode
        ];

        if (!empty($details)) {
            $error['details'] = $details;
        }

        if (!empty($errorCode)) {
            $error['errorCode'] = $errorCode;
        }

        Logger::getInstance()->error($message, [
            'statusCode' => $statusCode,
            'errorCode' => $errorCode,
            'details' => $details,
            'url' => $_SERVER['REQUEST_URI'] ?? '',
            'method' => $_SERVER['REQUEST_METHOD'] ?? '',
            'ip' => $_SERVER['REMOTE_ADDR'] ?? ''
        ]);

        self::setHeaders($statusCode);
        echo json_encode([
            'status' => 'error',
            'error' => $error
        ]);
        exit;
    }

    public static function formatSuccess($data = null, string $message = ''): array
    {
        Logger::getInstance()->debug('Response::formatSuccess called', [
            'data' => $data,
            'message' => $message
        ]);

        $response = [
            'status' => 'success',
            'data' => $data,
            'message' => $message
        ];

        Logger::getInstance()->debug('Response::formatSuccess returning', ['response' => $response]);
        
        return $response;
    }

    public static function formatError(
        string $message,
        int $statusCode = 400,
        array $details = [],
        string $errorCode = ''
    ): array {
        $error = [
            'code' => $statusCode,
            'message' => $message
        ];

        if (!empty($details)) {
            $error['details'] = $details;
        }

        if (!empty($errorCode)) {
            $error['errorCode'] = $errorCode;
        }

        Logger::getInstance()->error($message, [
            'statusCode' => $statusCode,
            'errorCode' => $errorCode,
            'details' => $details,
            'url' => $_SERVER['REQUEST_URI'] ?? '',
            'method' => $_SERVER['REQUEST_METHOD'] ?? '',
            'ip' => $_SERVER['REMOTE_ADDR'] ?? ''
        ]);

        return [
            'status' => 'error',
            'error' => $error
        ];
    }

    private static function setHeaders(int $statusCode): void
    {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code($statusCode);
    }

    public static function sendAuthenticationError(string $message = 'Authentication required'): void
    {
        self::sendError($message, 401, [], 'AUTHENTICATION_REQUIRED');
    }

    public static function sendAuthorizationError(string $message = 'Access denied'): void
    {
        self::sendError($message, 403, [], 'ACCESS_DENIED');
    }

    public static function sendValidationError(array $validationErrors): void
    {
        $details = array_map(function($field, $issue) {
            return [
                'field' => $field,
                'issue' => $issue
            ];
        }, array_keys($validationErrors), array_values($validationErrors));

        self::sendError('Validation failed', 422, $details, 'VALIDATION_ERROR');
    }
}
