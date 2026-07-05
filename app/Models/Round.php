<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Round extends Model
{
    protected $fillable = [
        'status',
        'total_user',
        'total_dispatched',
        'total_success',
        'total_failed',
        'duration_ms',
        'started_at',
        'finished_at',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'timestamp',
            'finished_at' => 'timestamp',
        ];
    }
}
