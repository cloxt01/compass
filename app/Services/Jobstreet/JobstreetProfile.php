<?php

namespace App\Services\Jobstreet;

use App\Clients\JobstreetAPI;
use App\Services\Adapters\JobstreetAdapter;


class JobstreetProfile extends JobstreetAdapter
{
    protected $data = null;

    public function __construct(JobstreetAPI $client)
    {
        parent::__construct($client);

    }

    public function load(): array
    {
        if ($this->data === null) {
            $review = $this->client->graphql('ReviewPage')['data']['data']['viewer'] ?? [];
            $document = $this->client->graphql('DocumentsQuery')['data']['data']['viewer'] ?? [];

            
            
            $this->data = array_merge(["review" => $review], ["document" => $document]);
        }
        return $this->data ?? [];
    }

    public function get_resumes(): array
    {
        return $this->load()['data']['viewer']['resumes'] ?? [];
    }

    public function get_roles(): array
    {
        return $this->load()['data']['viewer']['roles'] ?? [];
    }

    public function get_latest_resume(): array
    {

        return end($this->load()['data']['viewer']['resumes']) ?? [];
    }
    public function get_latest_roles(): array
    {
        return $this->load()['data']['viewer']['roles'][0] ?? [];
    }

    public function get_skills(): array
    {
        return $this->load()['data']['viewer']['skills'] ?? [];
    }

    public function get_profile_visibility(): array
    {
        return [
            'profileVisibility' => $this->load()['data']['viewer']['profileVisibility']['level'],
            'profileVisibility2' => $this->load()['data']['viewer']['profileVisibility2']['id'],
        ];
    }
    public function get_qualifications(): array
    {
        return $this->load()['data']['viewer']['qualifications'] ?? [];
    }
    public function get_reference_checks(): array
    {
        return $this->load()['data']['viewer']['referenceChecks'] ?? [];
    }

    
}