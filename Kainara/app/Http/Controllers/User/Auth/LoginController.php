<?php

namespace App\Http\Controllers\User\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Penting untuk fasilitas autentikasi Laravel

class LoginController extends Controller
{
    /**
     * Show the application's login form.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        // Pastikan view ini ada di resources/views/auth/login.blade.php
        return view('auth.login');
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request)
    {
        // 1. Validasi input
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        // 2. Mencoba proses autentikasi
        // Auth::attempt() akan mencoba menemukan user berdasarkan kredensial
        // dan memverifikasi password. Jika berhasil, user akan di-login.
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials, $request->boolean('remember'))) { // 'remember' untuk fungsionalitas remember me
            $request->session()->regenerate();

            // Redirect ke halaman yang dituju setelah login
            // Anda bisa mengubahnya ke 'home' atau rute lain yang Anda inginkan
            return redirect()->intended('/'); // intended akan mengarahkan user ke URL yang ingin mereka akses sebelum login
        }

        // 3. Jika autentikasi gagal
        // Mengembalikan ke halaman login dengan error input
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        Auth::logout(); // Logout user

        $request->session()->invalidate(); // Invalidasi sesi
        $request->session()->regenerateToken(); // Regenerasi token CSRF

        return redirect('/'); // Redirect ke halaman utama setelah logout
    }
}