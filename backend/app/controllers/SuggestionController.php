<?php

namespace App\Controllers;

use App\Services\SuggestionService;

class SuggestionController extends UserSubmissionController
{
    public function __construct()
    {
        parent::__construct(new SuggestionService());
    }
}