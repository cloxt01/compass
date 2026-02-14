<?php

namespace App\Application\UseCase;

use Illuminate\Support\Facades\Log;

use App\Infrastructure\Contracts\PlatformAdapter;
use App\Infrastructure\Contracts\PlatformAccount;

class ApplyUseCase {

    public function __construct(
        private PlatformAdapter $adapter,
        private PlatformAccount $account
    ) {
        $this->account = $account;
        $this->adapter = $adapter;
    }

    public function apply(string $jobId): bool {
        $job = $this->adapter->loadJob($jobId);
        $profile = $this->adapter->loadProfile();
        $config = $this->account->apply_configurations;

        
        if(!$this->adapter->canApply($job)['canApply']){
            Log::warning("Tidak dapat melamar pekerjaan ID: " . $jobId . " karena tidak memenuhi syarat.");
            Log::warning(json_encode($this->adapter->canApply($job)['issues']));
            return false;
        }
        $payload = $this->adapter->buildPayload($job, $profile, $config);
        return $this->adapter->execute($payload);
    }
}