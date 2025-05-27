<?php

namespace App\Services;

use App\Models\Complaint;
use App\Models\UserSubmissionModel;
use App\DTOs\ComplaintDTO;
use App\DTOs\ComplaintStatusDTO;
use App\DTOs\UserSubmissionDTO;
use App\DTOs\UserSubmissionStatusDTO;

class ComplaintService extends UserSubmissionService
{
    protected function getModel(): UserSubmissionModel
    {
        return new Complaint();
    }

    protected function getDTO(array $data): UserSubmissionDTO
    {
        return new ComplaintDTO($data);
    }

    protected function getStatusDTO(array $data): UserSubmissionStatusDTO
    {
        return new ComplaintStatusDTO($data);
    }
}