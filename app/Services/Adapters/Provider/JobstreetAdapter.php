<?php

namespace App\Services\Adapters\Provider;

use App\Clients\JobstreetAPI;
use App\Infrastructure\Contracts\Platform\PlatformAdapter;
use App\Infrastructure\Factory\PlatformFactory;
use App\Services\Platform\Jobstreet\JobstreetJob;
use App\Services\Platform\Jobstreet\JobstreetProfile;

// Adapters

// Services

class JobstreetAdapter implements PlatformAdapter {

    const PROVIDER_NAME = 'Jobstreet';
    const PROVIDER_CODE = 'jobstreet';

    public function __construct(protected JobstreetAPI $client){
        $this->client = $client;
    }

    protected function profile():JobstreetProfile {
        return new JobstreetProfile($this->client);
    }
    public function job():JobstreetJob {
        return new JobstreetJob($this->client);
    }

    public function loadJob(string $jobId): array {
        $raw = $this->job()->details($jobId);
        return PlatformFactory::job_reader(self::PROVIDER_CODE, $raw);
    }

        public function isLimit(string $jobId = 'a5839963-283f-460b-8daa-cd6d7e7014c7'): bool
    {
        return $this->job()->is_limit($jobId);
    }

    public function loadProfile(): array
    {
        $raw = $this->profile()->load();
        return PlatformFactory::profile_reader(self::PROVIDER_CODE, $raw);
    }


    public function buildPayload($data): array
    {
        return PlatformFactory::build_payload(self::PROVIDER_CODE, $data);
    }

    public function canApply(array $details): array
    {
        return PlatformFactory::job_inspector(self::PROVIDER_CODE, $details);
    }
    public function generateTraceInfo(): string {
        return '';
    }

    public function execute(string $jobId, array $payload, array $config = []): bool
    {
        return $this->job()->apply($jobId, $payload);
    }


}
