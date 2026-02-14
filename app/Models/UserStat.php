<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserStat extends Model
{
    protected $fillable = ['user_id', 'date', 'total_applied'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
}