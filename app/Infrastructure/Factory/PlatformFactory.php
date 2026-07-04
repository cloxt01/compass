<?php

namespace App\Infrastructure\Factory;

use App\Clients\GlintsAPI;
use App\Clients\JobstreetAPI;
use App\Infrastructure\Contracts\PlatformAdapter;
use App\Models\User;
use App\Services\Adapters\GlintsAdapter;
use App\Services\Adapters\JobstreetAdapter;

class PlatformFactory
{
    public static function make(string $provider, User $user): ?PlatformAdapter {
        $account = match ($provider) {
            'glints' => $user->glintsAccount,
            'jobstreet' => $user->jobstreetAccount,
            default => null
        };
        if (!$account) {
            return null;
        }

        return match ($provider) {
            'glints' => (new GlintsAdapter(new GlintsAPI($account->access_token, $account->cookie))),
            'jobstreet' => (new JobstreetAdapter(new JobstreetAPI($account->access_token))),
            default => null
        };
    }
}
