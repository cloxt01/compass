<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Infrastructure\Contracts\PlatformAccount;

class JobstreetAccount extends Model implements PlatformAccount 
{
     protected $fillable = [
        'user_id',
        'access_token',
        'refresh_token',
        'expires_at',
        'status',
        'apply_configurations',

    ];
    protected $casts = [
        'expires_at' => 'datetime',
        'apply_configurations' => 'array',
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
    public function saveConfig(string $key, $value){
        $configs = $this->apply_configurations;
        $configs[$key] = $value;
        $this->apply_configurations = $configs;
        $this->save();
    }
    public function getConfig(?string $key = null, $default = []): mixed {
        if($key === null){
            return $this->apply_configurations ?? [];
        }
        return $this->apply_configurations[$key] ?? $default;
    }
}