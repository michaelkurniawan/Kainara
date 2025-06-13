<?php

namespace App\Http\Middleware; // <-- PASTIKAN INI SAMA PERSIS

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware // <-- PASTIKAN NAMA KELAS INI SAMA PERSIS
{
    /**
     * Handle an incoming request.
     * Tangani permintaan masuk.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Periksa apakah pengguna sudah terautentikasi dan memiliki peran 'admin'
        if (auth()->check() && auth()->user()->role === 'admin') {
            return $next($request);
        }

        // Jika tidak, abaikan dengan error 403 (Unauthorized) atau redirect ke halaman login
        abort(403, 'Unauthorized action.'); // Atau redirect ke route('login')
    }
}
