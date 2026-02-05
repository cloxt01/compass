<?php

namespace App\Clients;
use App\Support\DataHelper;
use App\Exceptions\UnknownOperation;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class JobstreetToken extends api
{
    const host = 'https://login.seek.com';

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
    
    public  function verify_otp($email, $code, $provider)
    {
        match($provider) {
            'jobstreet' => $payload = [
                "client_id" => config('compass.client_id'),
                "connection" => "email",
                "email" => $email,
                "verification_code" => $code,
            ],
            default => throw new UnknownOperation($provider)
        };
    
        $response = $this->api()->post(self::host . '/passwordless/verify', $payload);
        Log::info($response);
        Log::info(DataHelper::is_json($response));
        return DataHelper::is_json($response) ? false : true;
        }
    }