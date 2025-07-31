@extends('layouts.app')

@section('title', 'Reset Password')

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
        .form-group .password-toggle {
            position: absolute;
            right: 0;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #6c757d;
            padding: 0 5px;
        }
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
        <span class="font-serif-italic header m-0">Reset Password</span>
        <p class="text-muted font-sans-thin-italic mb-4">Set your new password</p>

        <form method="POST" action="{{ route('password.update') }}">
            @csrf

            <input type="hidden" name="token" value="{{ $token }}">

            <div class="form-group text-start">
                <label for="email" class="form-label font-sans-thin-italic">Email</label>
                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror font-sans-italic" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus readonly> {{-- Ditambahkan 'readonly' di sini --}}
                @error('email')
                    <div class="invalid-feedback text-start">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="form-group text-start">
                <label for="password" class="form-label font-sans-thin-italic">Password</label>
                <div class="input-group">
                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror font-sans-italic" name="password" required autocomplete="new-password">
                    <span class="password-toggle" id="password-toggle">
                        <i class="fa-solid fa-eye-slash"></i>
                    </span>
                    @error('password')
                        <div class="invalid-feedback text-start">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>

            <div class="form-group text-start">
                <label for="password-confirm" class="form-label font-sans-thin-italic">Confirm Password</label>
                <div class="input-group">
                    <input id="password-confirm" type="password" class="form-control font-sans-italic" name="password_confirmation" required autocomplete="new-password">
                    <span class="password-toggle" id="password-confirm-toggle">
                        <i class="fa-solid fa-eye-slash"></i>
                    </span>
                </div>
            </div>

            <div class="d-flex justify-content-start mt-2">
                <button type="submit" class="btn btn-login font-serif-bold">
                    Reset Password
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const passwordField = document.getElementById('password');
        const passwordToggle = document.getElementById('password-toggle');
        const passwordConfirmField = document.getElementById('password-confirm');
        const passwordConfirmToggle = document.getElementById('password-confirm-toggle');

        function setupPasswordToggle(field, toggle) {
            if (toggle) {
                toggle.addEventListener('click', function () {
                    const type = field.getAttribute('type') === 'password' ? 'text' : 'password';
                    field.setAttribute('type', type);
                    this.querySelector('i').classList.toggle('fa-eye');
                    this.querySelector('i').classList.toggle('fa-eye-slash');
                });
            }
        }

        setupPasswordToggle(passwordField, passwordToggle);
        setupPasswordToggle(passwordConfirmField, passwordConfirmToggle);
    });
</script>
@endpush
