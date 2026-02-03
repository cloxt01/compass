<?php

namespace App\Jobs;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;


class Job {
    public function job_status($jobId)
    {
        $status = Redis::hgetall("job:$jobId");
        return response()->json([
            'job_id' => $jobId,
            'status' => $status['status'] ?? 'UNKNOWN',
            'data' => $status
        ], 200);
    }

}