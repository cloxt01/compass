<?php

namespace App\Infrastructure\Factory;

use App\Clients\GlintsAPI;
use App\Clients\JobstreetAPI;
use App\Exceptions\CantApply;
use App\Infrastructure\Contracts\PlatformAdapter;
use App\Models\User;
use App\Services\Adapters\GlintsAdapter;
use App\Services\Adapters\JobstreetAdapter;
use App\Support\QuestionnaireParser;
use Illuminate\Support\Facades\Log;

class PlatformFactory
{
    public static function make(string $provider, User $user): ?PlatformAdapter
    {
        $account = match ($provider) {
            'glints' => $user->glintsAccount,
            'jobstreet' => $user->jobstreetAccount,
            default => null
        };
        if (!$account) {
            return null;
        }

        return match ($provider) {
            'glints' => (new GlintsAdapter(new GlintsAPI($account->access_token, $account->cookie))),
            'jobstreet' => (new JobstreetAdapter(new JobstreetAPI($account->access_token))),
            default => null
        };
    }

    public static function job_reader(string $provider, array $data): array
    {
        if (empty($data) || !$data || empty($data)) {
            return [];
        }
        Log::info(json_encode($data));
        return match ($provider) {
            'jobstreet' => [
                'metadata' => [
                        'id' => $data['details']['job']['id'] ?? 'Unknown',
                        'title' => $data['details']['job']['title'] ?? 'Unknown',
                        'advertiser' => $data['details']['job']['advertiser']['name'] ?? 'Unknown',
                        'company' => $data['details']['companyProfile']['name'] ?? 'Unknown',
                        'location' => $data['details']['job']['location']['label'] ?? 'Unknown'
                    ] ?? [],
                'eligibility' => [
                        'expired' => $data['details']['job']['isExpired'] ?? false,
                        'linkout' => $data['details']['job']['isLinkOut'] ?? false,
                        'applied' => empty($data['details']['personalised']['appliedDateTime']) ? false : true
                    ] ?? [],
                'insights' => [
                        'applicantsCount' => $data['details']['insights'][0]['count'] ?? 0
                    ] ?? [],
                'products' => [
                        'questionnaire' => $data['process']['questionnaire'] ?? []
                    ] ?? []
            ],
            'glints' => [
                'metadata' => [
                    'id' => $data['details']['id'] ?? '',
                    'title' => $data['details']['title'] ?? 'Unknown',
                    'company' => $data['details']['company']['name'] ?? 'Unknown',
                    'location' => $data['details']['location']['formattedName'] ?? 'Unknown',
                ],
                'eligibility' => [
                    'linkout' => $data['details']['job']['externalApplyURL'] ?? false,
                    'expired' => (empty($data['details']['expiryDate']) && empty($data['details']['closedAt'])) ? null : (strtotime($data['details']['expiryDate'] ?? '') < time() || !empty($data['details']['closedAt'])),
                    'closed' => $data['details']['status'] === 'CLOSED' ? true : false,
                    'applied' => $data['details']['isApplied'] ?? false,
                ],
                'insights' => [
                    'isActivelyHiring' => $data['details']['isActivelyHiring'] ?? false,
                    'isHot' => $data['details']['isHot'] ?? false,
                    'creatorResponseRate' => $data['details']['creatorResponseRate'] ?? null,
                    'creatorResponseTime' => $data['details']['creatorResponseTime'] ?? null,
                    'isHighResponseRate' => $data['details']['isHighResponseRate'] ?? false,
                ],
                'products' => [
                    'questionnaire' => $data['hiring_question']['employerScreeningQuestions'] ?? []
                ]
            ]
        };
    }

    public static function job_inspector(string $provider, $data): array
    {
        $issues = [];

        if ($data['eligibility']['expired'] !== false) {
            $issues[] = [
                'type' => 'expired',
                'level' => 'hard',
                'message' => 'Job sudah expired'
            ];
        }
        if ($data['eligibility']['linkout']) {
            $issues[] = [
                'type' => 'linkout',
                'level' => 'hard',
                'message' => 'Job adalah link keluar'
            ];
        }
        if ($data['eligibility']['applied']) {
            $issues[] = [
                'type' => 'applied',
                'level' => 'hard',
                'message' => "Anda sudah melamar pekerjaan ini"
            ];
        }

        if (!empty($data['products']['questionnaire'])) {
            $issues[] = [
                'type' => 'questionnaire',
                'level' => 'hard',
                'message' => 'Anda harus menjawab pertanyaan untuk melamar pekerjaan ini'
            ];
        }

        usort($issues, function ($a, $b) {
            if ($a['level'] === $b['level']) {
                return 0;
            }
            return $a['level'] === 'hard' ? -1 : 1;
        });


        return [
            'canApply' => empty(array_filter($issues, fn($x) => $x['level'] === 'hard')),
            'issues' => $issues
        ];
    }

