<?php

namespace App\Jobs\Middleware;

use App\Models\User;
use Illuminate\Support\Facades\Log;

class CheckProvider
{
    public function handle($job, $next)
    {
        $user = User::find($job->user_id);
        $adapter = PlatformFactory::make($job->provider, $user);

        $limit = $adapter->is_limit($job->job_id);

        if($limit){
            Log::warning('User ID : ' . $user->id. ', dilewati karena limit provider tercapai.');
            return;
        }
        return $next($job);
    }
}
