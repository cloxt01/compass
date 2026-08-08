<?php

namespace App\Services\Adapters\AI;

use Illuminate\Support\Facades\Log;
use App\Services\AI\AIPayloadBuilder;
use App\Services\AI\AIPromptBuilder;
use App\Services\AI\AIService;
use App\Support\QuestionNormalizer;


class AIAdapter implements \App\Infrastructure\Contracts\AI\AIAdapter
{
    protected $service;
    public function __construct(
    )
    {
        $this->service = new AIService();
    }
    public function autoAnswer(string $provider, array $profile, array $questionnaire): array
    {

        $normalized = QuestionNormalizer::normalize($provider, $questionnaire);
        Log::info("Normalized : ".json_encode($normalized));


        $profile = json_decode('{
            "nama": "Muhammad Ferdiansyah",
            "pendidikan": "S1",
            "pengalaman": "Fresh Graduate",
            "lokasi": "Lebak, Banten",
            "gaji_terakhir": null,
            "ekspektasi_gaji": "Rp5.000.000",
            "notice_period": "ASAP",
            "bersedia_industri_banking": true
        }', true);
        $prompt = AIPromptBuilder::build($profile, $normalized);
        Log::info("AI Prompt : ".json_encode($prompt));
        $response = $this->service->chat(
            $prompt['user'],
            $prompt['system']
        );
        Log::info("AI Response : ".json_encode($response));
        return $response;
    }

}
