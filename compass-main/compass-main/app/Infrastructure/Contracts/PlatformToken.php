<?php

namespace App\Infrastructure\Contracts;

use App\Infrastructure\Contracts\PlatformAccount;

interface PlatformToken
{
    public function refreshToken(string $refreshToken): ?array;
}