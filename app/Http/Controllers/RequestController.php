<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;


class RequestController {
    public function request_info(string $id)
    {
        $status = Redis::hgetall("job:$id");
        return response()->json([
            'request_id' => $id,
            'status' => $status['status'] ?? 'UNKNOWN',
            'data' => $status
        ], 200);
    }

}