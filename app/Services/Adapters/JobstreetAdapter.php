<?php

namespace App\Services\Adapters;

use App\Clients\JobstreetAPI;
use App\Infrastructure\Contracts\PlatformAdapter;
use App\Infrastructure\Jobstreet\JobstreetPayloadBuilder;

// Adapters
use App\Services\Jobstreet\JobstreetProfile;
use App\Services\Jobstreet\JobstreetJob;

// Services
use App\Services\JobInspector;
use App\Services\ProfileDetails;
use App\Services\JobDetails;

class JobstreetAdapter implements PlatformAdapter {
    
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
    public function buildPayload(array $jobDetails, array $profileDetails): array
    {
        return $this->builder->build($jobDetails, $profileDetails);
    }

    public function canApply(array $details): array
    {
        return JobInspector::fromJobstreet($details);
    }

    public function execute(array $payload): bool
    {
        return $this->job()->apply($payload);
    }

}