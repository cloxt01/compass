<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

class ApplicationHelper
{
    public static function alreadyApplied($userId, $jobId): ?string
    {
        return DB::table('applications')
            ->where('user_id', $userId)
            ->where('job_id', $jobId)
            ->value('status');
    }
}
