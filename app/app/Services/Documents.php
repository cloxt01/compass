<?php

namespace App\Services\Jobstreet;

use App\Clients\JobstreetAPI;

class Documents
{
    protected JobstreetAPI $client;

    public function __construct(JobstreetAPI $client)
    {
        $this->documents = $client->graphql('DocumentsQuery');
    }

    public function get_documents(): array
    {
        return $this->documents;
    }

    public function get_resumes(): array
    {
        return $this->documents['data']['viewer']['resumes'] ?? [];
    }

    public function get_latest_resume(): array
    {

        return end($this->documents['data']['viewer']['resumes']) ?? [];
    }
}
