<?php

namespace App\Domain\Jobstreet;

use App\Clients\JobstreetAPI;

class ApplicationExecutor {
    protected JobstreetAPI $client;

    public function __construct(JobstreetAPI $client){
        $this->client = $client;
    }

    public function apply($payload): array {
        $response = $this->client->graphql('ApplySubmitApplication', $payload);
        if($response['ok'] && $response['data']['data']['submitApplication']['__typename'] === 'SubmitApplicationSuccess'){
            return ['success' => true];
        } else {
            return [
                'success' => false,
                'error' => $response['data'],
            ];
        }
    }
}