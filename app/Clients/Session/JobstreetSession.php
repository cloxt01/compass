<?php

namespace App\Clients;

use App\Applications\Contracts\SessionProvider;
use App\Applications\Contracts\TokenProvider;
use App\Clients\TokenAPI;
use App\Clients\JobstreetAPI;


class JobstreetSession {

    protected SessionProvider $sessionProvider;
    protected TokenProvider $tokenProvider;

    public function __construct(SessionProvider $sessionProvider, TokenProvider $tokenProvider)
    {
        $this->api = new TokenAPI();
        $this->sessionProvider = $sessionProvider;
        $this->tokenProvider = $tokenProvider;
    }
    
    public function get_token()
    {
        {
            try {
                $response = $this->api->api()->post($this->api::BASE_URL, [
                    "client_id"             => config('jobooster.client_id'),
                    "redirect_uri"          => "https://id.jobstreet.com/oauth/callback/",
                    "initial_scope"         => "openid profile email offline_access",
                    "JobseekerSessionId"    => $this->sessionProvider->getSessionId($this->api::SERVICE),
                    "identity_sdk_version"  => "10.0.7",
                    "refresh_href"          => "https://id.jobstreet.com/",
                    "grant_type"            => "refresh_token",
                    "refresh_token"         => $this->tokenProvider->getRefreshToken($this->api::SERVICE)
                ]);

                if ($response->failed()) {
                    return [
                        'status' => 'error',
                        'http_code' => $response->status(),
                        'data' => $response->json() ?? $response->body()
                    ];
                }

                return [
                    'status' => 'success',
                    'http_code' => $response->status(),
                    'data' => $response->json()
                ];
            } catch (RequestException $e) {
                return [
                    'status' => 'error',
                    'http_code' => 500,
                    'message' => $e->getMessage()
                ];
            }
        }
    }

    public function get_session(){
        $api = new JobstreetAPI(
            $this->tokenProvider->getAccessToken(),
            $this->sessionProvider->getSessionId($this->api::SERVICE)
            );
        $resp = $api->graphql('sendLoginCallbackEvent', [], ['cookies' => true]);
        
        if($resp['ok']) {
            $cookies = $resp['cookies'];
            $session = $cookies->getCookieByName('JobseekerSessionId')->getValue();
            return [
                'status' => 'success',
                'http_code' => 200,
                'session_id' => $session ?? null,
                'data' => $resp['data']
            ];
        } else {
            return [
                'status' => 'error',
                'http_code' => 500,
                'session_id' => null,
                'data' => $resp['data'] ?? null,
            ];
        }
    }
    
}