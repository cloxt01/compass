<?php

namespace App\Services\Glints;

use App\Clients\GlintsAPI;
use App\Services\Adapters\GlintsAdapter;

class GlintsLocation extends GlintsAdapter
{
    protected ?array $data = null;

    public function __construct(GlintsAPI $client)
    {
        parent::__construct($client);
    }

    public function load(): array
    {
        if ($this->data === null) {

        }
        return $this->data ?? [];
    }
}
