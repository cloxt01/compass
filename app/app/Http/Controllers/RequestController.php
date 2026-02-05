<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;


class RequestController {
    public function request_info($request_id)
    {
        $status = Redis::hgetall("job:$request_id");
        return response()->json([
            'request_id' => $request_id,
            'status' => $status['status'] ?? 'UNKNOWN',
            'data' => $status
        ], 200);
    }

}