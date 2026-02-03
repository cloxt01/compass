<?php

namespace App\Services;

use App\Clients\JobstreetAPI;
use App\Clients\GlintsAPI;
use App\Support\QueryHelper;

class SearchJobs
{
    protected JobstreetAPI | GlintsAPI $client;

    public function __construct(GlintsAPI | JobstreetAPI $client)
    {
        $this->client = $client;
    }

    public function search(array $params = []): array
    {
        if ($this->client instanceof JobstreetAPI){
            $path = '/api/jobsearch/v5/me/search';
            $params = [
            'siteKey' => 'ID-Main',
            'sourcesystem' => 'houston',
            'eventCaptureSessionId' => $this->client->sessionId ?? '',
            'userid' => $this->client->sessionId ?? '',
            'usersessionid' => $this->client->sessionId ?? '',
            'page' => 1,
            'keywords' => $params['keyword'] ?? '',
            'where' => $params['location'] ?? '',
            'pageSize' => (int)($params['page_size'] ?? 32),
            'include' => 'seogptTargeting,relatedsearches',
            'locale' => 'id-ID',
            'source' => 'FE_SERP' ]; 
        } else {
            $path = '/api/jobsearch/v5/me/search';
            $params = [];
        }

        return $this->client->get($path, $params);
    }
}
