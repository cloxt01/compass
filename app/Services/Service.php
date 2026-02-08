<?php

namespace App\Services;

use App\Clients\JobstreetAPI;
use App\Clients\GlintsAPI;

abstract class Service
{
    protected $client;

    public function __construct(JobstreetAPI | GlintsAPI $client)
    {
        $this->client = $client;
    }
}