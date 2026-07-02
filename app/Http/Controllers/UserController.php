<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use function Pest\Laravel\json;

class UserController
{
    public function application_stats(Request $request)
    {
        $userId = auth()->id();
        $today = now()->startOfDay(); // Mendapatkan waktu mulai hari ini (00:00:00)

        $stats = DB::table('applications')
            ->where('user_id', $userId)
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('SUM(CASE WHEN status IN ("success", "applied") THEN 1 ELSE 0 END) as applied')
            ->selectRaw('SUM(CASE WHEN status = "success" THEN 1 ELSE 0 END) as success')
            ->selectRaw('SUM(CASE WHEN created_at >= ? AND (status = "success" OR status = "applied") THEN 1 ELSE 0 END) as today_count', [$today])
            ->first();

        return response()->json([
            'stats' => [
                'total'       => (int) $stats->total,
                'applied'     => (int) $stats->applied,
                'success'     => (int) $stats->success,
                'today_count' => (int) $stats->today_count,
            ]
        ]);
    }

    public function queue_status(Request $request)
    {
        $userId = auth()->id();

        $query = DB::table('jobs')
            ->join('apply_queue', 'jobs.id', '=', 'apply_queue.job_id')
            ->where('apply_queue.user_id', $userId);

        $pending = (clone $query)->whereNull('jobs.reserved_at')->count();

        $processing = (clone $query)->whereNotNull('jobs.reserved_at')->count();

        $recent = (clone $query)
            ->select('jobs.*')
            ->orderBy('jobs.created_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'pending'    => $pending,
            'processing' => $processing,
            'recent'     => $recent
        ]);
    }
}
