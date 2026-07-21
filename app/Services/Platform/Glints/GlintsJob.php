<?php

namespace App\Services\Platform\Glints;

use App\Clients\GlintsAPI;
use App\Infrastructure\Factory\PlatformFactory;
use App\Services\Adapters\Provider\GlintsAdapter;
use App\Support\DataHelper;
use Illuminate\Support\Facades\Log;

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
                'connection' => 'Jobstreet',
                'url' => "https://id.jobstreet.com/id/job/" . $job['node']['job']['id']
            ];
        }
        return $data;
    }
    public function hiring_question(array $params = []): array
    {
        if(!isset($params['jobId'])){
            return [];
        }
        $result = $this->client->graphql('jobHiringQuestion', $params);
        if (!isset($result) || !is_array($result)) {
            Log::warning("Job Hiring Question returned an error : ". json_encode($result));
            return [];
        }
        return PlatformFactory::job_question(self::PROVIDER_CODE, $result);
    }

    /*
     * Params :
     *  --- Required ---
     *  - Key : type (default)
     *  ----------------
     *  - CountryCode : string (ID)
     *  - SearchTerm : string ('')
     *  - includeExternalJobs : bool (false)
     *  - page : int (1)
     *  - pageSize : int (30)
     */
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
            Log::info("Job search params tidak tervalidasi: ". json_encode($params));
            return [];
        }
        $response = $this->client->graphql('searchJobsV3', $params, ['isv2' => true]);

        if(!$response['ok'] || !isset($response['data']['searchJobsV3']['jobsInPage'])){
            return [];
        }
        return $response['data']['searchJobsV3']['jobsInPage'];
    }

    public function details(string $jobId): array
    {
        $details = $this->client->get('/v2/job/'. $jobId);
        if(!isset($details['data']['data'])){
            Log::info("Job Details tidak menampilkan data: ". json_encode($details));
            return [];
        }

        $hiring_question = $this->hiring_question(['jobId' => $jobId]);


        $resp = array_merge(["details" => $details['data']['data']], ["hiring_question" => $hiring_question]);
        return $resp;
    }
    public function apply(string $jobId, array $payload, $config): bool
    {
        if(!isset($jobId)){
            return false;
        }
        $path = '/v2/v2/jobs/' . $jobId. '/applications';
        $this->client->headers['Referer'] = 'https://glints.com/id/opportunities/jobs/'.$jobId.'/apply?utm_referrer=fyp&traceInfo='.$config['traceInfo'];
        $resp = $this->client->post($path, $payload);

//        print_r("Host : ".$this->client->host.$path);
//        print_r("\nHeaders :". json_encode($this->client->headers));
//        print_r("\nPayload : ".json_encode($payload));
//        print_r("\nResponse : ".json_encode($resp));

        Log::info('Job Apply: '.json_encode([
            'jobId' => $jobId,
            'payload' => $payload,
            'response' => $resp
        ]));
        if(
            (
                isset($resp['data']['data']['applicationId']) ||
                isset($resp['data']['data']['id'])) && (
                    !empty($resp['data']['data']['applicationId']) ||
                    !empty($resp['data']['data']['id']))
            ){
            return true;
        } else {
            Log::warning("Gagal melamar pekerjaan: " . json_encode($resp));
            return false;
        }

    }


}
