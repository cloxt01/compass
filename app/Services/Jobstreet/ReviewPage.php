<?php

namespace App\Services\Jobstreet;

use App\Clients\JobstreetAPI;
use App\Services\JobstreetService;


class ReviewPage extends JobstreetService
{
    protected $data = null;

    public function __construct(JobstreetAPI $client)
    {
        parent::__construct($client);

    }

    public function load(): array
    {
        if ($this->data === null) {
            $this->data = $this->client->graphql('ReviewPage')['data'];
        }
        return $this->data;
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