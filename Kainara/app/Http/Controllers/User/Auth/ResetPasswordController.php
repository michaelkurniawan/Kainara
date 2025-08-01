<?php

namespace App\Http\Controllers\User\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use App\Models\User; // Ensure the User model is imported

class ResetPasswordController extends Controller
{
    /**
     * Display the password reset form.
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
     * Reset the given user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function reset(Request $request)
    {
        $request->validate($this->rules(), $this->validationErrorMessages());

        // Get the status of the password reset process
        $response = $this->broker()->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->password = Hash::make($password);
                $user->save();
            }
        );

        if ($response == Password::PASSWORD_RESET) {
            // Flash success notification
            session()->flash('notification', [
                'type' => 'success',
                'title' => 'Password Reset Successful!',
                'message' => 'Your password has been successfully reset. Please log in with your new password.',
                'hasActions' => false
            ]);
            return redirect()->route('login')->with('status', __($response));
        }

        // If reset fails
        throw ValidationException::withMessages([
            'email' => [trans($response)],
        ]);
    }

    /**
     * Get the password reset validation rules.
     *
     * @return array
     */
    protected function rules()
    {
        return [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed', // 'confirmed' requires a password_confirmation field
        ];
    }

    /**
     * Get the custom validation error messages for the password reset.
     *
     * @return array
     */
    protected function validationErrorMessages()
    {
        return [];
    }

    /**
     * Get the password broker used by the controller.
     *
     * @return \Illuminate\Contracts\Auth\Passwords\PasswordBroker
     */
    public function broker()
    {
        return Password::broker();
    }
}
