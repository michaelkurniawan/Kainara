@extends('layouts.app')

@section('title', 'Login')

@push('styles')
<style>
    body {
        background-color: #f8f9fa; /* Warna background netral */
    }

    .login-container {
        min-height: calc(100vh - var(--header-actual-height) - var(--footer-padding-y) - 5rem); /* Sesuaikan tinggi agar konten pas di tengah vertikal, kurangi tinggi header & footer */
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        background-image: url('{{ asset('images/background-login-top.png') }}'), url('{{ asset('images/background-login-bottom.png') }}');
        background-repeat: no-repeat, no-repeat;
        background-position: top center, bottom center;
        background-size: contain, contain; /* Menyesuaikan gambar agar sesuai dengan ukuran container */
        padding: 50px 0; /* Memberi padding atas bawah agar konten tidak terlalu mepet */
    }

    .login-card {
        background-color: #ffffff;
        border: none;
        border-radius: 10px;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
        padding: 40px;
        width: 100%;
        max-width: 500px;
        text-align: center;
    }

    .login-card h2 {
        font-family: var(--font-primary);
        color: var(--color-text-dark);
        margin-bottom: 10px;
        font-size: 2.2rem;
        font-weight: 700;
    }

    .login-card p.text-muted {
        font-size: 0.95rem;
        margin-bottom: 30px;
    }

    .form-control {
        border-radius: 5px;
        padding: 12px 15px;
        font-size: 1rem;
        border: 1px solid #ced4da;
    }

    .form-control:focus {
        border-color: var(--color-brand);
        box-shadow: 0 0 0 0.25rem rgba(var(--color-brand-rgb, 173, 157, 108), 0.25); /* Menggunakan variabel CSS untuk warna brand */
    }

    .form-label {
        font-weight: 500;
        color: var(--color-text-dark);
        text-align: left;
        width: 100%;
        margin-bottom: 8px;
    }

    .form-group {
        margin-bottom: 20px;
        position: relative; /* Untuk ikon mata */
    }

    .form-group .password-toggle {
        position: absolute;
        right: 15px;
        top: 60%; /* Sesuaikan posisi ikon mata */
        transform: translateY(-50%);
        cursor: pointer;
        color: #6c757d;
    }

    .btn-login {
        background-color: var(--color-brand);
        color: #ffffff;
        padding: 12px 0;
        border-radius: 5px;
        font-size: 1.1rem;
        font-weight: 600;
        width: 100%;
        transition: background-color 0.3s ease;
    }

    .btn-login:hover {
        background-color: #9a8a5e; /* Warna sedikit lebih gelap saat hover */
        color: #ffffff;
    }

    .text-link {
        color: var(--color-brand);
        text-decoration: none;
        font-weight: 500;
        transition: color 0.2s ease;
    }

    .text-link:hover {
        color: #9a8a5e;
        text-decoration: underline;
    }

    .reset-password-link {
        display: block;
        text-align: left;
        margin-top: 5px;
        font-size: 0.9rem;
    }

    .register-link {
        margin-top: 25px;
        font-size: 0.95rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid login-container">
    <div class="login-card">
        <h2>Login</h2>
        <p class="text-muted">Sign in with existing account</p>

        <form action="{{ route('login') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                @error('email')
                    <div class="invalid-feedback text-start">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <div class="input-group">
                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required autocomplete="current-password">
                    <span class="password-toggle" id="password-toggle">
                        <i class="fa-solid fa-eye-slash"></i>
                    </span>
                    @error('password')
                        <div class="invalid-feedback text-start">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-link reset-password-link">Reset Password</a>
                @endif
            </div>

            <div class="d-grid gap-2 mb-4">
                <button type="submit" class="btn btn-login">Login</button>
            </div>
        </form>

        @if (Route::has('register'))
            <p class="register-link text-muted">Don't have an account yet? <a href="{{ route('register') }}" class="text-link">Register now</a></p>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const passwordField = document.getElementById('password');
        const passwordToggle = document.getElementById('password-toggle');

        if (passwordToggle) {
            passwordToggle.addEventListener('click', function () {
                const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordField.setAttribute('type', type);
                this.querySelector('i').classList.toggle('fa-eye');
                this.querySelector('i').classList.toggle('fa-eye-slash');
            });
        }
    });
</script>
@endpush