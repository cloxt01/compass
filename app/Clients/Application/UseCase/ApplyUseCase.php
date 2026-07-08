<?php

namespace App\Clients\Application\UseCase;

use App\Events\JobStatus;
use App\Infrastructure\Contracts\PlatformAccount;
use App\Infrastructure\Contracts\PlatformAdapter;
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

        $result = [
            'success' => null,
            'status' => 'unknown',
            'provider' => ProviderHelper::who($this->account),
            'issues' => [],
            'job' => [
                'job_id' => $jobId,
                'job_title' => $job['metadata']['title'],
                'job_company' => $job['metadata']['company']
            ]
        ];

        JobStatus::dispatch($this->user_id, $job, ProviderHelper::who($this->account), 'load_profile');
        $profile = $this->adapter->loadProfile();


        $traceInfo = $this->adapter->generateTraceInfo();
        if(!empty($traceInfo)) {
            $this->account->saveConfig('traceInfo' , $traceInfo);
        }
        JobStatus::dispatch($this->user_id, $job, ProviderHelper::who($this->account), 'load_userConfig');
        $config = $this->account->getConfig();

        JobStatus::dispatch($this->user_id, $job, ProviderHelper::who($this->account), 'inspect');
        $inspector = $this->adapter->canApply($job);

        if(!$inspector['canApply']){
            Log::warning("Tidak dapat melamar pekerjaan ID: " . $jobId . " karena tidak memenuhi syarat.");
            Log::warning(json_encode($inspector['issues']));
            $result['status'] = $inspector['issues'][0]['type'] ?? 'unknown';
            $result['success'] = false;
            $result['issues'] = $inspector['issues'];
            return $result;
        }
        $data = [
            'job' => $job,
            'profile' => $profile,
            'config' => $config
        ];


        if(!$data['job'] || !$data['profile'] || !$data['config']){
            Log::warning('Ada data yang kosong : '. json_encode($data));
        }

        JobStatus::dispatch($this->user_id, $job, ProviderHelper::who($this->account), 'build_payload');
        $payload = $this->adapter->buildPayload($data);


        JobStatus::dispatch($this->user_id, $job, ProviderHelper::who($this->account), 'apply');

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
