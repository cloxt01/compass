<?php

namespace App\Domain\Jobstreet;

use App\Clients\JobstreetAPI;

use App\Services\JobstreetService;
use App\Domain\Jobstreet\JobDetailsReader;
use App\Domain\Jobstreet\JobEligibilityEvaluator;
use App\Domain\Jobstreet\JobApplicationProcess;
use App\Support\QuestionnaireParser;

class ApplicationPayloadBuilder {

    protected array $resume;
    protected array $roles;
    protected array $profile_visibility;

    public function __construct(JobstreetAPI $client, JobstreetService $service){
        $this->client = $client;

        $document_service = $service->documents();
        $review_service = $service->review();

        $this->resume = $document_service->get_latest_resume();
        $this->roles = $review_service->get_latest_roles();
        $this->profile_visibility = $review_service->get_profile_visibility();
    }


    public function build(array $details, array $questionnaire = []): array
    {

        $payload = [
            "jobId" => $details['id'],
            "jobTitle" => $details['title'],
            "companyName" => $details['company']
        ];
        // checking & adding resume
        if (empty($this->resume)) {
            throw new \Exception("Tidak ada resume yang ditemukan.");
        }
        $payload += [
            "resume" => [
                "id" => $this->resume['id'],
                "uri" => $this->resume['fileMetadata']['uri']
            ]
        ];

        // checking & adding roles
        if (!empty($this->roles)) {
            $payload += [
                "roles" => [
                    "company" => $this->roles['company']['text'] ?? 'Unknown',
                    "title" => $this->roles['title']['text'] ?? 'Unknown'
                ]
            ];
            
            if (isset($this->roles['from']['year']) && isset($this->roles['from']['month'])) {
                $payload['roles']['started'] = [
                    "year" => (int)($this->roles['from']['year']),
                    "month" => (int)($this->roles['from']['month'])
                ];
            }
            if (isset($this->roles['to']['year']) && isset($this->roles['to']['month'])) {
                $payload['roles']['finished'] = [
                    "year" => (int)($this->roles['to']['year']),
                    "month" => (int)($this->roles['to']['month'])
                ];
            }
        }

        // adding profile_visibility & question
        $payload += [
            "profileVisibility2" => $this->profile_visibility['profileVisibility2'],
            "questionnaireAnswers" => QuestionnaireParser::prepareAndAnswerFromGraphQL($questionnaire)
        ];

        return $payload;
    }
}