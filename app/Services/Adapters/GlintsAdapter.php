<?php

namespace App\Services\Adapters;

use App\Clients\GlintsAPI;
use App\Infrastructure\Contracts\PlatformAdapter;
use App\Infrastructure\Glints\GlintsPayloadBuilder;
use App\Services\Glints\GlintsJob;
use App\Services\Glints\GlintsProfile;
use App\Services\JobDetails;
use App\Services\JobInspector;
use App\Services\ProfileDetails;

// Adapters

// Services

class GlintsAdapter implements PlatformAdapter {

    protected $builder;
    public function __construct(
        protected GlintsAPI $client
    ){
        $this->builder = new GlintsPayloadBuilder();
    }

    protected function profile():GlintsProfile {
        return new GlintsProfile($this->client);
    }
    public function job():GlintsJob {
        return new GlintsJob($this->client);
    }

    public function loadJob(string $jobId): array {
        $raw = $this->job()->details($jobId);
        return JobDetails::fromGlints($raw);
    }

    public function loadProfile(): array
    {
        $raw = $this->profile()->load();
        return ProfileDetails::fromGlints($raw);
    }


    public function buildPayload(array $jobDetails, array $profileDetails, array $config=[]): array
    {

        return $this->builder->build($jobDetails, $profileDetails, $config);
    }

    public function canApply(array $details): array
    {
        return JobInspector::fromGlints($details);
    }
    public function generateTraceInfo(): string {
        return bin2hex(random_bytes(16));
    }
    public function execute(string $jobId, array $payload, array $config = []): bool
    {
        return $this->job()->apply($jobId, $payload, $config);
    }

}
