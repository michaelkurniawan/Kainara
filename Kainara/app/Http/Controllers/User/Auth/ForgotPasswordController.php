<?php

namespace App\Http\Controllers\User\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password; // Untuk fitur reset password Laravel
use Illuminate\Validation\ValidationException; // Untuk menangani validasi
use App\Models\User; // Pastikan model User diimpor

class ForgotPasswordController extends Controller
{
    /**
     * Tampilkan form untuk meminta reset password.
     *
     * @return \Illuminate\View\View
     */
    public function showLinkRequestForm()
    {
        return view('auth.passwords.email'); // View untuk form permintaan email
    }

    /**
     * Kirim tautan reset password ke alamat email yang diberikan.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        // Dapatkan status dari proses pengiriman link reset password
        $response = $this->broker()->sendResetLink(
            $request->only('email')
        );

        if ($response == Password::RESET_LINK_SENT) {
            // Flash notifikasi sukses
            session()->flash('notification', [
                'type' => 'success',
                'title' => 'Link Reset Terkirim!',
                'message' => 'Tautan reset password telah dikirim ke alamat email Anda.',
                'hasActions' => false
            ]);
            return back()->with('status', __($response));
        }

        // Jika pengiriman gagal, kembalikan error
        throw ValidationException::withMessages([
            'email' => [trans($response)],
        ]);
    }

    /**
     * Dapatkan broker password yang digunakan oleh controller.
     *
     * @return \Illuminate\Contracts\Auth\Passwords\PasswordBroker
     */
    public function broker()
    {
        return Password::broker();
    }
}
