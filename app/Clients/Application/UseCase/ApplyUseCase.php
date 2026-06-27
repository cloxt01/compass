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

    public function apply(string $jobId): bool {
        JobStatus::dispatch($this->user_id, $jobId, ProviderHelper::who($this->account), 'load_job');
        $job = $this->adapter->loadJob($jobId);

        JobStatus::dispatch($this->user_id, $jobId, ProviderHelper::who($this->account), 'load_profile');
        $profile = $this->adapter->loadProfile();


        $traceInfo = $this->adapter->generateTraceInfo();
        if(!empty($traceInfo)) {
            $this->account->saveConfig('traceInfo' , $traceInfo);
        }
        JobStatus::dispatch($this->user_id, $jobId, ProviderHelper::who($this->account), 'load_userConfig');
        $config = $this->account->getConfig();

        JobStatus::dispatch($this->user_id, $jobId, ProviderHelper::who($this->account), 'inspect');
        $inspector = $this->adapter->canApply($job);

        if(!$inspector['canApply']){
            Log::warning("Tidak dapat melamar pekerjaan ID: " . $jobId . " karena tidak memenuhi syarat.");
            Log::warning(json_encode($inspector['issues']));
            $this->adapter->db()->upsert_job($this->account->user->id, $jobId, $inspector['issues'][0]['type']);
            return false;
        }
        JobStatus::dispatch($this->user_id, $jobId, ProviderHelper::who($this->account), 'build_payload');
        $payload = $this->adapter->buildPayload($job, $profile, $config);

        JobStatus::dispatch($this->user_id, $jobId, ProviderHelper::who($this->account), 'apply');

        return $this->adapter->execute($jobId, $payload, $config);
    }
}
