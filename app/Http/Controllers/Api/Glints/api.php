<?php

namespace App\Http\Controllers\Api\Glints;

use App\Clients\GlintsAPI;

class api
{
    private $provider;

    public function __construct(protected GlintsAPI $client){}

    public function searchLocation($keyword){
        $response = $this->client->graphql('searchHierarchicalLocations', [
            'searchTerm' => $keyword,
            'limit' => 10,
            'levels' => [1,2,3,4]
        ]);
        return $response ?? [];
    }
}
