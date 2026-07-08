<?php

namespace App\Services\Platform\Glints;

use App\Clients\GlintsAPI;
use App\Services\Adapters\GlintsAdapter;


class GlintsProfile extends GlintsAdapter
{
    protected $data = null;

    public function __construct(GlintsAPI $client)
    {
        parent::__construct($client);
    }

    public function load(): array
    {
        if ($this->data === null) {
            $this->data = $this->client->get('/v2/me');
        }
        return ($this->data && isset($this->data['data']['data']))  ? $this->data['data']['data'] : [];
    }

    public function get_resumes(): ?string
    {
        return $this->load()['data']['resume'] ?? null;
    }

    public function get_skills(): array
    {
        return $this->load()['data']['skils']?? [];
    }

    public function get_higest_education(): ?string
    {
        return $this->load()['data']['highestEducationLevel'] ?? [];
    }
}
