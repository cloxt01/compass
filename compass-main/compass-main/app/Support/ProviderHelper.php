<?php

namespace App\Support;

use App\Models\GlintsAccount;
use function Pest\Laravel\instance;

class ProviderHelper
{
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

}
