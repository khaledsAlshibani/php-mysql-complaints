<?php

namespace App\Core;

abstract class Controller
{
    protected function sendJson($data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        
        // This was added for unit testing using phpunit
        // Only exit in non-test environment
        // if (php_sapi_name() !== 'cli') {
        //     exit;
        // }
    }

    protected function sendError(string $message, int $statusCode = 400): void
    {
        $this->sendJson(['error' => $message], $statusCode);
    }

    protected function getJsonInput(): ?array
    {
        $input = file_get_contents('php://input');
        if (empty($input)) {
            $this->sendError('No input data provided');
            return null;
        }

        $data = json_decode($input, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->sendError('Invalid JSON payload: ' . json_last_error_msg());
            return null;
        }
        
        return $data;
    }

    protected function validateRequired(array $data, array $fields): bool
    {
        foreach ($fields as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                $this->sendError("Missing required field: {$field}");
                return false;
            }
        }
        return true;
    }
}
