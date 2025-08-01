<?php

namespace App\Http\Controllers\User\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
// Remove the RegistersUsers trait as we will implement the logic manually
// use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Auth\Events\Registered;
use App\Providers\RouteServiceProvider;
use Illuminate\Validation\ValidationException; // Import this exception

class RegisterController extends Controller
{
    /**
     * Where to redirect users after registration.
     * This is the default redirect for RegistersUsers trait, but we'll override it in `register` method.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME; // This will be used if we let the trait handle the redirect, but we won't for non-auto-login.

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        try {
            // 1. Call the validator method to validate the request input.
            $this->validator($request->all())->validate();

            // 2. Create a new user instance.
            $user = $this->create($request->all());

            // 3. Trigger the Registered event.
            // This will trigger the Notification/Listener responsible for sending the verification email.
            event(new Registered($user));

            // IMPORTANT: Do NOT call Auth::login($user) or Auth::guard()->login($user) here.
            // This is the key to preventing automatic login.

            // 4. Flash a success message and redirect to the login page.
            $request->session()->flash('notification', [
                'type' => 'success',
                'title' => 'Registration Success!',
                'message' => 'Your account has been created. A verification email has been sent to your email, please check to verify your email address.',
                'hasActions' => true // Notification will have an 'Okay' button
            ]);

            // Redirect to the login page after successful registration, so the user can verify their email
            return redirect()->route('login');

        } catch (ValidationException $e) {
            // If validation fails, redirect back with errors and old input
            return redirect()->back()->withInput($request->except('password'))->withErrors($e->errors());
        } catch (\Exception $e) {
            // Catch other exceptions that might occur
            \Log::error('Registration error: ' . $e->getMessage(), ['exception' => $e]);

            // Flash custom error notification
            $request->session()->flash('notification', [
                'type' => 'error',
                'title' => 'Registration Failed!',
                'message' => 'An unexpected error occurred during registration. Please try again.',
                'hasActions' => false
            ]);

            // Redirect back to the registration page or another appropriate page
            return redirect()->back()->withInput($request->except('password'));
        }
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        return User::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'email_verified_at' => null, // Ensure it's explicitly null
            'last_login' => null, // Ensure it's explicitly null
        ]);
    }

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\View\View
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }
}