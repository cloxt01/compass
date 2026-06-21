<?php

namespace App\Services\Glints;

use App\Services\JobQuestion;
use App\Support\DataHelper;
use Illuminate\Support\Facades\Log;
use App\Clients\GlintsAPI;
use App\Services\Adapters\GlintsAdapter;

class GlintsJob extends GlintsAdapter
{
    protected ?array $data = null;

    public function __construct(GlintsAPI $client)
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
        Log::info("Fetched applied jobs for user: " . count($data) . " jobs found.");
        return $data;
    }
    public function hiring_question(array $params = []): array
    {
        if(!isset($params['jobId'])){
            return [];
        }
        $result = $this->client->graphql('jobHiringQuestion', $params);
        if (!isset($result) || !is_array($result)) {
            Log::info("Job Hiring Question returned an error : ". json_encode($result));
            return [];
        }
        Log::info("Job Hiring Question result: " . json_encode($result));
        return JobQuestion::fromGlints($result) ?: [];
    }
    public function search(array $params = []): array
    {
        $list = ['CountryCode' => ['ID'],
            'lastUpdatedAtRange' => ['ANY_TIME', 'PAST_MONTH', 'PAST_24_HOURS', 'PAST_WEEK'],
            'type' => [
                'FULL_TIME',
                'CONTRACT',
                'INTERNSHIP',
                'PROJECT_BASED',
                'PART_TIME'
            ],
            'workArrangementOptions' => [
                'ONSITE',
                'HYBRID',
                'REMOTE'
            ],
            'educationLevels' => [
                "PRIMARY_SCHOOL",
                "SECONDARY_SCHOOL",
                "HIGH_SCHOOL",
                "DIPLOMA",
                "BACHELOR_DEGREE",
                "PROFESSIONAL_EDUCATION",
                "MASTER_DEGREE",
                "DOCTORATE"
            ],
            'yearsOfExperienceFilter' => [
                ['range' => [
                    "NO_EXPERIENCE",
                    "FRESH_GRAD",
                    "LESS_THAN_A_YEAR",
                    "ONE_TO_THREE_YEARS",
                    "THREE_TO_FIVE_YEARS",
                    "FIVE_TO_TEN_YEARS",
                    "MORE_THAN_TEN_YEARS"
                ]]
            ]
            ];

        if(!DataHelper::validateJobSearchParams($params, $list)){
            return [];
        }

        return $this->client->graphql('searchJobsV3', $params, ['isv2' => true]) ?? [];
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

