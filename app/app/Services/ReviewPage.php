<?php

namespace App\Services\Jobstreet;

use App\Clients\JobstreetAPI;

class ReviewPage
{
    protected JobstreetAPI $client;

    public function __construct(JobstreetAPI $client)
    {
        $this->client = $client;
    }

    public function get_review_page(): array
    {
        return $this->client->graphql('ReviewPage');
    }
    
    public function get_latest_roles(): array
    {
        return $this->get_review_page()['data']['viewer']['roles'][0] ?? [];
    }

    public function get_skills(): array
    {
        return $this->get_review_page()['data']['viewer']['skills'] ?? [];
    }

    public function get_profile_visibility(): array
    {
        return [
            'profileVisibility' => $this->get_review_page()['data']['viewer']['profileVisibility']['level'],
            'profileVisibility2' => $this->get_review_page()['data']['viewer']['profileVisibility2']['id'],
        ];
    }
    public function get_qualifications(): array
    {
        return $this->get_review_page()['data']['viewer']['qualifications'] ?? [];
    }
    public function get_reference_checks(): array
    {
        return $this->get_review_page()['data']['viewer']['referenceChecks'] ?? [];
    }
}