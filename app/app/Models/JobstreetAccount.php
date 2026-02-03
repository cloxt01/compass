<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobstreetAccount extends Model
{
     protected $fillable = [
        'user_id',
        'access_token',
        'refresh_token',
        'expires_at',
        'status',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}