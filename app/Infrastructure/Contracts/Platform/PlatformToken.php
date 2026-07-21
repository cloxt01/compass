<?php

namespace App\Infrastructure\Contracts\Platform;

interface PlatformToken
{
    public function refreshToken(string $refreshToken): ?array;
}
