<?php

namespace App\Services\Token;

use App\Clients\api;
use App\Infrastructure\Contracts\Platform\PlatformToken;
use App\Support\QueryHelper;

class JobstreetToken extends api implements PlatformToken
{

    protected string $host = 'https://login.seek.com';

    public function __construct()
    {
        parent::__construct();
        $this->headers = [
            'accept' => '*/*',
            'auth0-client' => config('compass.auth0_client'),
            'content-type' => 'application/json',
            'dnt' => '1',
            'priority' => 'u=1, i',
            'sec-ch-ua' => '"Not(A:Brand";v="8", "Chromium";v="144", "Google Chrome";v="144"',
            'sec-ch-ua-mobile' => '?0',
            'sec-ch-ua-connection' => '"Windows"',
            'sec-fetch-dest' => 'empty',
            'sec-fetch-mode' => 'cors',
            'sec-fetch-site' => 'same-origin',
            'user-agent' => config('compass.user_agent'),
        ];
    }

    public function refreshToken(string $refreshToken): ?array
    {
        $payload = [
            "client_id"             => config('compass.platforms.jobstreet.client_id'),
            "redirect_uri"          => "https://id.jobstreet.com/oauth/callback/",
            "initial_scope"         => "openid profile email offline_access",
            "JobseekerSessionId"    => QueryHelper::generateUUID(),
            "identity_sdk_version"  => "10.0.7",
            "refresh_href"          => "https://id.jobstreet.com/",
            "grant_type"            => "refresh_token",
            "refresh_token"         => $refreshToken
        ];
        $response = $this->post('/oauth/token', $payload);
        if($response['status'] != 'success' && !isset($response['data']['access_token'])){
            return null;
        }
        return $response['data'];
    }


}
