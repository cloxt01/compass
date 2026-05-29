<?php

namespace App\Services;

class JobDetails
{
    

    public static function fromJobstreet($raw){
        
        return [
            'metadata' => [
                'id' => $raw['details']['job']['id'] ?? 'Unknown',
                'title' => $raw['details']['job']['title'] ?? 'Unknown',
                'advertiser' => $raw['details']['job']['advertiser']['name'] ?? 'Unknown',
                'company' => $raw['details']['companyProfile']['name'] ?? 'Unknown',
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
    public static function fromGlints($raw){
        return [];
    }
}