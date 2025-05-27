<?php

namespace App\Models;

class Complaint extends UserSubmissionModel
{
    protected function getTable(): string
    {
        return 'complaints';
    }

    protected function getTableAlias(): string
    {
        return 'c';
    }
}