<?php

namespace App\Services\AI;

use App\Models\ApplicationAiAnswer;
use App\Models\User;
use App\Services\OpenRouterService;
use App\Support\QuestionNormalizer;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * Orchestrator untuk auto-answer screening question.
 *
 * Flow:
 * 1. Normalize questionnaire → format standar (via QuestionNormalizer)
 * 2. Build profile kandidat (via ProfileBuilder)
 * 3. Kirim ke OpenRouter dengan prompt jujur (tidak boleh mengarang)
 * 4. Parse JSON response + hitung match_score dari per-question confidence
 * 5. Simpan ke tabel application_ai_answers
 * 6. Kembalikan struktur untuk consumer (AnswerTransformer)
 */
class AutoAnswerService
{
    public function __construct(
        private OpenRouterService $openRouter = new OpenRouterService()
    ) {}

    /**
     * Jalankan auto-answer untuk sebuah lamaran.
     *
     * @return array{
     *   answers: array,              // parsed AI answers per normalized question (aligned by index)
     *   normalized: array,           // normalized questionnaire
     *   profile: array,              // profile yang dipakai
     *   match_score: int,            // 0-100
     *   per_question: array,         // rincian per question
     *   history_id: int|null,        // id row application_ai_answers
     *   model: string,
     *   duration_ms: int,
     * }
     */
    public function run(
        User $user,
        string $provider,
        array $platformProfile,
        array $questionnaire,
        array $jobMeta = []
    ): array {
        $normalized = QuestionNormalizer::normalize($provider, $questionnaire);
        $profile = ProfileBuilder::build($provider, $platformProfile, $user);
        $config = $this->openRouter->resolveConfig($user);

        if (empty($config['api_key'])) {
            $history = $this->logFailure(
                $user, $provider, $jobMeta, $normalized, $profile,
                'API key OpenRouter belum diatur. Silakan isi di Settings → AI Provider.',
                $config['model']
            );
            throw new RuntimeException('API key OpenRouter belum diatur. Silakan isi di Settings → AI Provider.');
        }

        $systemPrompt = $this->buildSystemPrompt();
        $userPrompt = $this->buildUserPrompt($profile, $normalized);

        $start = microtime(true);
        try {
            $response = Http::withToken($config['api_key'])
                ->acceptJson()
                ->timeout(60)
                ->post('https://openrouter.ai/api/v1/chat/completions', [
                    'model' => $config['model'],
                    'temperature' => $config['temperature'],
                    'max_tokens' => max(1200, (int) $config['max_tokens']),
                    'response_format' => ['type' => 'json_object'],
                    'messages' => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user', 'content' => $userPrompt],
                    ],
                ]);
        } catch (\Throwable $e) {
            $duration = (int) round((microtime(true) - $start) * 1000);
            $this->logFailure(
                $user, $provider, $jobMeta, $normalized, $profile,
                'Gagal menghubungi OpenRouter: ' . $e->getMessage(),
                $config['model'], $duration
            );
            throw new RuntimeException('Gagal menghubungi OpenRouter: ' . $e->getMessage());
        }

        $duration = (int) round((microtime(true) - $start) * 1000);

        if ($response->failed()) {
            $errorMsg = data_get($response->json(), 'error.message') ?: ('HTTP ' . $response->status());
            $this->logFailure(
                $user, $provider, $jobMeta, $normalized, $profile,
                "OpenRouter error: {$errorMsg}",
                $config['model'], $duration
            );
            throw new RuntimeException("OpenRouter error: {$errorMsg}");
        }

        $body = $response->json();
        $content = data_get($body, 'choices.0.message.content');
        $usage = data_get($body, 'usage', []);

        $parsed = $this->parseJson($content);
        if (!$parsed || !isset($parsed['answers']) || !is_array($parsed['answers'])) {
            $this->logFailure(
                $user, $provider, $jobMeta, $normalized, $profile,
                'Respons AI tidak dapat di-parse sebagai JSON valid.',
                $config['model'], $duration, ['raw' => $content, 'usage' => $usage]
            );
            throw new RuntimeException('Respons AI tidak dapat di-parse sebagai JSON valid.');
        }

