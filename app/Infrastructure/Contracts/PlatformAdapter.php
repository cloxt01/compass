<?php
namespace App\Infrastructure\Contracts;

interface PlatformAdapter
{
    public function loadJob(string $jobId):array;
    public function loadProfile():array;
    public function buildPayload($data):array;
    public function canApply(array $details):array;
    public function execute(string $jobId ,array $payload, array $config = []):bool;
}
