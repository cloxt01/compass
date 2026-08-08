<?php

namespace App\Services\Adapters\Provider;

use App\Clients\GlintsAPI;
use App\Infrastructure\Contracts\Platform\PlatformAdapter;
use App\Infrastructure\Factory\PlatformFactory;
use App\Services\Adapters\AI\AIAdapter;
use App\Services\Platform\Glints\GlintsJob;
use App\Services\Platform\Glints\GlintsProfile;

// Adapters

// Services

class GlintsAdapter implements PlatformAdapter {

    const PROVIDER_NAME = 'Glints';
    const PROVIDER_CODE = 'glints';


    public function __construct(
        protected GlintsAPI $client
    ){
    }

    protected function ai(): AIAdapter {
        return new AIAdapter();
    }
    protected function profile():GlintsProfile {
        return new GlintsProfile($this->client);
    }
    public function job():GlintsJob {
        return new GlintsJob($this->client);
    }

    public function loadJob(string $jobId, $hiring_question = true): array {
        $raw = $this->job()->details($jobId, $hiring_question);
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

    public function answerQuestion(array $profile, array $question): array
    {
        return $this->ai()->autoAnswer(self::PROVIDER_CODE, $profile, $question);
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
        return bin2hex(random_bytes(16));
    }
    public function execute(string $jobId, array $payload, array $config = []): bool
    {
        return $this->job()->apply($jobId, $payload, $config);
    }

}
