<?php

namespace App\Services\Adapters;

use App\Clients\JobstreetAPI;
use App\Infrastructure\Contracts\PlatformAdapter;
use App\Infrastructure\Jobstreet\JobstreetPayloadBuilder;
use App\Services\JobDetails;
use App\Services\JobInspector;
use App\Services\Jobstreet\JobstreetJob;
use App\Services\Jobstreet\JobstreetProfile;
use App\Services\ProfileDetails;

// Adapters

// Services

class JobstreetAdapter implements PlatformAdapter {

    protected $builder;
    public function __construct(protected JobstreetAPI $client){
        $this->client = $client;
        $this->builder = new JobstreetPayloadBuilder();
    }

    protected function profile():JobstreetProfile {
        return new JobstreetProfile($this->client);
    }
    public function job():JobstreetJob {
        return new JobstreetJob($this->client);
    }

    public function loadJob(string $jobId): array {
        $raw = $this->job()->details($jobId);
        return JobDetails::fromJobstreet($raw);
    }

    public function loadProfile(): array
    {
        $raw = $this->profile()->load();
        return ProfileDetails::fromJobstreet($raw);
    }


    public function buildPayload(array $jobDetails, array $profileDetails, array $config=[]): array
    {

        return $this->builder->build($jobDetails, $profileDetails, $config);
    }

    public function canApply(array $details): array
    {
        return JobInspector::fromJobstreet($details);
    }
    public function generateTraceInfo(): string {
        return '';
    }

    public function execute(string $jobId, array $payload, array $config = []): bool
    {
        return $this->job()->apply($jobId, $payload);
    }


}
