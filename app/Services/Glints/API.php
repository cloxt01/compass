<?php

namespace App\Services\Glints;

use App\Clients\GlintsAPI;

class API
{
    public function __construct(protected GlintsAPI $client){

    }

    public function search_location(string $keyword = '') :array {
        $this->data = $this->client->graphql('searchHierarchicalLocations', [
            'searchTerm' => $keyword,
            'limit' => 10,
            'levels' => [1,2,3,4]
        ]);
        return $this->data ?? [];
    }
}
