<?php

namespace App\Services\Jobstreet;


use Illuminate\Support\Facades\DB;

class JobstreetDB
{

    public function __construct()
    {
    }

    public static function upsert_job(int $userId, string $jobId, string $type): bool
    {
        return DB::table('jobstreet_applications')->updateOrInsert((array)['job_id' => $jobId, 'user_id' => $userId, 'status' => $type, 'updated_at' => DB::raw('NOW()')]);
    }
}
