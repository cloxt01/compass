<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class RefreshToken
{
    public function handle($request, $next)
    {
        Log::info("RefreshToken Middleware executed");
        $user = auth()->user();
        $account = $user->jobstreetAccount;
        if ($account && $account->isExpired() && $account->status === 'active') {
            $account->refreshToken();
        }
        return $next($request);
    }
}
