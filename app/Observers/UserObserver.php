<?php

namespace App\Observers;

use App\Models\Package;
use App\Models\Subscription;
use App\Models\User;
use App\Services\Billing\SubscriptionService;
use App\Services\Billing\UsageService;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        $free = Package::where('code', '=', Package::CODE_FREE)->first();


        $subscription = app(SubscriptionService::class)
            ->create($user, $free);
        $subscription->update([
            'status' => Subscription::STATUS_ACTIVE
        ]);
        $subscription->save();

        app(UsageService::class)
            ->create($subscription);
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        //
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        //
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }
}
