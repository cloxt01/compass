<?php

namespace App\Services;

use App\Clients\api;
use App\Support\DataHelper;
use App\Support\QueryHelper;
use App\Exceptions\UnknownOperation;
use App\Models\JobstreetAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class JobstreetToken extends api
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
            'sec-ch-ua-platform' => '"Windows"',
            'sec-fetch-dest' => 'empty',
            'sec-fetch-mode' => 'cors',
            'sec-fetch-site' => 'same-origin',
            'user-agent' => config('compass.user_agent'),
        ];
    }
    
    public  function verify_otp($email, $code)
    {
        $payload = [
            "client_id" => config('compass.client_id'),
            "connection" => "email",
            "email" => $email,
            "verification_code" => $code,
        ];
    
        $response = $this->post('/passwordless/verify', $payload);

        switch($response['http_code']){
            case 200:
                return 'verified';
            case 400:
                return 'unverified';
            case 429:
                return 'blocked';
            default:
                return 'failed';
        }
        
    }
    public function refreshToken(JobstreetAccount $account): array | null
    {
        $payload = [
            "client_id"             => config('compass.client_id'),
            "redirect_uri"          => "https://id.jobstreet.com/oauth/callback/",
            "initial_scope"         => "openid profile email offline_access",
            "JobseekerSessionId"    => QueryHelper::generateUUID(),
            "identity_sdk_version"  => "10.0.7",
            "refresh_href"          => "https://id.jobstreet.com/",
            "grant_type"            => "refresh_token",
            "refresh_token"         => $account->refresh_token
        ];
        $response = $this->post('/oauth/token', $payload);
        if($response['status'] != 'success' && !isset($response['data']['access_token'])){
            return null;
        }
        return $response['data'];
    }
     

}