        // Hitung breakdown
        $perQuestion = $this->buildPerQuestion($normalized, $parsed['answers']);
        [$matchScore, $unanswered] = $this->computeMatchScore($perQuestion);

        // Simpan history
        $history = ApplicationAiAnswer::create([
            'user_id' => $user->id,
            'application_id' => $jobMeta['application_id'] ?? null,
            'job_id' => (string) ($jobMeta['job_id'] ?? ''),
            'job_title' => $jobMeta['job_title'] ?? null,
            'provider' => $provider,
            'model' => $config['model'],
            'questionnaire' => $normalized,
            'profile' => $profile,
            'prompt' => ['system' => $systemPrompt, 'user' => $userPrompt],
            'raw_response' => ['content' => $content, 'usage' => $usage, 'parsed' => $parsed],
            'final_answers' => $parsed['answers'],
            'per_question' => $perQuestion,
            'match_score' => $matchScore,
            'unanswered_count' => $unanswered,
            'total_questions' => count($perQuestion),
            'tokens_prompt' => (int) data_get($usage, 'prompt_tokens', 0) ?: null,
            'tokens_completion' => (int) data_get($usage, 'completion_tokens', 0) ?: null,
            'tokens_total' => (int) data_get($usage, 'total_tokens', 0) ?: null,
            'duration_ms' => $duration,
            'status' => 'success',
        ]);

