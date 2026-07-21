<?php

namespace App\Infrastructure\Contracts\AI;


interface AIAdapter
{
    public function autoAnswer(string $provider, array $profile, array $questionnaire): array;
}
