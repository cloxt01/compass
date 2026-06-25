<?php

namespace App\Services\Glints;


use Illuminate\Support\Facades\DB;

class GlintsDB
{

    public function __construct()
    {
    }

    public static function upsert_job(int $userId, string $jobId, string $type): bool
    {
        return DB::table('glints_applications')->updateOrInsert((array)['job_id' => $jobId, 'user_id' => $userId, 'status' => $type, 'updated_at' => DB::raw('NOW()')]);
    }
}
