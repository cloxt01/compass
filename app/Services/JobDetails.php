<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class JobDetails
{


    public static function fromJobstreet($raw){

        return [
            'metadata' => [
                'id' => $raw['details']['job']['id'] ?? 'Unknown',
                'title' => $raw['details']['job']['title'] ?? 'Unknown',
                'company' => $raw['details']['job']['advertiser']['name'] ?? 'Unknown',
//                'advertiser' => $raw['details']['job']['advertiser']['name'] ?? 'Unknown',
//                'company' => $raw['details']['companyProfile']['name'] ?? 'Unknown',
                'location' => $raw['details']['job']['location']['label'] ?? 'Unknown'
            ] ?? [],
            'eligibility' => [
                'expired' => $raw['details']['job']['isExpired'] ?? false,
                'linkout' => $raw['details']['job']['isLinkOut'] ?? false,
                'applied' => empty($raw['details']['personalised']['appliedDateTime']) ? false : true
            ] ?? [],
            'insights' => [
                'applicantsCount' => $raw['details']['insights'][0]['count'] ?? 0
            ] ?? [],
            'products' => [
                'questionnaire' => $raw['process']['questionnaire'] ?? []
            ] ?? []
        ];
    }
    public static function fromGlints($raw)
    {
        if(empty($raw))
            return [];
        Log::info(json_encode($raw));
        return [
            'metadata' => [
                'id' => $raw['details']['id'] ?? '',
                'title' => $raw['details']['title'] ?? 'Unknown',
                'company' => $raw['details']['company']['name'] ?? 'Unknown',
                'location' => $raw['details']['location']['formattedName'] ?? 'Unknown',
            ],
            'eligibility' => [
                'linkout' => $raw['details']['job']['externalApplyURL'] ?? false,
                'expired' => (empty($raw['details']['expiryDate']) && empty($raw['details']['closedAt'])) ? null : (strtotime($raw['details']['expiryDate'] ?? '') < time() || !empty($raw['details']['closedAt'])),
                'closed' => $raw['details']['status'] === 'CLOSED' ? true : false,
                'applied' => $raw['details']['isApplied'] ?? false,
            ],
            'insights' => [
                'isActivelyHiring' => $raw['details']['isActivelyHiring'] ?? false,
                'isHot' => $raw['details']['isHot'] ?? false,
                'creatorResponseRate' => $raw['details']['creatorResponseRate'] ?? null,
                'creatorResponseTime' => $raw['details']['creatorResponseTime'] ?? null,
                'isHighResponseRate' => $raw['details']['isHighResponseRate'] ?? false,
            ],
            'products' => [
                'questionnaire' => $raw['hiring_question']['employerScreeningQuestions'] ?? []
            ]
        ];
//       return $raw;

//        return $raw['screeningQuestionsEnabled'] ? [true] : [false];
    }
}
