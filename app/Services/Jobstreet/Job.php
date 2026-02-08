<?php

namespace App\Services\Jobstreet;

use App\Clients\JobstreetAPI;
use App\Services\JobstreetService;

class Job extends JobstreetService
{
    protected ?array $data = null;

    public function __construct(JobstreetAPI $client)
    {
        parent::__construct($client);
    }

    public function applied(int $limit = 1): array
    {

        $resp = $this->client->graphql('GetAppliedJobs', ['first' => $limit]);
        $data = [];

        if(!isset($resp['data']['data']['viewer']['appliedJobs']['edges'])){
            return [];
        }
        foreach ($resp['data']['data']['viewer']['appliedJobs']['edges'] as $job) {
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
    public function search(array $params = []){
        $path = '/api/jobsearch/v5/me/search';
        $params = [
            'siteKey' => 'ID-Main',
            'page' => 1,
            'keywords' => $params['keyword'],
            'where' => $params['location'],
            'pageSize' => (int)($params['pageSize']),
            'locale' => 'id-ID'];
        return $this->client->get($path, $params);
    }
}

