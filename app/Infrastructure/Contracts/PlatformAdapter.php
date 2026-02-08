<?php
namespace App\Infrastructure\Contracts;

interface PlatformAdapter
{
    public function loadJob(string $jobId):array;
    public function loadProfile():array;
    public function buildPayload(array $jobDetails, array $profileDetails):array;
    public function canApply(array $details):array;
    public function execute(array $payload):bool;
}