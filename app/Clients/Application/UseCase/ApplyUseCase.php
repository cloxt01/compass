<?php

namespace App\Clients\Application\UseCase;

use App\Infrastructure\Contracts\PlatformAccount;
use App\Infrastructure\Contracts\PlatformAdapter;
use Illuminate\Support\Facades\Log;

class ApplyUseCase {

    public function __construct(
        private PlatformAdapter $adapter,
        private PlatformAccount $account
    ) {}

    public function apply(string $jobId): bool {
        $job = $this->adapter->loadJob($jobId);
        $profile = $this->adapter->loadProfile();

        $traceInfo = $this->adapter->generateTraceInfo();
        if(!empty($traceInfo)) {
            $this->account->saveConfig('traceInfo' , $traceInfo);
        }
        $config = $this->account->getConfig();

        $inspector = $this->adapter->canApply($job);
        if(!$inspector['canApply']){
            Log::warning("Tidak dapat melamar pekerjaan ID: " . $jobId . " karena tidak memenuhi syarat.");
            Log::warning(json_encode($inspector['issues']));
            $this->adapter->db()->upsert_job($this->account->user->id, $jobId, $inspector['issues'][0]['type']);
            return false;
        }
        $payload = $this->adapter->buildPayload($job, $profile, $config);
        return $this->adapter->execute($jobId, $payload, $config);
    }
}
