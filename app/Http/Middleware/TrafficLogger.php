<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TrafficLogger
{
    public function handle(Request $request, Closure $next)
    {
        Log::channel('traffic')->info('HTTP Request', [
            'time' => now()->toDateTimeString(),

            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'path' => $request->path(),

            'remote_addr' => $request->server('REMOTE_ADDR'),
            'ip' => $request->ip(),
            'ips' => $request->ips(),

            'cf_connecting_ip' => $request->header('CF-Connecting-IP'),
            'x_forwarded_for' => $request->header('X-Forwarded-For'),
            'x_real_ip' => $request->header('X-Real-IP'),

            'host' => $request->getHost(),
            'user_agent' => $request->userAgent(),
            'referer' => $request->headers->get('referer'),

            'query' => $request->query(),

            'user_id' => auth()->id(),

            'headers' => $request->headers->all(),
        ]);

        return $next($request);
    }
}