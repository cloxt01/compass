<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TrafficLogger
{
    public function handle(Request $request, Closure $next)
    {

        return $next($request);
    }
}
