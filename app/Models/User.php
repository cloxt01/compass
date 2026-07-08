<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Notifications\CustomResetPassword;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'apply_configuration',
        'last_apply_configuration',
        'status',
        'automation_paused',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'apply_configuration' => 'array',
            'last_apply_configuration' => 'array',
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function jobstreetAccount()
    {
        return $this->hasOne(JobstreetAccount::class, 'user_id');
    }
    public function glintsAccount()
    {
        return $this->hasOne(GlintsAccount::class, 'user_id');
    }
    public function applications()
    {
        return $this->hasMany(Application::class, 'user_id');
    }
    public function isAutomationPaused(): bool {
        return $this->automation_paused;
    }
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class, 'user_id');
    }

    public function getLastActiveSubscription(): ?Subscription
    {
        return $this->subscriptions()->whereIn('status', ['active', 'grace'])
            ->latest('started_at')
            ->first();
    }
    public function isAnyConnectedProvider(): bool
    {
        return ($this->glintsAccount ?? false) || ($this->jobstreetAccount ?? false);
    }


    public function sendPasswordResetNotification($token)
    {
        $this->notify(new CustomResetPassword($token));
    }

}
