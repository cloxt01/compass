<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

use App\Clients\JobstreetAPI;
use App\Services\Jobstreet\ReviewPage;
use App\Services\Jobstreet\Documents;
use App\Services\Jobstreet\Job;

class JobstreetService extends Service
{
    protected $client;
    protected ReviewPage $review;
    protected Documents $document;
    protected AppliedJobs $applied_jobs;
    protected Job $job;


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
    public function job()
    {
        return new Job($this->client);
    }
    
}