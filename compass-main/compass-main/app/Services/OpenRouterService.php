<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class OpenRouterService
{
    public const DEFAULT_MODEL = 'deepseek/deepseek-v4-flash';
    public const DEFAULT_TEMPERATURE = 0.35;
    public const DEFAULT_MAX_TOKENS = 800;

    /**
     * Resolve OpenRouter config from user override first, then env.
     */
    public function resolveConfig(?User $user = null): array
    {
        $override = [];
        if ($user) {
            $configuration = $user->apply_configuration;
            if (is_array($configuration) && is_array($configuration['career_ai'] ?? null)) {
                $override = $configuration['career_ai'];
            }
        }

        $envKey = (string) config('services.openrouter.key');
        $envModel = (string) config('services.openrouter.model');

        $apiKey = isset($override['api_key']) && is_string($override['api_key']) && trim($override['api_key']) !== ''
            ? trim($override['api_key'])
            : $envKey;

        $model = isset($override['model']) && is_string($override['model']) && trim($override['model']) !== ''
            ? trim($override['model'])
            : ($envModel !== '' ? $envModel : self::DEFAULT_MODEL);

        $temperature = self::DEFAULT_TEMPERATURE;
        if (isset($override['temperature']) && is_numeric($override['temperature'])) {
            $temperature = max(0.0, min(2.0, (float) $override['temperature']));
        }

        $maxTokens = self::DEFAULT_MAX_TOKENS;
        if (isset($override['max_tokens']) && is_numeric($override['max_tokens'])) {
            $maxTokens = max(64, min(4096, (int) $override['max_tokens']));
        }

        return [
            'api_key' => $apiKey,
            'model' => $model,
            'temperature' => $temperature,
            'max_tokens' => $maxTokens,
        ];
    }

    public function answerScreening(string $question, string $candidateContext, string $jobContext = '', ?User $user = null): string
    {
        $config = $this->resolveConfig($user);

        if (empty($config['api_key'])) {
            throw new RuntimeException('API key OpenRouter belum diatur. Silakan isi di Settings → AI Provider.');
        }

        $response = Http::withToken($config['api_key'])
            ->acceptJson()
            ->timeout(45)
            ->post('https://openrouter.ai/api/v1/chat/completions', [
                'model' => $config['model'],
                'temperature' => $config['temperature'],
                'max_tokens' => $config['max_tokens'],
                'messages' => [
                    ['role' => 'system', 'content' => 'Kamu adalah asisten lamaran kerja. Jawab dalam Bahasa Indonesia, jujur, spesifik, profesional, dan jangan mengarang pengalaman kandidat. Berikan hanya jawaban yang siap ditempel ke formulir screening.'],
                    ['role' => 'user', 'content' => "Pertanyaan screening:\n{$question}\n\nProfil kandidat:\n{$candidateContext}\n\nKonteks lowongan:\n{$jobContext}"],
                ],
            ]);

        if ($response->failed()) {
            $errorMsg = data_get($response->json(), 'error.message');
            throw new RuntimeException('OpenRouter gagal memproses jawaban' . ($errorMsg ? ": {$errorMsg}" : '. Silakan cek model atau API key.'));
        }

        $content = data_get($response->json(), 'choices.0.message.content');
        if (!is_string($content)) {
            throw new RuntimeException('OpenRouter mengembalikan format jawaban yang tidak valid.');
        }
        $answer = trim($content);
        if ($answer === '') {
            throw new RuntimeException('OpenRouter mengembalikan jawaban kosong. Silakan coba lagi.');
        }

        return $answer;
    }

    /**
     * Ask the AI to provide detailed reasoning (Bahasa Indonesia) about a match score.
     */
    public function reasonMatch(string $candidate, string $job, array $breakdown, int $totalScore, ?User $user = null): string
    {
        $config = $this->resolveConfig($user);

        if (empty($config['api_key'])) {
            // AI reasoning is optional – return empty string when not configured so caller can fallback.
            return '';
        }

        $breakdownLines = [];
        foreach ($breakdown as $key => $item) {
            $breakdownLines[] = "- {$key}: skor {$item['score']}%, bobot {$item['weight']}%, kontribusi {$item['contribution']}";
        }
        $breakdownText = implode("\n", $breakdownLines);

        $response = Http::withToken($config['api_key'])
            ->acceptJson()
            ->timeout(45)
            ->post('https://openrouter.ai/api/v1/chat/completions', [
                'model' => $config['model'],
                'temperature' => $config['temperature'],
                'max_tokens' => $config['max_tokens'],
                'messages' => [
                    ['role' => 'system', 'content' => 'Kamu adalah asisten HR yang membantu kandidat memahami kecocokannya dengan lowongan. Jawab dalam Bahasa Indonesia, ringkas (maks 5 kalimat), jujur, dan berikan rekomendasi konkret.'],
                    ['role' => 'user', 'content' => "Profil kandidat:\n{$candidate}\n\nLowongan:\n{$job}\n\nSkor akhir: {$totalScore}%\n\nRincian per kategori:\n{$breakdownText}\n\nJelaskan mengapa skornya sekian dan area apa yang perlu diperkuat."],
                ],
            ]);

        if ($response->failed()) {
            return '';
        }

        $content = data_get($response->json(), 'choices.0.message.content');
        return is_string($content) ? trim($content) : '';
    }

    /**
     * Ping OpenRouter with the given (or resolved) config and a minimal prompt.
     * Returns ['ok' => bool, 'message' => string, 'latency_ms' => int, 'model' => string].
     */
    public function testConnection(array $overrides = [], ?User $user = null): array
    {
        $config = $this->resolveConfig($user);

        // Allow inline overrides (e.g. values from form that user hasn't saved yet)
        if (isset($overrides['model']) && is_string($overrides['model']) && trim($overrides['model']) !== '') {
            $config['model'] = trim($overrides['model']);
        }
        if (isset($overrides['api_key']) && is_string($overrides['api_key']) && trim($overrides['api_key']) !== '') {
            $config['api_key'] = trim($overrides['api_key']);
        }

        if (empty($config['api_key'])) {
            return ['ok' => false, 'message' => 'API key belum diisi.', 'latency_ms' => 0, 'model' => $config['model']];
        }

        $start = microtime(true);
        try {
            $response = Http::withToken($config['api_key'])
                ->acceptJson()
                ->timeout(20)
                ->post('https://openrouter.ai/api/v1/chat/completions', [
                    'model' => $config['model'],
                    'temperature' => 0,
                    'max_tokens' => 8,
                    'messages' => [
                        ['role' => 'system', 'content' => 'Balas hanya dengan kata "OK".'],
                        ['role' => 'user', 'content' => 'ping'],
                    ],
                ]);
        } catch (\Throwable $e) {
            return ['ok' => false, 'message' => 'Gagal menghubungi OpenRouter: ' . $e->getMessage(), 'latency_ms' => (int) round((microtime(true) - $start) * 1000), 'model' => $config['model']];
        }

        $latency = (int) round((microtime(true) - $start) * 1000);

        if ($response->failed()) {
            $errorMsg = data_get($response->json(), 'error.message') ?: ('HTTP ' . $response->status());
            return ['ok' => false, 'message' => $errorMsg, 'latency_ms' => $latency, 'model' => $config['model']];
        }

        $content = data_get($response->json(), 'choices.0.message.content');
        if (!is_string($content) || trim($content) === '') {
            return ['ok' => false, 'message' => 'Respons OpenRouter kosong / tidak valid.', 'latency_ms' => $latency, 'model' => $config['model']];
        }

        return [
            'ok' => true,
            'message' => 'Berhasil terhubung. Contoh respons: "' . mb_substr(trim($content), 0, 40) . '"',
            'latency_ms' => $latency,
            'model' => $config['model'],
        ];
    }
}
