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
        if (Auth::check()){
            if (Auth::user()->isAdmin()){
                return $next($request);
            } else {
                Auth::logout();
                return redirect()->route('home')->with('error', 'You do not have admin access.');
            }
        }

        return redirect()->route('admin.login')->with('error', 'Please login as admin to access this page.');
    }
}
