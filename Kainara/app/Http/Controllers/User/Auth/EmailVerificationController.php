<?php

namespace App\Http\Controllers\User\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use App\Providers\RouteServiceProvider;
use App\Models\User; // Ensure the User model is imported
use Illuminate\Support\Facades\URL; // For creating signed URLs
use Illuminate\Auth\Notifications\VerifyEmail; // Laravel's built-in email verification notification

class EmailVerificationController extends Controller
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
     * Show the email verification notice.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        // Check if the user is already verified
        if (!is_null($request->user()->email_verified_at)) {
            return redirect($this->redirectPath());
        }

        return view('auth.verify-email');
    }

    /**
     * Mark the given user's email as verified.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function verify(Request $request)
    {
        // Find the user by ID from the URL
        $user = User::findOrFail($request->route('id'));

        // Ensure the URL hash is valid and matches the user
        $expectedHash = sha1($user->email); // Get email directly from the user object
        $providedHash = (string) $request->route('hash');

        if (! hash_equals($providedHash, $expectedHash)) {
            throw new \Illuminate\Auth\Access\AuthorizationException('Invalid verification hash.');
        }

        // Check if the user is already verified
        if (!is_null($user->email_verified_at)) {
            // If already verified, flash a notification that it's already verified
            session()->flash('notification', [
                'type' => 'info',
                'title' => 'Email Already Verified',
                'message' => 'Your email has already been verified.',
                'hasActions' => true // Changed to TRUE for an OK button
            ]);
            return redirect()->route('login');
        }

        // Mark the email as verified
        $user->email_verified_at = Carbon::now();
        $verified = $user->save(); // Save changes to the database

        if ($verified) {
            event(new Verified($user)); // Trigger the Verified event

            // Flash custom notification after successful verification
            session()->flash('notification', [
                'type' => 'success',
                'title' => 'Verification Successful!',
                'message' => 'Your email has been successfully verified. Please log in to your account.',
                'hasActions' => true // Changed to TRUE for an OK button
            ]);
        } else {
            // Flash notification if saving fails
            session()->flash('notification', [
                'type' => 'error',
                'title' => 'Verification Failed',
                'message' => 'An error occurred while verifying your email. Please try again.',
                'hasActions' => true // Changed to TRUE for an OK button
            ]);
        }

        // IMPORTANT: Log out the user if they were automatically logged in by the verification process.
        // This is to prevent automatic login after verification.
        if (Auth::check()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        // Redirect to the login page
        return redirect()->route('login');
    }

    /**
     * Resend the email verification notification.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function resend(Request $request)
    {
        // Check if the user is already verified
        if (!is_null($request->user()->email_verified_at)) {
            return redirect($this->redirectPath());
        }

        // Send the email verification notification
        $user = $request->user();
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(config('auth.verification.expire', 60)),
            [
                'id' => $user->getKey(),
                'hash' => sha1($user->email), // Using email directly from the user object
            ]
        );

        // Using Laravel's built-in email verification notification
        $user->notify(new VerifyEmail($verificationUrl));

        session()->flash('notification', [
            'type' => 'info',
            'title' => 'Verification Email Resent',
            'message' => 'A new verification link has been sent to your email address.',
            'hasActions' => true // Changed to TRUE for an OK button
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