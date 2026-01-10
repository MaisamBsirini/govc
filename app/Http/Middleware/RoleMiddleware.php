<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user(); // ✅ THIS IS IMPORTANT

        if (!$user) {
            return response()->json(['message' => 'Not logged in'], 401);
        }

        if (!in_array($user->role, $roles)) {
            return response()->json(['message' => 'No permission'], 403);
        }

        return $next($request);
    }
}
