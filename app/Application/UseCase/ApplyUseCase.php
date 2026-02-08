<?php

namespace App\Application\UseCase;

use Illuminate\Support\Facades\Log;

use App\Infrastructure\Contracts\PlatformAdapter;

class ApplyUseCase {

    public function __construct(
        private PlatformAdapter $adapter
    ) {
        $this->adapter = $adapter;
    }

    public function apply(string $jobId): bool {
        $job = $this->adapter->loadJob($jobId);
        $profile = $this->adapter->loadProfile();
        if(empty($job)){
            Log::error("Gagal memuat detail pekerjaan untuk ID: " . $jobId);
            return false;
        }
        if(!$this->adapter->canApply($job)['canApply']){
            Log::warning("Tidak dapat melamar pekerjaan ID: " . $jobId . " karena tidak memenuhi syarat.");
            Log::warning(json_encode($this->adapter->canApply($job)['issues']));
            return false;
        }

        $payload = $this->adapter->buildPayload($job, $profile);
        return $this->adapter->execute($payload);
    }
}