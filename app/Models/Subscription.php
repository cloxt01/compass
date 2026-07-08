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
            SubscriptionUsage::class
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

}
