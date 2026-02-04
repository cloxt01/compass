<?php

namespace App\Clients;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class Token extends api
{
    const host = [
        'jobstreet' => 'https://login.seek.com',
        'glints' => null
    ];

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
    
    public function verify_otp(Request $request, $provider)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|string|max:20'
            ]);
        
        if ($provider === 'jobstreet'){
            $payload = [
                "client_id" => config('compass.client_id'),
                "connection" => "email",
                "email" => $request->input('email'),
                "verification_code" => $request->input('code'),
            ];
        } else {
            throw new UnknownOperation("Provider not supported: " . $provider);
        }
        
        $response = $this->api()->post(self::host[$provider] . '/passwordless/verify', $payload);
        return $response;
        }
    }