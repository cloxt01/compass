<?php

namespace Tests\Unit\Services\AI;

use App\Models\ApplicationAiAnswer;
use App\Models\User;
use App\Services\AI\AutoAnswerService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Extended coverage for AutoAnswerService: retry payload details, error/failure logging,
 * match score computation, and manual-profile propagation into the prompt.
 */
class AutoAnswerServiceExtendedTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\PackageSeeder::class);

        $this->user = User::factory()->create([
            'apply_configuration' => [
                'career_ai' => [
                    'api_key' => 'sk-or-test-fake-key',
                    'model' => 'deepseek/deepseek-v4-flash',
                    'temperature' => 0.35,
                    'max_tokens' => 800,
                ],
                'auto_answer' => [
                    'enabled' => true,
                    'profile' => [
                        'nama' => 'TEST_Manual Candidate',
                        'ekspektasi_gaji' => 6500000,
                        'notice_period' => 'IMMEDIATELY',
                    ],
                ],
            ],
        ]);
    }

    protected function questionnaireFixture(): array
    {
        return [
            [
                'name' => 'NOTICE_PERIOD',
                'label' => 'When can you start working?',
                'type' => 'SINGLE_CHOICE_WITHOUT_SUBQUESTIONS',
                'sub_questions' => [[
                    'id' => null,
                    'sub_label' => null,
                    'options' => [
                        ['value' => 'Immediately', 'mapped_value' => 'IMMEDIATELY'],
                        ['value' => '1 month', 'mapped_value' => 'ONE_MONTH'],
                    ],
                ]],
            ],
            [
                'name' => 'CUSTOM_PLAIN_TEXT',
                'label' => 'Berapakah gaji yang Anda harapkan?',
                'type' => 'CUSTOM_PLAIN_TEXT',
                'sub_questions' => [['id' => 'salary-uuid', 'sub_label' => null, 'options' => []]],
            ],
        ];
    }

    protected function bodyWithAnswers(array $answers, array $usage = null): array
    {
        return [
            'id' => 'gen-test',
            'model' => 'deepseek/deepseek-v4-flash',
            'choices' => [['message' => ['role' => 'assistant', 'content' => json_encode(['answers' => $answers])]]],
            'usage' => $usage ?? ['prompt_tokens' => 100, 'completion_tokens' => 50, 'total_tokens' => 150],
        ];
    }

    protected function successAnswers(): array
    {
        return [
            ['type' => 'radio', 'answer' => ['text' => 'Immediately', 'value' => 'IMMEDIATELY'], 'confidence' => 100, 'missing_info' => null],
            ['type' => 'text', 'answer' => ['text' => 'Rp6.500.000'], 'confidence' => 90, 'missing_info' => null],
        ];
    }

    protected function runService(array $jobMeta = []): array
    {
        return (new AutoAnswerService())->run(
            $this->user, 'glints', [], $this->questionnaireFixture(),
            $jobMeta + ['job_id' => 'job-ext', 'job_title' => 'TEST Job']
        );
    }

    // ------------------------------------------------------------- SUCCESS PATH

    public function test_success_path_persists_full_history_row()
    {
        Http::fake(fn () => Http::response($this->bodyWithAnswers($this->successAnswers()), 200));

        $result = $this->runService(['job_id' => 'job-success']);

        $this->assertSame(95, $result['match_score']);
        $this->assertSame(0, $result['unanswered_count']);
        $this->assertSame('deepseek/deepseek-v4-flash', $result['model']);
        $this->assertGreaterThanOrEqual(0, $result['duration_ms']);

        $history = ApplicationAiAnswer::find($result['history_id']);
        $this->assertNotNull($history);
        $this->assertSame('success', $history->status);
        $this->assertSame('glints', $history->provider);
        $this->assertSame(2, $history->total_questions);
        $this->assertSame(95, $history->match_score);
        $this->assertSame(0, $history->unanswered_count);
        $this->assertSame(100, $history->tokens_prompt);
        $this->assertSame(150, $history->tokens_total);
        $this->assertCount(2, $history->per_question);
        $this->assertTrue($history->per_question[0]['is_answered']);
        $this->assertSame('Immediately', $history->per_question[0]['answer_summary']);
        $this->assertArrayHasKey('system', $history->prompt);
        $this->assertNull($history->error_message);
    }

    public function test_manual_profile_is_included_in_prompt_sent_to_openrouter()
    {
        Http::fake(fn () => Http::response($this->bodyWithAnswers($this->successAnswers()), 200));

        $result = $this->runService();

        $this->assertSame('TEST_Manual Candidate', $result['profile']['nama']);
        $this->assertSame(6500000, $result['profile']['ekspektasi_gaji']);

        Http::assertSent(function ($request) {
            $body = json_decode($request->body(), true);
            $userMsg = $body['messages'][1]['content'] ?? '';
            return str_contains($userMsg, 'TEST_Manual Candidate')
                && str_contains($userMsg, '6500000')
                && $body['response_format']['type'] === 'json_object'
                && $body['model'] === 'deepseek/deepseek-v4-flash';
        });
    }

    public function test_first_attempt_uses_bumped_max_tokens_floor()
    {
        Http::fake(fn () => Http::response($this->bodyWithAnswers($this->successAnswers()), 200));

        $this->runService();

        Http::assertSent(function ($request) {
            $body = json_decode($request->body(), true);
            return $body['max_tokens'] === 1200;
        });
    }

    // ------------------------------------------------------------- RETRY BEHAVIOR

    public function test_second_attempt_uses_larger_max_tokens_and_low_effort_reasoning()
    {
        $payloads = [];
        $calls = 0;
        Http::fake(function ($request) use (&$calls, &$payloads) {
            $calls++;
            $payloads[] = json_decode($request->body(), true);
            return $calls === 1
                ? Http::response(['error' => ['message' => 'upstream']], 503)
                : Http::response($this->bodyWithAnswers($this->successAnswers()), 200);
        });

        $this->runService(['job_id' => 'job-503']);

        $this->assertSame(2, $calls);
        $this->assertSame(1200, $payloads[0]['max_tokens']);
        $this->assertSame(1500, $payloads[1]['max_tokens']);
        $this->assertTrue($payloads[1]['reasoning']['enabled']);
        $this->assertSame('low', $payloads[1]['reasoning']['effort']);
        $this->assertArrayNotHasKey('provider', $payloads[1]);
    }

    public function test_retries_on_408_request_timeout()
    {
        $calls = 0;
        Http::fake(function () use (&$calls) {
            $calls++;
            return $calls === 1
                ? Http::response(['error' => ['message' => 'timeout']], 408)
                : Http::response($this->bodyWithAnswers($this->successAnswers()), 200);
        });

        $result = $this->runService(['job_id' => 'job-408']);

        $this->assertSame(2, $calls);
        $this->assertNotNull($result['history_id']);
    }

    public function test_does_not_retry_on_422_client_error()
    {
        $calls = 0;
        Http::fake(function () use (&$calls) {
            $calls++;
            return Http::response(['error' => ['message' => 'bad request']], 422);
        });
        try {
            $this->runService(['job_id' => 'job-422']);
            $this->fail('Expected RuntimeException');
        } catch (\RuntimeException $e) {
            $this->assertStringContainsString('bad request', $e->getMessage());
        }
        $this->assertSame(1, $calls);
    }

    public function test_stops_after_two_attempts_on_repeated_500()
    {
        $calls500 = 0;
        Http::fake(function () use (&$calls500) {
            $calls500++;
            return Http::response(['error' => ['message' => 'upstream down']], 500);
        });
        try {
            $this->runService(['job_id' => 'job-500-500']);
            $this->fail('Expected RuntimeException');
        } catch (\RuntimeException $e) {
            $this->assertStringContainsString('upstream down', $e->getMessage());
        }
        $this->assertSame(2, $calls500, 'Should attempt exactly twice on repeated 5xx');

        $history = ApplicationAiAnswer::where('job_id', 'job-500-500')->first();
        $this->assertNotNull($history);
        $this->assertSame('failed', $history->status);
    }

    public function test_connection_exception_then_500_logs_failure_with_http_error()
    {
        $calls = 0;
        Http::fake(function () use (&$calls) {
            $calls++;
            if ($calls === 1) {
                throw new ConnectionException('cURL error 28: Operation timed out after 60005 milliseconds');
            }
            return Http::response(['error' => ['message' => 'still broken']], 500);
        });

        try {
            $this->runService(['job_id' => 'job-mixed']);
            $this->fail('Expected RuntimeException');
        } catch (\RuntimeException $e) {
            $this->assertStringContainsString('OpenRouter', $e->getMessage());
        }

        $this->assertSame(2, $calls);
        $history = ApplicationAiAnswer::where('job_id', 'job-mixed')->first();
        $this->assertNotNull($history);
        $this->assertSame('failed', $history->status);
        $this->assertNotNull($history->error_message);
    }

    // ------------------------------------------------------------- FAILURE LOGGING

    public function test_missing_api_key_logs_failure_and_throws_without_http_call()
    {
        Http::fake();
        $this->user->update(['apply_configuration' => ['career_ai' => []]]);

        try {
            $this->runService(['job_id' => 'job-nokey']);
            $this->fail('Expected RuntimeException');
        } catch (\RuntimeException $e) {
            $this->assertStringContainsString('API key OpenRouter belum diatur', $e->getMessage());
        }

        Http::assertNothingSent();
        $history = ApplicationAiAnswer::where('job_id', 'job-nokey')->first();
        $this->assertNotNull($history);
        $this->assertSame('failed', $history->status);
        $this->assertSame(2, $history->total_questions);
    }

    public function test_unparseable_ai_content_logs_failure_with_raw_response()
    {
        Http::fake(fn () => Http::response([
            'choices' => [['message' => ['content' => 'sorry, I cannot answer']]],
            'usage' => ['total_tokens' => 10],
        ], 200));

        try {
            $this->runService(['job_id' => 'job-badjson']);
            $this->fail('Expected RuntimeException');
        } catch (\RuntimeException $e) {
            $this->assertStringContainsString('JSON', $e->getMessage());
        }

        $history = ApplicationAiAnswer::where('job_id', 'job-badjson')->first();
        $this->assertNotNull($history);
        $this->assertSame('failed', $history->status);
        $this->assertSame('sorry, I cannot answer', data_get($history->raw_response, 'raw'));
    }

    public function test_markdown_fenced_json_is_parsed_successfully()
    {
        $json = json_encode(['answers' => $this->successAnswers()]);
        Http::fake(fn () => Http::response([
            'choices' => [['message' => ['content' => "```json\n{$json}\n```"]]],
            'usage' => [],
        ], 200));

        $result = $this->runService(['job_id' => 'job-fenced']);

        $this->assertSame(95, $result['match_score']);
        $this->assertNotNull($result['history_id']);
    }

    // ------------------------------------------------------------- SCORING

    public function test_unanswered_questions_lower_match_score_and_are_counted()
    {
        Http::fake(fn () => Http::response($this->bodyWithAnswers([
            ['type' => 'radio', 'answer' => ['text' => 'Immediately', 'value' => 'IMMEDIATELY'], 'confidence' => 80, 'missing_info' => null],
            ['type' => 'text', 'answer' => null, 'confidence' => 0, 'missing_info' => 'Gaji tidak tersedia di profil'],
        ]), 200));

        $result = $this->runService(['job_id' => 'job-partial']);

        $this->assertSame(1, $result['unanswered_count']);
        $this->assertSame(40, $result['match_score']);
        $this->assertFalse($result['per_question'][1]['is_answered']);
        $this->assertSame('Gaji tidak tersedia di profil', $result['per_question'][1]['missing_info']);
    }

    public function test_confidence_is_clamped_and_missing_answers_default_to_zero()
    {
        Http::fake(fn () => Http::response($this->bodyWithAnswers([
            ['type' => 'radio', 'answer' => ['text' => 'Immediately', 'value' => 'IMMEDIATELY'], 'confidence' => 500],
        ]), 200));

        $result = $this->runService(['job_id' => 'job-clamp']);

        $this->assertSame(100, $result['per_question'][0]['confidence']);
        $this->assertSame(0, $result['per_question'][1]['confidence']);
        $this->assertFalse($result['per_question'][1]['is_answered']);
        $this->assertSame(50, $result['match_score']);
    }
}
