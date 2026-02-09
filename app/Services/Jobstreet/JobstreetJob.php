<?php

namespace App\Services\Jobstreet;

use Illuminate\Support\Facades\Log;
use App\Clients\JobstreetAPI;
use App\Services\Adapters\JobstreetAdapter;

class JobstreetJob extends JobstreetAdapter
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
    public function search(array $params = []): array
    {
        $path = '/api/jobsearch/v5/me/search';
        
        $params = [
            'siteKey' => 'ID-Main',
            'page' => 1,
            'keywords' => $params['keyword'],
            'where' => $params['location'],
            'pageSize' => (int)($params['pageSize']),
            'locale' => 'id-ID'
        ];
        // $searchParams = [
        //     'eventcapturesessionid' => null,              // kosong = null, bukan string kosong
        //     'include'               => 'seogptTargeting,relatedsearches',
        //     'keywords'              => 'Developer',
        //     'locale'                => 'id-ID',
        //     'page'                  => 1,                 // integer
        //     'pagesize'              => 10,                // jangan bunuh hasil sendiri
        //     'sitekey'               => 'ID-Main',
        //     'source'                => 'FE_SERP',
        //     'sourcesystem'          => 'houston',
        //     'userid'                => null,              // anonymous, tapi eksplisit
        //     'usersessionid'         => null,              // sama
        //     'where'                 => 'Banten',
        // ];
        return $this->client->get($path, $params) ?? [];
    }

    public function details(string $jobId): array
    {
        $details = $this->client->graphql('jobDetailsWithPersonalised', ['jobId' => $jobId])['data']['data']['jobDetails'] ?? [];
        $process = $this->client->graphql('GetJobApplicationProcess', ['jobId' => $jobId])['data']['data']['jobApplicationProcess'] ?? [];

        $resp = array_merge(["details" => $details], ["process" => $process]);
        
        return $resp;
    }
    public function apply(array $payload): bool
    {
        $resp = $this->client->graphql('ApplySubmitApplication', $payload);
        if($resp['ok'] && $resp['data']['data']['submitApplication']['__typename'] === 'SubmitApplicationSuccess'){
            return true;
        } else {
            Log::error("Gagal melamar pekerjaan: " . json_encode($resp));
            return false;
        }
    }
}

