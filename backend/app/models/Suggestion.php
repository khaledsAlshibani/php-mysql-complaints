<?php

namespace App\Models;

class Suggestion extends UserSubmissionModel
{
    protected function getTable(): string
    {
        return 'suggestions';
    }

    protected function getTableAlias(): string
    {
        return 's';
    }
}