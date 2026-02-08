<?php

namespace App\Domain\Jobstreet;

use App\Clients\JobstreetAPI;

class JobDetailsReader {

    protected JobstreetAPI $client;
    protected array $raw;

    public function __construct(JobstreetAPI $client) {
        $this->client = $client;
    }

    public function load(string $jobId): bool {
        $this->raw = $this->client->graphql('jobDetailsWithPersonalised', ['jobId' => $jobId])['data']['data']['jobDetails'];
        return $this->raw ? true : false;
    }

    public function metadata(): array {
        return [
            'id' => $this->raw['job']['id'],
            'title' => $this->raw['job']['title'],
            'advertiser' => $this->raw['job']['advertiser']['name'] ?? 'Unknown',
            'company' => $this->raw['companyProfile']['name'] ?? 'Unknown',
            'location' => $this->raw['job']['location']['label'] ?? 'Unknown'
        ];
    }

    public function eligibility(): array {
        return [
            'expired' => $this->raw['job']['isExpired'] ?? false,
            'linkout' => $this->raw['job']['isLinkOut'] ?? false,
            'applied' => $this->raw['personalised']['appliedDateTime'] ?? false
        ];
    }

    public function insight(): array {
        return [
            'applicantsCount' => $this->raw['insights'][0]['count'] ?? 0
        ];
    
    }

    public function products(): array {
        return [
            'questionnaire' => $this->raw['products']['questionnaire'] ?? []
        ];
    }
}