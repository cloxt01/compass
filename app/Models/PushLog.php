<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PushLog extends Model
{
    protected $fillable = [
        'user_id',
        'keyword',
        'batch',
        'glints',
        'jobstreet',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected function casts(): array
    {
        return [
            'glints' => 'boolean',
            'jobstreet' => 'boolean',
        ];
    }
}
