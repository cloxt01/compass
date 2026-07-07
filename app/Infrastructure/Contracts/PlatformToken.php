<?php

namespace App\Infrastructure\Contracts;

interface PlatformToken
{
    public function refreshToken(string $refreshToken): ?array;
}
