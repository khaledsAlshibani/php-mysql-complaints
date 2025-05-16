<?php

namespace App\Core;

class Response
{
    public static function sendSuccess(?array $data = null, string $message = 'Request completed successfully', int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode(self::formatSuccess($data, $message));
    }

    public static function formatSuccess(?array $data = null, string $message = 'Request completed successfully'): array
    {
        return [
            'status' => 'success',
            'data' => $data,
            'message' => $message
        ];
    }

    public static function sendError(
        string $message,
        int $statusCode = 400,
        array $details = [],
        ?string $customErrorCode = null
    ): void {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode(self::formatError($message, $statusCode, $details, $customErrorCode));
    }

    public static function formatError(
        string $message,
        int $statusCode = 400,
        array $details = [],
        ?string $customErrorCode = null
    ): array {
        $error = [
            'code' => $statusCode,
            'message' => $message
        ];

        if (!empty($details)) {
            $error['details'] = $details;
        }

        if ($customErrorCode) {
            $error['errorCode'] = $customErrorCode;
        }

        return [
            'status' => 'error',
            'error' => $error
        ];
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
