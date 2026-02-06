<?php

namespace App\Services\Jobstreet;

use App\Clients\JobstreetAPI;
use App\Services\JobstreetService;

class SearchJobs extends JobstreetService 
{
    public function __construct(JobstreetAPI $client)
    {
        parent::__construct($client);
    }

    public function search(){
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
        return $this->client->get($path, $params);
    }
    
}
