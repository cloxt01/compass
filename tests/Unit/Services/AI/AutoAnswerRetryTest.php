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
 * Verify the retry + reasoning-off behavior of AutoAnswerService,
 * addressing the reported timeout bug (cURL error 28).
 */
class AutoAnswerRetryTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed packages required by UserObserver on User creation
        $this->seed(\Database\Seeders\PackageSeeder::class);

        $this->user = User::factory()->create([
            'apply_configuration' => [
                'career_ai' => [
                    'api_key' => 'sk-or-test-fake-key',
                    'model' => 'deepseek/deepseek-v4-flash',
                    'temperature' => 0.35,
                    'max_tokens' => 800,
                ],
                'auto_answer' => ['enabled' => true],
            ],
        ]);
    }

    /**
     * The Glints questionnaire fixture used across tests (mirrors main2.php format).
     */
    protected function questionnaireFixture(): array
    {
        return [
            [
                'name' => 'NOTICE_PERIOD',
                'label' => 'When can you start working?',
                'type' => 'SINGLE_CHOICE_WITHOUT_SUBQUESTIONS',
                'sub_questions' => [
                    [
                        'id' => null,
                        'sub_label' => null,
                        'options' => [
                            ['value' => 'Immediately', 'mapped_value' => 'IMMEDIATELY'],
                            ['value' => '1 month', 'mapped_value' => 'ONE_MONTH'],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'CUSTOM_PLAIN_TEXT',
                'label' => 'Berapakah gaji yang Anda harapkan?',
                'type' => 'CUSTOM_PLAIN_TEXT',
                'sub_questions' => [
                    ['id' => 'salary-uuid', 'sub_label' => null, 'options' => []],
                ],
            ],
        ];
    }

    /**
     * Response body yang valid dari OpenRouter (structure disamakan dgn contoh nyata).
     */
    protected function successResponseBody(): array
    {
        return [
            'id' => 'gen-test',
            'model' => 'deepseek/deepseek-v4-flash',
            'choices' => [[
                'message' => [
                    'role' => 'assistant',
                    'content' => json_encode([
                        'answers' => [
                            [
                                'type' => 'radio',
                                'answer' => ['text' => 'Immediately', 'value' => 'IMMEDIATELY'],
                                'confidence' => 95,
                                'missing_info' => null,
                            ],
                            [
                                'type' => 'text',
                                'answer' => ['text' => 'Rp5.000.000'],
                                'confidence' => 90,
                                'missing_info' => null,
                            ],
                        ],
                    ]),
                ],
            ]],
            'usage' => [
                'prompt_tokens' => 100,
                'completion_tokens' => 50,
                'total_tokens' => 150,
            ],
        ];
    }

    /** @test */
    public function retries_when_first_attempt_times_out_then_succeeds()
    {
        // First attempt: throw ConnectionException (simulate cURL timeout)
        // Second attempt: succeed
        $callCount = 0;
        Http::fake(function ($request) use (&$callCount) {
            $callCount++;
            if ($callCount === 1) {
                throw new ConnectionException(
                    'cURL error 28: Operation timed out after 60005 milliseconds'
                );
            }
            return Http::response($this->successResponseBody(), 200);
        });

        $service = new AutoAnswerService();
        $result = $service->run(
            $this->user,
            'glints',
            [], // empty platform profile
            $this->questionnaireFixture(),
            ['job_id' => 'job-123', 'job_title' => 'Test Job']
        );

        $this->assertSame(2, $callCount, 'Should retry exactly once after ConnectionException');
        $this->assertNotNull($result['history_id']);
        $this->assertGreaterThan(0, $result['match_score']);
        $this->assertCount(2, $result['per_question']);

        $history = ApplicationAiAnswer::find($result['history_id']);
        $this->assertSame('success', $history->status);
    }

    /** @test */
    public function fails_gracefully_when_both_attempts_timeout()
    {
        Http::fake(function () {
            throw new ConnectionException(
                'cURL error 28: Operation timed out after 60005 milliseconds'
            );
        });

        $service = new AutoAnswerService();

        try {
            $service->run(
                $this->user,
                'glints',
                [],
                $this->questionnaireFixture(),
                ['job_id' => 'job-456', 'job_title' => 'Fail Job']
            );
            $this->fail('Expected RuntimeException was not thrown');
        } catch (\RuntimeException $e) {
            $this->assertStringContainsString('OpenRouter', $e->getMessage());
        }

        // Harus tetap simpan history dengan status failed
        $history = ApplicationAiAnswer::where('job_id', 'job-456')->first();
        $this->assertNotNull($history, 'Failed attempt should still be logged to history');
        $this->assertSame('failed', $history->status);
        $this->assertStringContainsString('OpenRouter', $history->error_message);
    }

    /** @test */
    public function first_attempt_sends_reasoning_disabled_for_speed()
    {
        Http::fake(fn () => Http::response($this->successResponseBody(), 200));

        $service = new AutoAnswerService();
        $service->run(
            $this->user,
            'glints',
            [],
            $this->questionnaireFixture(),
            ['job_id' => 'job-fast']
        );

        Http::assertSent(function ($request) {
            $body = json_decode($request->body(), true);
            // First attempt should explicitly disable reasoning to prevent timeout
            return isset($body['reasoning']['enabled'])
                && $body['reasoning']['enabled'] === false
                && isset($body['provider']['reasoning']['enabled'])
                && $body['provider']['reasoning']['enabled'] === false;
        });
    }

    /** @test */
    public function retries_on_http_500_and_second_attempt_uses_reasoning_true()
    {
        $sentPayloads = [];
        $callCount = 0;
        Http::fake(function ($request) use (&$callCount, &$sentPayloads) {
            $callCount++;
            $sentPayloads[] = json_decode($request->body(), true);
            if ($callCount === 1) {
                return Http::response(['error' => ['message' => 'Upstream error']], 500);
            }
            return Http::response($this->successResponseBody(), 200);
        });

        $service = new AutoAnswerService();
        $result = $service->run(
            $this->user,
            'glints',
            [],
            $this->questionnaireFixture(),
            ['job_id' => 'job-500']
        );

        $this->assertSame(2, $callCount);
        $this->assertNotNull($result['history_id']);

        // Attempt #1: reasoning disabled
        $this->assertFalse($sentPayloads[0]['reasoning']['enabled']);
        // Attempt #2: reasoning enabled (fallback with more time)
        $this->assertTrue($sentPayloads[1]['reasoning']['enabled']);
    }

    /** @test */
    public function does_not_retry_on_4xx_client_error()
    {
        $callCount = 0;
        Http::fake(function () use (&$callCount) {
            $callCount++;
            return Http::response(['error' => ['message' => 'Invalid API key']], 401);
        });

        $service = new AutoAnswerService();
        try {
            $service->run($this->user, 'glints', [], $this->questionnaireFixture(), ['job_id' => 'job-401']);
            $this->fail('Expected RuntimeException');
        } catch (\RuntimeException $e) {
            $this->assertStringContainsString('Invalid API key', $e->getMessage());
        }

        $this->assertSame(1, $callCount, '4xx should NOT retry');
    }
}
