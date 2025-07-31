<?php

namespace App\Http\Controllers\User\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use App\Providers\RouteServiceProvider;
use App\Models\User; // Pastikan model User diimpor
use Illuminate\Support\Facades\URL; // Untuk membuat URL yang ditandatangani
use Illuminate\Auth\Notifications\VerifyEmail; // Notifikasi verifikasi email bawaan Laravel

class EmailVerificationController extends Controller // Nama kelas sesuai yang Anda gunakan
{
    /**
     * Where to redirect users after verification.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth')->except('verify', 'show', 'resend');
        $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }

    /**
     * Tampilkan notifikasi verifikasi email.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        // Periksa apakah pengguna sudah terverifikasi secara manual
        if (!is_null($request->user()->email_verified_at)) {
            return redirect($this->redirectPath());
        }

        return view('auth.verify-email');
    }

    /**
     * Tandai email pengguna yang diberikan sebagai terverifikasi.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function verify(Request $request)
    {
        // Temukan pengguna berdasarkan ID dari URL
        $user = User::findOrFail($request->route('id'));

        // Pastikan hash URL valid dan cocok dengan pengguna
        $expectedHash = sha1($user->email); // Mengambil email langsung dari objek user
        $providedHash = (string) $request->route('hash');

        if (! hash_equals($providedHash, $expectedHash)) {
            throw new \Illuminate\Auth\Access\AuthorizationException('Invalid verification hash.');
        }

        // Periksa apakah pengguna sudah diverifikasi (hasVerifiedEmail() diimplementasikan di sini)
        if (!is_null($user->email_verified_at)) {
            // Jika sudah diverifikasi, sekalian flash notifikasi bahwa sudah diverifikasi
            session()->flash('notification', [
                'type' => 'info', // Atau 'success'
                'title' => 'Verifikasi Email',
                'message' => 'Email Anda sudah diverifikasi sebelumnya.',
                'hasActions' => true // Diubah menjadi TRUE untuk tombol OK
            ]);
            return redirect()->route('login');
        }

        // Tandai email sebagai terverifikasi (markEmailAsVerified() diimplementasikan di sini)
        $user->email_verified_at = Carbon::now();
        $verified = $user->save(); // Simpan perubahan ke database

        if ($verified) {
            event(new Verified($user)); // Pemicu event Verified

            // Flash notifikasi kustom setelah verifikasi berhasil
            session()->flash('notification', [
                'type' => 'success',
                'title' => 'Verifikasi Berhasil!',
                'message' => 'Email Anda berhasil diverifikasi. Silakan masuk ke akun Anda.',
                'hasActions' => true // Diubah menjadi TRUE untuk tombol OK
            ]);
        } else {
            // Flash notifikasi jika penyimpanan gagal
            session()->flash('notification', [
                'type' => 'error',
                'title' => 'Verifikasi Gagal',
                'message' => 'Terjadi kesalahan saat memverifikasi email Anda. Silakan coba lagi.',
                'hasActions' => true // Diubah menjadi TRUE untuk tombol OK
            ]);
        }

        // PENTING: Logout pengguna jika mereka secara otomatis login oleh proses verifikasi.
        // Ini untuk mencegah login otomatis setelah verifikasi.
        if (Auth::check()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        // Arahkan ke halaman login
        return redirect()->route('login');
    }

    /**
     * Kirim ulang notifikasi verifikasi email.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function resend(Request $request)
    {
        // Periksa apakah pengguna sudah diverifikasi
        if (!is_null($request->user()->email_verified_at)) {
            return redirect($this->redirectPath());
        }

        // Kirim notifikasi verifikasi email (sendEmailVerificationNotification() diimplementasikan di sini)
        $user = $request->user();
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(config('auth.verification.expire', 60)),
            [
                'id' => $user->getKey(),
                'hash' => sha1($user->email), // Menggunakan email langsung dari objek user
            ]
        );

        // Menggunakan notifikasi verifikasi email bawaan Laravel
        // Metode notify() disediakan oleh trait Illuminate\Notifications\Notifiable di model User
        $user->notify(new VerifyEmail($verificationUrl)); // Pastikan VerifyEmail menerima URL

        session()->flash('notification', [
            'type' => 'info',
            'title' => 'Email Verifikasi Dikirim Ulang',
            'message' => 'Tautan verifikasi baru telah dikirim ke alamat email Anda.',
            'hasActions' => true // Diubah menjadi TRUE untuk tombol OK
        ]);

        return back();
    }

    /**
     * Get the redirect path for successful verification.
     *
     * @return string
     */
    protected function redirectPath()
    {
        return property_exists($this, 'redirectTo') ? $this->redirectTo : '/home';
    }
}
