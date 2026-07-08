<?php

namespace App\Jobs\Middleware;

use App\Models\User;
use Illuminate\Support\Facades\Log;

class CheckSubscription
{
    public function handle($job, $next)
    {
        $user = User::find($job->user_id);
        if(!$user){
            Log::warnig("User ID : " . $job->user_id . " tidak ditemukan, tidak dapat cek subscription (CheckSubscription middleware)");
            return;
        }
        $subscription = $user->getLastActive();
        if (!$subscription) {
            Log::warning('User ID : ' . $user->id. ', dilewati karena tidak memiliki subscription aktif');
            return;
        }
        return $next($job);
    }
}
