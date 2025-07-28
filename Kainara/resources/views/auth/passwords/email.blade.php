@extends('layouts.app')

@section('title', 'Forgot Password')

@push('styles')
    {{-- Gaya kustom Anda untuk halaman login/reset password bisa di sini --}}
    <style>
        body { background-color: #f8f9fa; }
        .login-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            background-image: url('{{ asset('images/BG/BG.png') }}');
            background-repeat: no-repeat;
            background-position: top center;
            background-size: 100% auto;
            padding: 0;
            height: 55vh;
        }
        .login-card {
            border: none;
            border-radius: 10px;
            padding: 40px 60px;
            width: 100%;
            max-width: 500px; /* Sesuaikan lebar card */
            text-align: center;
        }
        .form-control { border-radius: 0; padding: 10px 0; border: none; border-bottom: 1px solid #ced4da; background-color: transparent; }
        .form-control:focus { border-color: var(--color-brand); box-shadow: none; outline: none; border-bottom: 2px solid var(--color-brand); background-color: transparent; }
        .form-label { color: var(--color-text-dark); text-align: left; width: 100%; margin-bottom: 5px; font-size: 18px; }
        .form-group { margin-bottom: 30px; position: relative; }
        .btn-login { background-color: var(--color-brand); color: #ffffff; padding: 10px 30px; border-radius: 5px; font-size: 1.1rem; font-weight: 600; width: auto; transition: background-color 0.3s ease; border: none; }
        .btn-login:hover { background-color: #9a8a5e; color: #ffffff; }
        .text-link { color: var(--color-brand); text-decoration: none; font-weight: 500; transition: color 0.2s ease; }
        .text-link:hover { color: #9a8a5e; text-decoration: underline; }
        .header { font-size: 48px; }
        .sub-heading { font-size: 24px; }
        .top-img { height: 40vh; }
        .form-control.is-invalid { border-color: #dc3545; background-image: none; padding-right: 0; }
        .form-control.is-invalid:focus { border-color: #dc3545; box-shadow: none; }
        .invalid-feedback { display: block; width: 100%; margin-top: 0.25rem; font-size: 0.875em; color: #dc3545; }
    </style>
@endpush

@section('content')
<div class="w-100 top-img">
    <img src='{{ asset('images/BG/BG 2.png') }}' alt="" class="w-100 top-img">
</div>

<div class="container-fluid login-container flex-grow-1">
    <div class="login-card mt-4">
        <span class="font-serif-italic header m-0">Forgot Password</span>
        <p class="text-muted font-sans-thin-italic mb-4">Enter your email to reset your password</p>

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="form-group text-start">
                <label for="email" class="form-label font-sans-thin-italic">Email</label>
                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror font-sans-italic" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                @error('email')
                    <div class="invalid-feedback text-start">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="d-flex justify-content-start mt-2">
                <button type="submit" class="btn btn-login font-serif-bold">
                    Send Password Reset Link
                </button>
            </div>

            <p class="register-link text-muted text-start font-sans-light-italic mt-3">
                Remembered your password? <a href="{{ route('login') }}" class="text-link font-sans-italic">Login now</a>
            </p>
        </form>
    </div>
</div>
@endsection