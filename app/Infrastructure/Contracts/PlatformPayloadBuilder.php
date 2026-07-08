<?php

namespace App\Infrastructure\Contracts;

interface PlatformPayloadBuilder
{
    public function build(array $details, array $profile, array $config): array;
}
