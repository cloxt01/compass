<?php

namespace App\Jobs\Middleware;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use App\Infrastructure\Factory\PlatformFactory;
use App\Events\JobStatus;

class CheckProviderRateLimit
{
    public function handle($job, $next)
    {
        $user = User::find($job->user_id);
        $adapter = PlatformFactory::make($job->provider, $user);

        $limit = $adapter->is_limit();

        if($limit){
            JobStatus::dispatch($user->id, $job->jobData, 'limit_provider');
            Log::warning('User ID : ' . $user->id. ', dilewati karena limit provider tercapai.');
            return;
        }
        return $next($job);
    }
}
