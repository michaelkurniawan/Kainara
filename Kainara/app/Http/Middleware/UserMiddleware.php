<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UserMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            if (Auth::user()->isAdmin()) {
                return redirect()->route('admin.dashboard')->with('info', 'Anda telah dialihkan ke dashboard admin.');
            }
            elseif (Auth::user()->isUser()) {
                return $next($request);
            }
            else {
                Auth::logout();
                return redirect()->route('login')->with('error', 'Akses tidak sah. Silakan login dengan kredensial yang sesuai.');
            }
        }
        return redirect()->route('login')->with('error', 'Anda harus login untuk mengakses halaman ini.');
    }
}