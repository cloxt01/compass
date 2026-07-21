<?php

namespace App\Services\AI;

class AIPromptBuilder
{
    public static function build(
        array $profile,
        array $payload
    ): array {

        return [
            'system' => self::system(),
            'user' => self::user(
                $profile,
                $payload
            ),
        ];
    }

    protected static function system(): string
    {
        return config('ai.prompt.system');
    }

    protected static function user(
        array $profile,
        array $payload
    ): string {

        return json_encode([
            'profile' => $profile,
            'question' => $payload,
        ], JSON_UNESCAPED_UNICODE);
    }
}
