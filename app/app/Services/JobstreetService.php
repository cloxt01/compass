<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

use App\Clients\JobstreetAPI;
use App\Services\Jobstreet\ReviewPage;
use App\Services\Jobstreet\Documents;
use App\Services\Jobsstreet\AppliedJobs;
use App\Services\Jobstreet\SearchJobs;

class JobstreetService extends Service
{
    protected $client;
    protected ReviewPage $review;
    protected Documents $document;
    protected AppliedJobs $applied_jobs;
    protected SearchJobs $search_jobs;


    public function __construct(JobstreetAPI $client)
    {
        parent::__construct($client);

    }

    public function review()
    {
        return new ReviewPage($this->client);
    }
    public function documents()
    {
        return new Documents($this->client);
    }
    public function applied_jobs()
    {
        return new AppliedJobs($this->client);
    }
    public function search_jobs()
    {
        return new SearchJobs($this->client);
    }
    
}