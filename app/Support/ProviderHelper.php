<?php

namespace App\Support;

use App\Models\GlintsAccount;
use function Pest\Laravel\instance;

class ProviderHelper
{
    const ALLOWED_PROVIDERS = [
        'glints',
        'jobstreet'
    ];
    public static function who($account): ?string
    {
        switch (class_basename($account)) {
            case 'GlintsAccount':
                return 'glints';
            case 'JobstreetAccount':
                return 'jobstreet';
            default:
                return null;
        }
    }

    public static function isAllowed(string $provider): bool
    {
        return in_array($provider, self::ALLOWED_PROVIDERS);
    }

}
