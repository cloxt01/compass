<?php

namespace App\Observers;

use App\Models\JobstreetAccount;
use Illuminate\Support\Facades\Log;
use App\Jobs\RefreshJobstreetToken;

class JobstreetAccountObserver
{
    /**
     * Handle the JobstreetAccount "created" event.
     */
    public function retrieved(JobstreetAccount $account): void
    {
        if (app()->runningInConsole()) {
            return;
        }
        if ($account->isExpired() && $account->status === 'active') {
            $token = RefreshJobstreetToken::dispatch($account->id);
        }
    }
    /**
     * Handle the JobstreetAccount "created" event.
     */
    public function created(JobstreetAccount $jobstreetAccount): void
    {
        //
    }

    /**
     * Handle the JobstreetAccount "updated" event.
     */
    public function updated(JobstreetAccount $jobstreetAccount): void
    {
        //
    }

    /**
     * Handle the JobstreetAccount "deleted" event.
     */
    public function deleted(JobstreetAccount $jobstreetAccount): void
    {
        //
    }

    /**
     * Handle the JobstreetAccount "restored" event.
     */
    public function restored(JobstreetAccount $jobstreetAccount): void
    {
        //
    }

    /**
     * Handle the JobstreetAccount "force deleted" event.
     */
    public function forceDeleted(JobstreetAccount $jobstreetAccount): void
    {
        //
    }
}
