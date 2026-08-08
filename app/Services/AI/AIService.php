<?php

namespace App\Services\AI;

use Prism\Prism\Facades\Prism;

class AIService
{
    public function __construct(){}

    public function chat(string $message, string $systemPrompt = ""): array
    {
        return Prism::text()
            ->using('openrouter', 'deepseek/deepseek-v4-flash')
            ->withSystemPrompt($systemPrompt)
            ->withProviderOptions([
                'provider' => [
                    'order' => ['alibaba'],
                    'allow_fallbacks' => false,
                ],
                'reasoning' => [
                    'enabled' => true,
                    'effort' => 'low'
                ]
            ])

            ->withMaxTokens(4000)
            ->withPrompt($message)
            ->asText()
            ->toArray();
    }
}
