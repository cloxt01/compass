<?php

namespace App\Services\Token;

use App\Clients\GlintsAPI;
use App\Infrastructure\Contracts\Platform\PlatformToken;
use App\Support\QueryHelper;
use App\Support\RequestHelper;
use Illuminate\Support\Facades\Log;

class GlintsToken extends GlintsAPI implements PlatformToken
{
    public function __construct()
    {
        parent::__construct();
        $this->headers = [
            'accept' => 'application/json, text/plain, */*',
            'content-type' => 'application/json',
            'dnt' => '1',
            'priority' => 'u=1, i',
            'origin' => 'https://glints.com',
            'sec-ch-ua' => '"Not(A:Brand";v="8", "Chromium";v="144", "Google Chrome";v="144"',
            'sec-ch-ua-mobile' => '?0',
            'sec-ch-ua-connection' => '"Windows"',
            'sec-fetch-dest' => 'empty',
            'sec-fetch-mode' => 'cors',
            'sec-fetch-site' => 'same-origin',
            'user-agent' => config('compass.user_agent'),
            'x-glints-country-code' => 'ID',
        ];
    }

    public  function getToken($email, $password): ?array
    {
        $payload = [
            'grant_type' => 'password',
            "client_id" => config('compass.platforms.glints.client_id'),
            "username" => $email,
            "password" => $password
        ];

        Log::info('Payload to Glints token : '.json_encode($payload));
        $response = $this->post('/oauth2/token', $payload);
        Log::info('Response to Glints token : '. json_encode($response));
        if(isset($response['data']['access_token'])){
            return [
                'access_token' => $response['data']['access_token'],
                'cookie' => isset($response['headers']['set-cookie'][0])
                    ? RequestHelper::parseSetCookieToCookieString($response['headers']['set-cookie'][0])
                    : null
            ];
        }
        return null;
    }
    public function refreshToken(string $refreshToken): ?array
    {
        $payload = [
            "client_id"             => config('compass.client_id'),
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
