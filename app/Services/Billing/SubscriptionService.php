<?php

namespace App\Services\Billing;

use App\Models\Package;
use App\Models\Subscription;
use App\Models\User;

class SubscriptionService
{
    /**
     * Subscription aktif user.
     */
    public function current(User $user): ?Subscription
    {
        return $user->getLastActiveSubscription();
    }

    /**
     * User memiliki subscription?
     */
    public function hasSubscription(User $user): bool
    {
        return $this->current($user) !== null;
    }

    /**
     * Subscription masih aktif?
     */
    public function hasAccess(User $user): bool
    {
        $subscription = $this->current($user);

        if (!$subscription) {
            return false;
        }

        return $subscription->expires_at->isFuture();
    }

    /**
     * Membuat subscription baru.
     */
    public function create(User $user, Package $package): Subscription
    {
        return Subscription::create([
            'user_id'         => $user->id,
            'package_id'      => $package->id,
            'status'          => Subscription::STATUS_PENDING,
            'package_price'   => $package->price,
            'started_at'      => now(),
            'expires_at'      => now()->addDays($package->duration_days),
            'next_billing_at' => now()->addDays($package->duration_days),
            'auto_renew'      => true,
        ]);
    }

    /**
     * Aktivasi subscription.
     */
    public function activate(Subscription $subscription): Subscription
    {
        $subscription->update([
            'status' => Subscription::STATUS_ACTIVE,
        ]);

        return $subscription->fresh();
    }

    /**
     * Grace period.
     */
    public function grace(Subscription $subscription): void
    {
        $subscription->update([
            'status' => Subscription::STATUS_GRACE,
        ]);
    }

    /**
     * Expire.
     */
    public function expire(Subscription $subscription): void
    {
        $subscription->update([
            'status' => Subscription::STATUS_EXPIRED,
        ]);
    }

    /**
     * Cancel.
     */
    public function cancel(Subscription $subscription): void
    {
        $subscription->update([
            'status' => Subscription::STATUS_CANCELLED,
            'auto_renew' => false,
        ]);
    }

    /**
     * Renew subscription.
     */
    public function renew(Subscription $subscription): Subscription
    {
        $subscription->update([
            'status'          => Subscription::STATUS_ACTIVE,
            'started_at'      => now(),
            'expires_at'      => now()->addDays(
                $subscription->package->duration_days
            ),
            'next_billing_at' => now()->addDays(
                $subscription->package->duration_days
            ),
        ]);

        return $subscription->fresh();
    }

    /**
     * Sisa hari subscription.
     */
    public function remainingDays(Subscription $subscription): int
    {
        return max(
            0,
            now()->diffInDays($subscription->expires_at, false)
        );
    }

    /**
     * Apakah subscription sudah expired.
     */
    public function isExpired(Subscription $subscription): bool
    {
        return now()->greaterThan($subscription->expires_at);
    }
}