    public static function job_question(string $provider, $data): array
    {
        $result = [];

        $questions = match ($provider) {
            'glints' => $data['data']['getJobHiringQuestions'],
            'jobstreet' => [],
            default => null
        };

        switch ($provider) {
            case 'jobstreet':
                break;
            case 'glints':
                foreach ($questions['predefinedQuestions'] as $question) {
                    $result['predefinedQuestions'][] = [
                        'name' => $question['name'],
                        'type' => $question['type'],
                        'required' => $question['required']
                    ];
                }
                foreach ($questions['employerScreeningQuestions'] as $question) {
                    $result['employerScreeningQuestions'][] = [
                        'name' => $question['name'],
                        'label' => $question['label'],
                        'type' => $question['questionType'],
                        'options' => $question['questions'][0]['responseOptions'] ?? [],
                    ];
                }
                break;
        }

        return $result;
    }

    public static function profile_reader($provider, $data): array
    {

        return match ($provider) {
            'jobstreet' => [
                'resumes' => $data['document']['resumes'] ?? [],
                'roles' => $data['review']['roles'] ?? [],
                'skills' => $data['review']['skills'] ?? [],
                'latest_resume' => ($data['document']['resumes'] ?? []) ? end($data['document']['resumes']) : null,
                'latest_roles' => $data['review']['roles'][0] ?? null,
                'profile_visibility' => [
                    '1' => $data['review']['profileVisibility']['level'] ?? null,
                    '2' => $data['review']['profileVisibility2']['id'] ?? null
                ],
                'qualifications' => $data['review']['qualifications'] ?? [],
                'reference_checks' => $data['review']['referenceChecks'] ?? []
            ],
            'glints' => [
                'id' => $data['id'] ?? null,
                'email' => $data['email'] ?? null,
                'first_name' => $data['firstName'] ?? null,
                'last_name' => $data['lastName'] ?? null,
                'full_name' => ($data['firstName'] ?? '') . " " . ($data['lastName'] ?? '') ?? null,
                'resume' => $data['resume'] ?? null,
                'gender' => $data['gender'] ?? null,
                'phone' => $data['phone'] ?? null,
                'whatsappNumber' => $data['whatsappNumber'] ?? null,
                'isPhoneNumberVerified' => $data['isPhoneNumberVerified'] ?? null,
                'isWhatsappVerified' => $data['isWhatsappVerified'] ?? null,
                'isVerified' => $data['isVerified'] ?? false,
                'careerStartDate' => $data['careerStartDate'] ?? null,
                'highestEducation' => $data['highestEducationLevel'] ?? null,
                'preferredLocations' => $data['preferredLocations'] ?? [],
                'applicationsCount' => $data['applicationsCount'] ?? null,
            ]
        };
    }

    public static function build_payload($provider, $data): array
    {
        $payload = [];

        switch ($provider) {
            case 'jobstreet':
                if(empty($data['details'])){
                    throw new CantApply("Detail pekerjaan tidak ditemukan.", 408);
                }
                if(empty($data['profile']['latest_resume'])) {
                    throw new CantApply("Tidak ada resume yang ditemukan.", 409);
                }

                foreach ($data['profile']['resumes'] as $resume) {
                    if ($resume['id'] === ($data['config']['resume'] ?? '')) {
                        $selectedResume = $resume;
                        break;
                    }
                }
                foreach ($data['profile']['roles'] as $role) {
                    if ($role['id'] === ($data['config']['role'] ?? '')) {
                        $selectedRole = $role;
                        break;
                    }
                }

                $resume = $selectedResume ?? $data['profile']['latest_resume'] ?? null;
                $roles = $selectedRole ?? $data['profile']['latest_roles'] ?? [];

                if (empty($roles)) {
                    Log::warning("Tidak ada role yang ditemukan.");
                }

                if (empty($resume)) {
                    Log::warning("Tidak ada resume yang ditemukan.");
                }


                $profile_visibility2 = $data['profile']['profile_visibility']['2'] ?? [];



                $payload = [
                    "jobId" => $data['details']['metadata']['id'],
                    "jobTitle" => $data['details']['metadata']['title'],
                    "companyName" => $data['details']['metadata']['company']
                ];

                $payload += [
                    "resume" => [
                        "id" => $resume['id'],
                        "uri" => $resume['fileMetadata']['uri']
                    ]
                ];

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

                $payload += [
                    "profileVisibility2" => $profile_visibility2,
                    "questionnaireAnswers" => QuestionnaireParser::prepareAndAnswerFromGraphQL($data['details']['products']['questionnaire'])
                ];

                break;
            case 'glints':
                $resume = $data['profile']['resume'] ?? null;
                if(!$resume){
                    throw new CantApply("Tidak ada resume yang ditemukan.", 409);
                }
                $payload = [
                    'data' => [
                        'resume' => $data['profile']['resume'],
                        'employerScreeningQuestionAnswers' => [],
                        'note' => '',
                        'attachments' => []
                    ],
                    'source' => 'For You',
                    'traceInfo' => $data['config']['traceInfo'] ?? bin2hex(random_bytes(16)),
                ];
                break;
        }
        return $payload;
    }
}
