<?php

namespace App\Domain\Jobstreet;

use App\Clients\JobstreetAPI;

class JobApplicationProcess
{
    protected ?array $raw = null;
    protected JobstreetAPI $client;

    public function __construct(JobstreetAPI $client){
        $this->client = $client;
    }

    public function load(string $jobId): JobApplicationProcess
    {
        
         $this->raw = $this->client->graphql('GetJobApplicationProcess', ['jobId' => $jobId])['data']['data']['jobApplicationProcess'];
         return $this;
    }

    public function questionnaire(): array {
        return $this->raw['questionnaire'] ?? [];
    }
}   