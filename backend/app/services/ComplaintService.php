<?php

namespace App\Services;

use App\Models\Complaint;
use App\Models\UserSubmissionModel;
use App\DTO\ComplaintDTO;
use App\DTO\ComplaintStatusDTO;
use App\DTO\UserSubmissionDTO;
use App\DTO\UserSubmissionStatusDTO;

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