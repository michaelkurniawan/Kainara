<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class AdminAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Jika pengguna sudah terautentikasi
        if (Auth::check()){
            // Dan pengguna adalah admin (asumsi ada method isAdmin() di model User Anda)
            if (Auth::user()->isAdmin()){
                // Lanjutkan request
                return $next($request);
            } else {
                // Jika bukan admin, logout dan redirect ke home dengan pesan error
                Auth::logout();
                return redirect()->route('home')->with('error', 'You do not have admin access.');
            }
        }

        // Jika pengguna belum terautentikasi, redirect ke halaman login admin
        return redirect()->route('admin.login')->with('error', 'Please login as admin to access this page.');
    }
}