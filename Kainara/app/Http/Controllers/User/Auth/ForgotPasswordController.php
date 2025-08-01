<?php

namespace App\Http\Controllers\User\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password; // For Laravel's password reset feature
use Illuminate\Validation\ValidationException; // For handling validation exceptions
use App\Models\User; // Ensure the User model is imported

class ForgotPasswordController extends Controller
{
    /**
     * Display the form to request a password reset link.
     *
     * @return \Illuminate\View\View
     */
    public function showLinkRequestForm()
    {
        return view('auth.passwords.email'); // View for the email request form
    }

    /**
     * Send a password reset link to the given email address.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        // Get the status of the password reset link sending process
        $response = $this->broker()->sendResetLink(
            $request->only('email')
        );

        if ($response == Password::RESET_LINK_SENT) {
            // Flash success notification
            session()->flash('notification', [
                'type' => 'success',
                'title' => 'Reset Link Sent!',
                'message' => 'A password reset link has been sent to your email address.',
                'hasActions' => false
            ]);
            return back()->with('status', __($response));
        }

        // If sending fails, return an error
        throw ValidationException::withMessages([
            'email' => [trans($response)],
        ]);
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