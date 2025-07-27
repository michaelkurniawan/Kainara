<?php

namespace App\Http\Controllers\User\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use App\Models\User; // Pastikan model User diimpor

class ResetPasswordController extends Controller
{
    /**
     * Tampilkan form reset password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function showResetForm(Request $request)
    {
        return view('auth.passwords.reset')->with(
            ['token' => $request->route('token'), 'email' => $request->email]
        );
    }

    /**
     * Reset password pengguna yang diberikan.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function reset(Request $request)
    {
        $request->validate($this->rules(), $this->validationErrorMessages());

        // Dapatkan status dari proses reset password
        $response = $this->broker()->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->password = Hash::make($password);
                $user->save();
            }
        );

        if ($response == Password::PASSWORD_RESET) {
            // Flash notifikasi sukses
            session()->flash('notification', [
                'type' => 'success',
                'title' => 'Reset Password Berhasil!',
                'message' => 'Password Anda telah berhasil direset. Silakan login dengan password baru Anda.',
                'hasActions' => false
            ]);
            return redirect()->route('login')->with('status', __($response));
        }

        // Jika reset gagal
        throw ValidationException::withMessages([
            'email' => [trans($response)],
        ]);
    }

    /**
     * Dapatkan aturan validasi untuk reset password.
     *
     * @return array
     */
    protected function rules()
    {
        return [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed', // 'confirmed' membutuhkan field password_confirmation
        ];
    }

    /**
     * Dapatkan pesan error validasi kustom untuk reset password.
     *
     * @return array
     */
    protected function validationErrorMessages()
    {
        return [];
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
