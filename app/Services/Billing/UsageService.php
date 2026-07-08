<?php

namespace App\Services\Billing;

use App\Models\Subscription;
use App\Models\SubscriptionUsage;
use App\Models\User;

class UsageService
{
    public function __construct(
        protected SubscriptionService $subscriptionService
    ) {}

    public function create(Subscription $subscription)
    {
        return SubscriptionUsage::create([
            'subscription_id' => $subscription->id,
            'date' => today(),
            'apply_count' => 0,
        ]);
    }

    public function canApply(
        Subscription $subscription
    ): bool
    {
        $usage = SubscriptionUsage::firstOrCreate(
            [
                'subscription_id' => $subscription->id,
                'date' => today(),
            ],
            [
                'apply_count' => 0,
            ]
        );

        if (
            $usage->apply_count >=
            $subscription->package->daily_limit
        ) {
            return false;
        }

        $monthly = SubscriptionUsage::query()

            ->where(
                'subscription_id',
                $subscription->id
            )

            ->whereMonth(
                'date',
                now()->month
            )

            ->whereYear(
                'date',
                now()->year
            )

            ->sum('apply_count');

        return $monthly <
            $subscription->package->monthly_limit;
    }
    public function increment(
        Subscription $subscription,
        int $count = 1
    ): void
    {
        $usage = SubscriptionUsage::firstOrCreate(
            [
                'subscription_id' => $subscription->id,
                'date' => today(),
            ],
            [
                'apply_count' => 0,
            ]
        );

        $usage->increment(
            'apply_count',
            $count
        );
    }
}
