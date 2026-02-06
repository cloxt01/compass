<?php

namespace App\Services\Jobstreet;

use App\Clients\JobstreetAPI;
use App\Services\JobstreetService;

class AppliedJobs extends JobstreetService
{
    protected ?array $data = null;

    public function __construct(JobstreetAPI $client)
    {
        parent::__construct($client);
    }

    public function load(): array
    {
        if ($this->data === null) {
            $this->data = $this->applied_jobs();
        }
        return $this->data;
    }

    public function applied_jobs(int $limit = 10): array
    {

        $resp = $this->client->graphql('GetAppliedJobs', ['first' => $limit]);
        $data = [];

        if(!isset($resp['data']['viewer']['appliedJobs']['edges'])){
            return [];
        }
        foreach ($resp['data']['viewer']['appliedJobs']['edges'] as $job) {
            $data[] = [
                'job_id' => $job['node']['job']['id'],
                'job_title' => $job['node']['job']['title'] ?? '',
                'job_location' => $job['node']['job']['location']['label'] ?? 'Unknown',
                'company' => $job['node']['job']['advertiser']['name'] ?? 'Unknown',
                'status' => $job['node']['events'][count($job['node']['events']) - 1]['status'] ?? 'Unknown',
                'platform' => 'Jobstreet',
                'url' => "https://id.jobstreet.com/id/job/" . $job['node']['job']['id']
            ];
        }
        return $data;
    }
}

