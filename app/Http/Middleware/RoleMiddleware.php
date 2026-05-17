<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string  ...$roles
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();

        if (!$user) {
            return $request->expectsJson() 
                ? response()->json(['message' => 'Unauthenticated'], 401)
                : redirect()->route('login');
        }

        if (!in_array($user->role, $roles)) {
            return $request->expectsJson()
                ? response()->json([
                    'message' => 'Unauthorized. Required roles: ' . implode(', ', $roles),
                    'user_role' => $user->role
                ], 403)
                : abort(403, 'Unauthorized access');
        }

        return $next($request);
    }
}