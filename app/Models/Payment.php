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

    const STATUS_PAID = 'paid';
    const STATUS_PENDING = 'pending';
    const STATUS_FAILED = 'failed';
    const STATUS_EXPIRED = 'expired';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_REFUND = 'refund';




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
