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
    public function record()
    {
        $this->stats()->firstOrCreate(
            ['date' => now()->toDateString()],
            ['total_applied' => 0]
        );
        return $this->stats()->increment('total_applied');
    }
}