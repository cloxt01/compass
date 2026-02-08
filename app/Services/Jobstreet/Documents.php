<?php

namespace App\Services\Jobstreet;

use App\Clients\JobstreetAPI;
use App\Services\JobstreetService;

class Documents extends JobstreetService
{
    protected ?array $data = null;

    public function __construct(JobstreetAPI $client)
    {
        parent::__construct($client);
    }

    public function load(): array
    {
        if($this->data){
            return $this->data;
        }
        $this->data = $this->client->graphql('DocumentsQuery')['data'];
        return $this->data;
    }

    public function get_resumes(): array
    {
        return $this->load()['data']['viewer']['resumes'] ?? [];
    }

    public function get_latest_resume(): array
    {

        return end($this->load()['data']['viewer']['resumes']) ?? [];
    }
}
