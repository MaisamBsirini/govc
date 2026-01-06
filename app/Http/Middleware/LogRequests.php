<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LogRequests
{
    public function handle(Request $request, Closure $next)
    {
        Log::info("Request: {$request->method()} {$request->path()}");

        $response = $next($request);

        Log::info("Response Status: " . $response->status());

        return $response;
    }
}
