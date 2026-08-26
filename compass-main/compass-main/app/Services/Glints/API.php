<?php

namespace App\Services\Glints;

use App\Clients\GlintsAPI;

class API
{
    public array $data;
    public function __construct(protected GlintsAPI $client){
    }

    public function search_location(string $keyword = '') :array {
        return $this->client->graphql('searchHierarchicalLocations', [
            'searchTerm' => $keyword,
            'limit' => 5,
            'levels' => [2,3]
        ]) ?? [];
    }
}
