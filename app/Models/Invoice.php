<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    protected $fillable = [
        'subscription_id',
        'invoice_number',
        'status',
        'subtotal',
        'discount',
        'tax',
        'total',
        'due_date',
        'notes',
        'paid_at',
    ];

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }


    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'paid_at' => 'datetime',
        ];
    }


}
