<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureActiveSubscription
{
    public function handle(
        Request $request,
        Closure $next
    ): Response
    {
        $subscription = $request->user()
            ->subscriptions()
            ->latest()
            ->first();

        if (!$subscription) {
            return response()->json([
                'message' => 'Subscription not found.'
            ], 403);
        }

         if (in_array($subscription->status, [
            'active',
            'grace',
        ])) {
            return response()->json([
                'message' => 'Subscription is not active.'
            ], 403);
        }

        return $next($request);
    }
}
