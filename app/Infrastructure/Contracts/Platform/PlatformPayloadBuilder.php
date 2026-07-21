<?php

namespace App\Infrastructure\Contracts\Platform;

interface PlatformPayloadBuilder
{
    public function build(array $details, array $profile, array $config): array;
}
