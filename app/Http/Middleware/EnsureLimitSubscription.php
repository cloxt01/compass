<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureLimitSubscription
{
    public function handle(
        Request $request,
        Closure $next
    ): Response
    {
        $limit = $request->user()
            ->getLastActiveSubscription()?->isLimit();

        if ($limit) {
            return response()->json([
                'message' => 'Penggunaan sudah mencapai batas limit.'
            ], 403);
        }

        return $next($request);
    }
}
