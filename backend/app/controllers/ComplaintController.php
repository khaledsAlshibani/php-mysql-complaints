<?php

namespace App\Controllers;

use App\Services\ComplaintService;

class ComplaintController extends UserSubmissionController
{
    public function __construct()
    {
        parent::__construct(new ComplaintService());
    }
}
