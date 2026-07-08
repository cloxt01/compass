<?php

namespace App\Jobs\Middleware;

use App\Models\User;
use Illuminate\Support\Facades\Log;

class CheckProvider
{
    public function handle($job, $next)
    {
        $user = User::find($job->user_id);
        if(!$user){
            Log::warnig("User ID : " . $job->user_id . " tidak ditemukan, tidak dapat cek provider (CheckProvider middleware)");
            return;
        }
        $has = $user->isAnyConnectedProvider();
        if (!$has) {
            Log::warning('User ID : ' . $user->id. ', dilewati karena tidak ada provider yang terhubung');
            return;
        }
        return $next($job);
    }
}
