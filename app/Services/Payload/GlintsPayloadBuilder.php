<?php

namespace App\Services\Payload;

use App\Infrastructure\Contracts\PlatformPayloadBuilder;

class GlintsPayloadBuilder implements PlatformPayloadBuilder
{
    public function __construct()
    {}

    public function build(array $details, array $profile, array $config): array
    {

        return [
            'data' => [
                'resume' => $profile['resume'],
                'employerScreeningQuestionAnswers' => [],
                'note' => '',
                'attachments' => []
            ],
            'source' => 'For You',
            'traceInfo' => $config['traceInfo'] ?? bin2hex(random_bytes(16)),
        ];
    }
}
