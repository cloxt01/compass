<?php

namespace App\Services\Jobstreet;

use App\Clients\JobstreetAPI;

class AppliedJobs
{
    protected JobstreetAPI $client;

    public function __construct(JobstreetAPI $client)
    {
        $this->client = $client;
    }

    public function get_applied_jobs(int $limit): array
    {

        $resp = $this->client->graphql('GetAppliedJobs', ['first' => $limit]);
        $data = [];

        if(isset($resp['data']['viewer']['appliedJobs']['edges'])){
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
            return [
                'success' => true,
                'code' => $resp['http_code'],
                'data' => $data
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Failed to get applied jobs.',
                'code' => $resp['http_code'],
                'error' => $resp['data'],
            ];
        }
    }
}