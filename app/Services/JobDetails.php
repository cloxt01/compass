<?php

namespace App\Services;

class JobDetails
{
    

    public static function fromJobstreet($raw){
        return [
            'metadata' => [
                'id' => $raw['job']['id'],
                'title' => $raw['job']['title'],
                'advertiser' => $raw['job']['advertiser']['name'] ?? 'Unknown',
                'company' => $raw['companyProfile']['name'] ?? 'Unknown',
                'location' => $raw['job']['location']['label'] ?? 'Unknown'
            ] ?? [],
            'eligibility' => [
                'expired' => $raw['job']['isExpired'] ?? false,
                'linkout' => $raw['job']['isLinkOut'] ?? false,
                'applied' => $raw['personalised']['appliedDateTime'] ?? false
            ] ?? [],
            'insights' => [
                'applicantsCount' => $raw['insights'][0]['count'] ?? 0
            ] ?? [],
            'products' => [
                'questionnaire' => $raw['job']['questionnaire'] ?? []
            ] ?? []
        ];
    }
    public static function fromGlints($raw){
        return [];
    }
}