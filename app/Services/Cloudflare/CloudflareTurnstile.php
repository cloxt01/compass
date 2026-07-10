<?php

namespace App\Services\Cloudflare;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CloudflareTurnstile
{

    private string $site_key;
    private string $secret_key;
    public function __construct()
    {
        $this->site_key = config('services.turnstile.site_key');
        $this->secret_key = config('services.turnstile.secret_key_key');
    }

    public static function verify(Request $request): bool
    {
        $token = $request->input('cf-turnstile-response');

        if(blank($token)) {
            return false;
        }

        $response = Http::asForm()->post(
            'https://challenges.cloudflare.com/turnstile/v0/siteverify',
            [
                'secret' => config('services.turnstile.secret_key'),
                'response' => $token,
                'remoteip' => $request->ip(),
            ]
        );

        if (! $response->json('success')) {
            return false;
        }
        return true;
    }
}
