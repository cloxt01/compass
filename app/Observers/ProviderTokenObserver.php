<?php

namespace App\Observers;

use App\Models\GlintsAccount;
use App\Models\JobstreetAccount;
use App\Services\Token\JobstreetToken;
use Illuminate\Support\Facades\Log;

class ProviderTokenObserver
{
    private JobstreetToken $service;
    public function __construct() {
        $this->service = new JobstreetToken();
    }
    /**
     * Dipanggil setiap kali model diambil dari database.
     */
    public function retrieved(GlintsAccount | JobstreetAccount $account)
    {
        Log::info("ProviderToken retrieved");
        if ($account->isExpired()) {
            Log::info("Token for account {$account->id} is expired. Refreshing token...");

            $account->updateStatus('expired');

            $token = $this->service->refreshToken($account->refresh_token);
            if (!$token) {
                Log::warning("Failed to refresh token for account {$account->id}. Please check the refresh token.");
                return;
            }
            $account->updateToken($token);
        }
    }
}
