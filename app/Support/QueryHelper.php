<?php
namespace App\Support;

use App\Support\FileHelper;
use App\Clients\JobstreetAPI;
use App\Clients\GlintsAPI;
use App\Exceptions\UnknownOperation;


use Illuminate\Support\Facades\DB;

class QueryHelper {
    public static function generateUUID() {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }
    public static function loadGraphQLQuery(JobstreetAPI | GlintsAPI $client, $operationName) {
        $path = resource_path("query/".$client::provider."/$operationName.gql");

        
        if (!file_exists($path)) {
            throw new UnknownOperation($operationName);
            // throw new UnknownOperation("File query tidak ditemukan di: " . $path);
        }

        $query = file_get_contents($path);
        return preg_replace('/\s+/', ' ', trim($query));
    }
    public static function buildGraphQLVariables(JobstreetAPI | GlintsAPI $client, string $operationName, array $variables): array | \stdClass {
        if ($client::provider == 'jobstreet'){
            $vars = match($operationName) {
            'jobDetailsWithPersonalised' => [
                "jobId" => $variables['jobId'],
                "jobDetailsViewedCorrelationId" => self::generateUUID(),
                "sessionId" => $client->sessionId,
                "zone" => "asia-4",
                "locale" => "id-ID",
                "languageCode" => "id",
                "countryCode" => "ID",
                "timezone" => "Asia/Jakarta",
                "enableGetCandidateJobMatch" => false,
                "platform" => "WEB",
                "visitorId" => self::generateUUID()
            ],
            'jobDetailsPersonalised' => [
                "enableGetCandidateJobMatch" => false,
                "id" => $variables['jobId'],
                "languageCode" => "id",
                "locale" => "id-ID",
                "platform" => "WEB",
                "timezone" => "Asia/Jakarta",
                "tracking" => [
                    "channel" => "WEB",
                    "jobDetailsViewedCorrelationId" => self::generateUUID(),
                    "sessionId" => $client->sessionId,
                ],
                "visitorId" => self::generateUUID(),
                "zone" => "asia-4"
            ],
            'GetJobApplicationProcess' => [
                "jobId" => $variables['jobId'],
                "isAuthenticated" => true,
                "locale" => "id-ID"
            ],
            'DocumentsQuery' => [
                "jobId" => $variables['jobId'] ?? '',
                "locale" => "id-ID"
            ],
            
            'ApplySubmitApplication' => [
                "input" => [
                    "jobId" => $variables['jobId'],
                    "correlationId" => self::generateUUID(),
                    "zone" => "asia-4",
                    "profilePrivacyLevel" => $variables['profileVisibility2'],
                    "resume" => [
                        "id" => $variables['resume']['id'],
                        "uri" => $variables['resume']['uri'],
                        "idFromResumeResource" => -1
                    ],
                    "mostRecentRole" => [
                        "company" => $variables['roles']['company'],
                        "title" => $variables['roles']['title'],
                        "started" => [
                            "year" => (int)$variables['roles']['started']['year'],
                            "month" => (int)$variables['roles']['started']['month']
                        ]
                    ],
                    "questionnaireAnswers" => $variables['questionnaireAnswers'] ?? []
                ],
                "locale" => "id-ID"
            ],
            'GetVerifications' => [
                "jobId" => $variables['jobId'],
                "selectedAnswerUris" => $variables['selectedAnswerUris'] ?? [],
                "zone" => "asia-4"
            ],
            'GetPriorityApplyCredit' => [
                "locale" => "id-ID",
                "timezone" => "Asia/Jakarta"
            ],
            'GetAppliedJobs' => [
                "first" => $variables['first'],
                "locale" => "id-ID",
                "timezone" => "Asia/Jakarta",
                "zone" => "asia-4",
                "selectedAnswerUris" => [],
                "sortBy" => [
                    "applicationDate" => "DESC"
                ]
            ],
            'ReviewPage' => [
                "jobId" => "",
                "zone" => "asia-4",
                "languageCode" => "id",
                "locale" => "id-ID"
            ],
            'GetPersonalDetails' => [
                "languageCode" => "id",
                "countryCode" => "ID",
                "zone" => "asia-4"
            ],
            'GetPublicProfile' => [
                "zone" => "asia-4",
                "locale" => "id",
                "platform" => "WEB"
            ],
            'GetProfileVisibility2', 'GetProfileVisibilityOptions' => [
                "locale" => "id-ID",
                "zone" => "asia-4"
            ],
            'GetScore' => [
                "zone" => "asia-4",
                "languageCode" => "id",
                "isUnifiedSite" => true
            ],
            'GetConfirmedQualifications', 'GetUnconfirmedQualifications' => [
                "languageCode" => "id",
                "zone" => "asia-4",
                "isUnifiedSite" => true
            ],
            'GetLicences' => [
                "languageCode" => "id",
                "zone" => "asia-4"
            ],
            'GetSuggestedSkills' => [
                "languageCode" => "id",
                "zone" => "asia-4"
            ],
            'GetResumes' => [
                "languageCode" => "id"
            ],
            'GetSalaryPreferences' => [
                "countryCode" => "ID",
                "languageCode" => "id"
            ],
            'GetRightsToWork' => [
                "languageCode" => "id",
                "zone" => "asia-4",
                "isUnifiedSite" => true
            ],
            'GetAvailability' => [
                "languageCode" => "id"
            ],
            'GetPreferredLocations2' => [
                "zone" => "asia-4",
                "languageCode" => "id"
            ],
            'getReferenceChecks' => [
                "locale" => "id-ID"
            ],
            'GetProfileInsights' => [
                "zone" => "asia-4",
                "locale" => "id-ID",
                "timezone" => "Asia/Jakarta"
            ],
            default => new \stdClass()
        };
        } else if ($client::provider == 'glints'){
            $vars = match($operationName) {
                'searchHierarchicalLocations' => [
                    // dua versi, pilih default yang paling lengkap
                    'searchTerm' => $variables['searchTerm'] ?? 'Bandung',
                    'limit' => $variables['limit'] ?? 9,
                    'levels' => $variables['levels'] ?? [1,2,3,4],
                    'countryCode' => $variables['countryCode'] ?? 'ID',
                    'withActiveJobsOnly' => $variables['withActiveJobsOnly'] ?? null,
                    'searchType' => $variables['searchType'] ?? 'SUGGESTION',
                ],
                'getJobDetailsById' => [
                    'opportunityId' => $variables['opportunityId'] ?? '',
                    'traceInfo' => $variables['traceInfo'] ?? '',
                    'source' => $variables['source'] ?? 'For You',
                ],
                'getCustomSlugPagesWithActiveJobs' => [
                    'countryCode' => $variables['countryCode'] ?? 'ID',
                ],
                'getEnabledFeatureFlags' => [
                    'deviceId' => $variables['deviceId'] ?? '',
                    'platform' => $variables['platform'] ?? 'DST',
                    'featureFlagNames' => $variables['featureFlagNames'] ?? [
                        "dstTWJobFilter","dstBuilderIntegration","dstFollowUpRequest","dstHideApplicationPageBanner","dstHideApplicationPageArchiveTitle","dstFollowerNotificationPreference","dstOdyssey","dstEnableMobileDownloadBanner","dstApplicationStatusTracking","dstImproveJobSearchFilter","dstEmptyJobSearchRedirectToFYPCTA","dstShowRecommendationCard","dstHideJobCategoryFilter","dstViewEducationLevelEnabled","dstChatApplicationStatusEnabled","dstWhatsappSupport","dstHideResumeSectionInApplicationPage","dstHideSkillsV2","dstExperimentJobDetailRevising","dstExperimentApplicationWarning","dstMigrationToApiV2","dstShowLoginPopup","dstExperimentSearchJobV3","dstExperimentExplorePageFilterOrder","dstEnableMobileDownloadModal","dstAirBridgeLinksEnabled","dstGlintsTracking","allExperimentApplyOnApp","dstNormalizedWorkExperience"
                    ],
                ],
                'jobHiringQuestion' => [
                    'jobId' => $variables['jobId'] ?? '',
                ],
                'getUserNotificationPreferenceCategories' => [],
                'JobRolePreferenceResolver' => [],
                'getMe' => [],
                'jobLocationPreferences' => [],
                'getHierarchicalLocationById' => [
                    'id' => $variables['id'] ?? '',
                ],
                'getApplications' => [
                    'data' => $variables['data'] ?? [
                        'limit' => 5,
                        'offset' => 0,
                        'orderType' => 'DESC',
                        // 'statuses' => [], // bisa diisi jika ada
                    ],
                ],
                default => new \stdClass()
            };
        } else {
        }
        return $vars;
    }
}