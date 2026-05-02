<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class KaderOnly
{
    /**
     * Handle an incoming request.
     * Middleware ini memastikan hanya kader (admin) yang bisa akses website
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu');
        }

        $user = auth()->user();
        
        // Hanya kader yang bisa akses website
        if ($user->role !== 'kader') {
            auth()->logout();
            return redirect()->route('login')->with('error', 'Akses ditolak. Website ini hanya untuk Kader/Admin. Orangtua silakan gunakan aplikasi mobile.');
        }

        return $next($request);
    }
}