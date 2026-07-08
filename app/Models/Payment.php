<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'invoice_id',
        'gateway',
        'gateway_transaction_id',
        'method',
        'reference',
        'amount',
        'status',
        'paid_at',
        'redirect_url',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
    protected function casts(): array
    {
        return [
            'paid_at' => 'datetime'
        ];
    }
}
