<?php

namespace App\Jobs;

use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Models\JobstreetAccount;
use App\Services\JobstreetToken;

class RefreshJobstreetToken implements ShouldQueue
{
    use Dispatchable, Queueable;

    public function __construct(
        public int $accountId
    ) {}

    public function handle(JobstreetToken $service)
    {
        try {
            $account = JobstreetAccount::find($this->accountId);
            Log::info($account);
            if (!$account || !$account->isExpired()) {
                return;
            }
            $token = $service->refreshToken($account);
            Log::info("[RefreshJobstreetToken] token response :". json_encode($token, JSON_UNESCAPED_SLASHES));
            if(!$token){
                $account->update([
                    'status' => 'reauth_required',
                ]);
                return;
            }
            $account->updateToken($token);
        } catch (\Throwable $e) {
            Log::error('RefreshJobstreetToken failed', [
                'account_id' => $this->accountId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
        
    }
}

