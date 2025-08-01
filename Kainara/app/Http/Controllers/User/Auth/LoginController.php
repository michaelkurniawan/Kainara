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
        // 1. Validate input
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        // 2. Attempt authentication process (without direct login)
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();

            // --- EMAIL VERIFICATION LOGIC STARTS HERE ---
            if (!$user->hasVerifiedEmail()) {
                Auth::logout(); // Logout unverified user
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                // Add error message to session using your custom format
                return back()->with('notification', [
                    'type' => 'error', // Adjust to your notification component's error type (e.g., 'danger')
                    'title' => 'Login Failed!',
                    'message' => 'Your account has not been verified. Please check your email for a verification link.',
                    'hasActions' => true // Notification will have an 'Okay' button
                ])->onlyInput('email');
            }
            // --- EMAIL VERIFICATION LOGIC ENDS HERE ---

            $request->session()->regenerate();

            if ($user) {
                $user->last_login = Carbon::now();
                $user->save();
            }

            return redirect()->intended('/');
        }

        // 3. If authentication fails
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

        // Flash a success notification to the session
        return redirect('/')->with('notification', [
            'type' => 'success', // Assuming you have a 'success' type for your notification component
            'title' => 'Logout Successful!',
            'message' => 'You have been successfully logged out of your account.',
            'hasActions' => false // Typically, a logout success doesn't need an action button
        ]);
    }
}