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
            $review = $this->client->graphql('ReviewPage')['data'] ?? [];
            $document = $this->client->graphql('DocumentsQuery')['data'] ?? [];

            print_r("----- REVIEW -----");
            print_r($review);
            print_r("----- DOCUMENT -----");
            print_r($document);

            if (!isset($review['data']['viewer']) && !isset($document['data']['viewer'])) {
                return [];
            }
            $this->data = array_merge($review, $document);
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