        return [
            'answers' => $parsed['answers'],
            'normalized' => $normalized,
            'profile' => $profile,
            'match_score' => $matchScore,
            'unanswered_count' => $unanswered,
            'per_question' => $perQuestion,
            'history_id' => $history->id,
            'model' => $config['model'],
            'duration_ms' => $duration,
        ];
    }

    protected function buildSystemPrompt(): string
    {
        return <<<PROMPT
Kamu adalah Compass AI — asisten yang mengisi formulir screening kerja secara jujur.

ATURAN KETAT:
- Hanya gunakan data dari objek "profile". Jangan mengarang, menambah, atau melebih-lebihkan fakta.
- Jika informasi TIDAK tersedia di profile, isi value dengan null dan tulis alasan pada "missing_info".
- Untuk radio/radio_group/checkbox/checkbox_group: value HARUS berasal dari opsi yang tersedia (field "value" pada options). Jangan mengarang value.
- Untuk pertanyaan teks bertipe ya/tidak, jawab "Ya" atau "Tidak" (jangan true/false/1/0).
- Untuk pertanyaan angka gaji, gunakan format "Rp<angka>" (mis. "Rp5.000.000") dari profile.
- Setiap jawaban WAJIB disertai "confidence" 0-100 (0=tebakan buta, 100=fakta eksplisit di profile).
- Output HARUS JSON valid TANPA markdown/penjelasan.

FORMAT OUTPUT (WAJIB, urutan answers sama persis dengan urutan question):
{
  "answers": [
    // untuk type "text":
    { "type": "text", "answer": { "text": "..." | null }, "confidence": 0-100, "missing_info": "..." | null },
    // untuk type "radio":
    { "type": "radio", "answer": { "text": "...", "value": "..." } | null, "confidence": 0-100, "missing_info": "..." | null },
    // untuk type "radio_group":
    { "type": "radio_group", "answer": [ { "id": "...", "text": "...", "value": "..." | null } ], "confidence": 0-100, "missing_info": "..." | null },
    // untuk type "checkbox":
    { "type": "checkbox", "answer": { "options": [ { "label": "...", "value": "..." } ] } | null, "confidence": 0-100, "missing_info": "..." | null },
    // untuk type "checkbox_group":
    { "type": "checkbox_group", "answer": [ { "id": "...", "options": [ { "label": "...", "value": "..." } ] } ], "confidence": 0-100, "missing_info": "..." | null }
  ]
}
PROMPT;
    }

    protected function buildUserPrompt(array $profile, array $normalized): string
    {
        return json_encode([
            'profile' => $profile,
            'question' => $normalized,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    protected function parseJson(?string $content): ?array
    {
        if (!is_string($content) || trim($content) === '') return null;
        // Bersihkan markdown code fence jika model bandel
        $clean = preg_replace('/^```(?:json)?\s*|\s*```$/im', '', trim($content));
        $data = json_decode($clean, true);
        return is_array($data) ? $data : null;
    }

    /**
     * Bangun per-question breakdown untuk transparansi.
     */
    protected function buildPerQuestion(array $normalized, array $aiAnswers): array
    {
        $out = [];
        foreach ($normalized as $i => $q) {
            $ans = $aiAnswers[$i] ?? null;
            $answerText = $this->summarizeAnswer($ans);
            $confidence = is_array($ans) ? (int) ($ans['confidence'] ?? 0) : 0;
            $confidence = max(0, min(100, $confidence));
            $missing = is_array($ans) ? ($ans['missing_info'] ?? null) : null;

            $out[] = [
                'index' => $i,
                'name' => $q['name'] ?? null,
                'question' => $q['question'] ?? null,
                'type' => $q['type'] ?? null,
                'answer_summary' => $answerText,
                'confidence' => $confidence,
                'is_answered' => $answerText !== null && $answerText !== '',
                'missing_info' => $missing,
            ];
        }
        return $out;
    }

    protected function summarizeAnswer($ans): ?string
    {
        if (!is_array($ans)) return null;
        $type = $ans['type'] ?? null;
        $a = $ans['answer'] ?? null;
        if ($a === null) return null;

        return match ($type) {
            'text' => is_array($a) ? (isset($a['text']) && $a['text'] !== null ? (string) $a['text'] : null) : null,
            'radio' => is_array($a) ? ($a['text'] ?? null) : null,
            'radio_group' => is_array($a)
                ? implode(', ', array_map(
                    fn ($x) => is_array($x)
                        ? ((($x['text'] ?? '?') . ': ' . ($x['value'] ?? '—')))
                        : '?',
                    $a
                ))
                : null,
            'checkbox' => is_array($a) && isset($a['options']) && is_array($a['options'])
                ? implode(', ', array_map(fn ($o) => $o['label'] ?? ($o['value'] ?? '?'), $a['options']))
                : null,
            'checkbox_group' => is_array($a)
                ? implode(' | ', array_map(function ($g) {
                    $opts = $g['options'] ?? [];
                    return ($g['id'] ?? '?') . ':' . implode(',', array_map(fn ($o) => $o['label'] ?? '?', $opts));
                }, $a))
                : null,
            default => null,
        };
    }

    /**
     * Match score = rata-rata confidence semua pertanyaan.
     * Pertanyaan yang tidak terjawab (null) dihitung sebagai 0.
     *
     * @return array{0:int,1:int} [matchScore, unansweredCount]
     */
    protected function computeMatchScore(array $perQuestion): array
    {
        if (empty($perQuestion)) return [0, 0];
        $total = count($perQuestion);
        $sum = 0;
        $unanswered = 0;
        foreach ($perQuestion as $pq) {
            if (!$pq['is_answered']) {
                $unanswered++;
                continue; // 0
            }
            $sum += $pq['confidence'];
        }
        $avg = (int) round($sum / $total);
        return [max(0, min(100, $avg)), $unanswered];
    }

    protected function logFailure(
        User $user, string $provider, array $jobMeta,
        array $normalized, array $profile,
        string $error, string $model,
        int $duration = 0, array $rawResponse = []
    ): ?ApplicationAiAnswer {
        try {
            return ApplicationAiAnswer::create([
                'user_id' => $user->id,
                'application_id' => $jobMeta['application_id'] ?? null,
                'job_id' => (string) ($jobMeta['job_id'] ?? ''),
                'job_title' => $jobMeta['job_title'] ?? null,
                'provider' => $provider,
                'model' => $model,
                'questionnaire' => $normalized,
                'profile' => $profile,
                'raw_response' => $rawResponse ?: null,
                'total_questions' => count($normalized),
                'duration_ms' => $duration ?: null,
                'status' => 'failed',
                'error_message' => $error,
            ]);
        } catch (\Throwable $e) {
            Log::error('Gagal simpan history AI: ' . $e->getMessage());
            return null;
        }
    }
}
