<?php

namespace App\Models;

use App\Infrastructure\Contracts\PlatformAccount;
use Illuminate\Database\Eloquent\Model;

class GlintsAccount extends Model implements PlatformAccount
{
    protected $table = 'glints_accounts';
    protected $fillable = [
        'user_id',
        'access_token',
        'cookie',
        'status',
        'apply_configuration'
    ];

    protected $casts = [
        'apply_configuration' => 'array',
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function saveConfig(string $key, $value): bool{
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
}
