<?php

namespace App\Clients;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;
use App\Infrastructure\Session\DbSessionProvider;
use App\Infrastructure\Token\DbTokenProvider;

class TokenAPI
{
    const host = [
        'jobsreet' => 'https://login.seek.com',
        'glints' => 'https://www.glints.com'
    ];
    public function api()
    {
        return Http::withHeaders([
                'Content-Type'  => 'application/json',
                'Accept'        => '*/*',
                'Auth0-Client'  => config('jobooster.auth0_client'),
                'Origin'        => 'https://id.jobstreet.com',
                'Referer'       => 'https://id.jobstreet.com/',
                'User-Agent'    => config('jobooster.user_agent'),
            ])
        ->acceptJson()
        ->timeout(20)
        ->connectTimeout(10)
        ->withOptions(['verify' => false]);
    }

    public function get_token()
    {
        {
            try {
                $response = $this->api()->post(self::host['jobsreet']. '/oauth/token', [
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

}
