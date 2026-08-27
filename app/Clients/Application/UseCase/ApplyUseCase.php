<?php

namespace App\Clients\Application\UseCase;

use App\Events\JobStatus;
use App\Infrastructure\Contracts\Platform\PlatformAccount;
use App\Infrastructure\Contracts\Platform\PlatformAdapter;
use App\Services\AI\AutoAnswerService;
use App\Support\ProviderHelper;
use Illuminate\Support\Facades\Log;

class ApplyUseCase {

    public int $user_id;
    public function __construct(
        private PlatformAdapter $adapter,
        private PlatformAccount $account
    ) {
        $this->user_id = $this->account->user->id;
    }

    public function apply(string $jobId): ?array {

        $job = $this->adapter->loadJob($jobId);
        JobStatus::dispatch($this->user_id, $job, ProviderHelper::who($this->account), 'load_job');

        $provider = ProviderHelper::who($this->account);
        $user = $this->account->user;
        $autoAnswerEnabled = (bool) data_get($user->apply_configuration, 'auto_answer.enabled', false);

        $result = [
            'success' => null,
            'status' => 'unknown',
            'provider' => $provider,
            'issues' => [],
            'job' => [
                'job_id' => $jobId,
                'job_title' => $job['metadata']['title'],
                'job_company' => $job['metadata']['company']
            ],
            'ai_answer' => null,
        ];

        JobStatus::dispatch($this->user_id, $job, $provider, 'load_profile');
        $profile = $this->adapter->loadProfile();


        $traceInfo = $this->adapter->generateTraceInfo();
        if(!empty($traceInfo)) {
            $this->account->saveConfig('traceInfo' , $traceInfo);
        }
        JobStatus::dispatch($this->user_id, $job, $provider, 'load_userConfig');
        $config = $this->account->getConfig();

        JobStatus::dispatch($this->user_id, $job, $provider, 'inspect');
        $inspector = $this->adapter->canApply($job);

        // Jika auto-answer aktif, questionnaire tidak dianggap hard block.
        if ($autoAnswerEnabled) {
            $inspector['issues'] = array_values(array_filter(
                $inspector['issues'] ?? [],
                fn ($issue) => ($issue['type'] ?? null) !== 'questionnaire'
            ));
            $inspector['canApply'] = empty(array_filter(
                $inspector['issues'],
                fn ($issue) => ($issue['level'] ?? '') === 'hard'
            ));
        }

        if(!$inspector['canApply']){
            Log::warning("Tidak dapat melamar pekerjaan ID: " . $jobId . " karena tidak memenuhi syarat.");
            Log::warning(json_encode($inspector['issues']));
            $result['status'] = $inspector['issues'][0]['type'] ?? 'unknown';
            $result['success'] = false;
            $result['issues'] = $inspector['issues'];
            return $result;
        }

        // Jalankan auto-answer bila ada questionnaire + fitur aktif
        $aiAnswerData = null;
        $questionnaire = $job['products']['questionnaire'] ?? [];
        if ($autoAnswerEnabled && !empty($questionnaire) && $provider === 'glints') {
            JobStatus::dispatch($this->user_id, $job, $provider, 'auto_answer');
            try {
                $aiAnswerData = app(AutoAnswerService::class)->run(
                    $user,
                    $provider,
                    $profile,
                    $questionnaire,
                    [
                        'job_id' => $jobId,
                        'job_title' => $job['metadata']['title'] ?? null,
                    ]
                );
                $result['ai_answer'] = [
                    'history_id' => $aiAnswerData['history_id'] ?? null,
                    'match_score' => $aiAnswerData['match_score'] ?? 0,
                    'unanswered' => $aiAnswerData['unanswered_count'] ?? 0,
                    'model' => $aiAnswerData['model'] ?? null,
                ];
            } catch (\Throwable $e) {
                Log::error('Auto-answer gagal: ' . $e->getMessage());
                $result['status'] = 'auto_answer_failed';
                $result['success'] = false;
                $result['issues'] = [[
                    'type' => 'auto_answer_failed',
                    'level' => 'hard',
                    'message' => 'Auto-answer AI gagal: ' . $e->getMessage(),
                ]];
                return $result;
            }
        }

        $data = [
            'job' => $job,
            'profile' => $profile,
            'config' => $config,
            'ai_answer' => $aiAnswerData,
        ];


        if(!$data['job'] || !$data['profile'] || !$data['config']){
            Log::warning('Ada data yang kosong : '. json_encode($data));
        }

        JobStatus::dispatch($this->user_id, $job, $provider, 'build_payload');
        $payload = $this->adapter->buildPayload($data);


        JobStatus::dispatch($this->user_id, $job, $provider, 'apply');

        $success = $this->adapter->execute($jobId, $payload, $config);
        if($success)
        {
            $result['status'] = 'success';
            $result['success'] = true;
        } else{
            $result['success'] = false;
        }
        return $result;
    }
}
