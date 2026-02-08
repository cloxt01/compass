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
    public function isExpired(int $bufferSeconds = 60): bool
    {
        if (!$this->expired_at instanceof Carbon) {
            return true;
        }

        return now()->addSeconds($bufferSeconds)->greaterThanOrEqualTo($this->expired_at);
    }
    public function updateToken(array $token)
    {
        if(!isset($token['access_token'])){
            return;
        }
        $this->access_token = $token['access_token'];
        $this->refresh_token = $token['refresh_token'];
        $this->expires_at = now()->addSeconds($token['expires_in'] - 60);
        $this->save();
    }
}