<?php
namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Services\Token\JobstreetToken;
use App\Infrastructure\Contracts\PlatformAccount;

class JobstreetAccount extends Model implements PlatformAccount
{
    protected $table = 'jobstreet_accounts';
     protected $fillable = [
        'user_id',
        'access_token',
        'refresh_token',
        'expired_at',
        'status',
        'apply_configuration',

    ];
    protected $casts = [
        'expired_at' => 'datetime',
        'apply_configuration' => 'array',
    ];


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function isExpired(int $bufferSeconds = 300): bool
    {
        if(!$this->expired_at){
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
        $this->expired_at = now()->addSeconds(3600);
        $this->save();
    }
    public function updateStatus (string $status): bool {
        if($this->status != $status){
            $this->status  = $status;
        }
        return $this->save() ?? false;
    }
    public function saveConfig(string $key, $value): bool {
        $configs = $this->apply_configuration ?? [];
        $configs[$key] = $value;
        $this->apply_configuration = $configs;
        return $this->save();
    }
    public function getConfig(?string $key = null, $default = []): mixed {
        if($key === null){
            return $this->apply_configuration ?? [];
        }
        return $this->apply_configuration[$key] ?? $default;
    }

    public function getApplyConfigurationsAttribute(): array
    {
        return $this->apply_configuration ?? [];
    }

    public function setApplyConfigurationsAttribute($value): void
    {
        $this->attributes['apply_configuration'] = is_array($value) ? json_encode($value) : $value;
    }
}
