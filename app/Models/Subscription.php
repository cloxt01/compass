<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use phpDocumentor\Reflection\Types\This;

class Subscription extends Model
{
    protected $fillable = [
        'user_id',
        'package_id',
        'status',
        'package_price',
        'started_at',
        'expires_at',
        'next_billing_at',
        'auto_renew',
    ];

    const STATUS_ACTIVE = 'active';
    const STATUS_PENDING = 'pending';
    const STATUS_GRACE = 'grace';
    const STATUS_EXPIRED = 'expired';
    const STATUS_CANCELLED = 'cancelled';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
    public function usages()
    {
        return $this->hasMany(
            SubscriptionUsage::class, "subscription_id"
        );
    }
    protected function casts(): array
    {
        return [
            'auto_renew' => 'boolean',
            'started_at' => 'datetime',
            'expires_at' => 'datetime',
            'next_billing_at' => 'datetime',
        ];
    }

    public function isLimit(): bool
    {
        $package = $this->package;

        $monthlyUsage = $this->usages()
            ->whereYear('date', date('Y'))
            ->whereMonth('date', date('m'))
            ->sum('apply_count');
        $dailyUsage = $this->usages()
            ->where('date', today())
            ->sum('apply_count');

        if($monthlyUsage >= $package->monthly_limit || $dailyUsage >= $package->daily_limit){
            return true;
        }
        return false;
    }

}
