<?php

namespace App\Services;

use App\Models\Suggestion;
use App\Models\UserSubmissionModel;
use App\DTOs\SuggestionDTO;
use App\DTOs\SuggestionStatusDTO;
use App\DTOs\UserSubmissionDTO;
use App\DTOs\UserSubmissionStatusDTO;

class SuggestionService extends UserSubmissionService
{
    protected function getModel(): UserSubmissionModel
    {
        return new Suggestion();
    }

    protected function getDTO(array $data): UserSubmissionDTO
    {
        return new SuggestionDTO($data);
    }

    protected function getStatusDTO(array $data): UserSubmissionStatusDTO
    {
        return new SuggestionStatusDTO($data);
    }
}