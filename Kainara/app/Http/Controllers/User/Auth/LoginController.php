<?php

namespace App\Http\Controllers\User\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use App\Models\User;

class LoginController extends Controller
{
    /**
     * Show the application's login form.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
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

        // 2. Mencoba proses autentikasi (tanpa login langsung)
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();

            // --- LOGIKA VERIFIKASI EMAIL DIMULAI DI SINI ---
            if (!$user->hasVerifiedEmail()) {
                Auth::logout(); // Logout user yang belum verifikasi
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                // Tambahkan pesan error ke session dengan format kustom kamu
                return back()->with('notification', [
                    'type' => 'error', // Sesuaikan dengan jenis notifikasi error di komponenmu (misalnya 'danger')
                    'title' => 'Login Gagal!',
                    'message' => 'Akun Anda belum diverifikasi. Silakan periksa email Anda untuk tautan verifikasi.',
                    'hasActions' => true // Notifikasi akan memiliki tombol 'Okay'
                ])->onlyInput('email');
            }
            // --- LOGIKA VERIFIKASI EMAIL BERAKHIR DI SINI ---

            $request->session()->regenerate();

            if ($user) {
                $user->last_login = Carbon::now();
                $user->save();
            }

            return redirect()->intended('/');
        }

        // 3. Jika autentikasi gagal
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
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}