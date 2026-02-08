<?php

namespace App\Infrastructure\Jobstreet;



use App\Support\QuestionnaireParser;

use App\Exceptions\JobNotFound;
use App\Exceptions\ResumeNotFound;



class JobstreetPayloadBuilder {

    public function __construct(){
    }


    public function build(array $details, array $profile): array
    {
        if(empty($details)){
            throw new \JobNotFound("Detail pekerjaan tidak ditemukan.");
        }
        if(empty($profile['latest_resume'])) {
            throw new \ResumeNotFound("Tidak ada resume yang ditemukan.");
        }

        $resume = $profile['latest_resume'];
        $roles = $profile['latest_roles'] ?? [];
        $profile_visibility2 = $profile['profile_visibility']['2'] ?? [];

        // Tambahkan informasi pekerjaan
        $payload = [
            "jobId" => $details['metadata']['id'],
            "jobTitle" => $details['metadata']['title'],
            "companyName" => $details['metadata']['company']
        ];

        // Tambahkan informasi resume terakhir
        $payload += [
            "resume" => [
                "id" => $resume['id'],
                "uri" => $resume['fileMetadata']['uri']
            ]
        ];

        // Tambahkan informasi pengalaman kerja jika tersedia
        if(!empty($roles)){
            $payload += [
                "roles" => [
                    "company" => $roles['company']['text'] ?? 'Unknown',
                    "title" => $roles['title']['text'] ?? 'Unknown'
                ]
            ];
            
            if (isset($roles['from']['year']) && isset($roles['from']['month'])) {
                $payload['roles']['started'] = [
                    "year" => (int)($roles['from']['year']),
                    "month" => (int)($roles['from']['month'])
                ];
            }
            if (isset($roles['to']['year']) && isset($roles['to']['month'])) {
                $payload['roles']['finished'] = [
                    "year" => (int)($roles['to']['year']),
                    "month" => (int)($roles['to']['month'])
                ];
            }
        }
        
        // Tambahkan visibilitas profil dan jawaban kuesioner
        $payload += [
            "profileVisibility2" => $profile_visibility2,
            "questionnaireAnswers" => QuestionnaireParser::prepareAndAnswerFromGraphQL($details['products']['questionnaire'])
        ];

        return $payload;
    }
}