<?php

namespace App\Services\Queue;

use App\Jobs\ProcessApplications;
use App\Models\ApplyQueue;
use App\Models\User;
use Illuminate\Support\Facades\Queue;

class ApplicationQueue
{
    public static function dispatch(User $user, string $provider, array $job, int $round_id): string
    {
        $queueId = Queue::connection('database')->push(
            new ProcessApplications($user, $provider, $job)
        );

        ApplyQueue::create([
            'job_id' => $queueId,
            'user_id' => $user->id,
            'round_id' => $round_id,
        ]);

        return $queueId;
    }
}
