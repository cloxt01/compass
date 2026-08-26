<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $fillable = [
        'signature',
        'last_run',
        'next_run',
        'last_status',
    ];
}
