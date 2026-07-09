<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    const CODE_FREE = 'FREE';

    protected $fillable = [
        'code',
        'name',
        'price',
        'duration_days',
        'daily_limit',
        'monthly_limit',
        'features',
        'is_active',
    ];

    protected $casts = [
        'features' => 'array',
        'is_active' => 'boolean',
    ];

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }
}
