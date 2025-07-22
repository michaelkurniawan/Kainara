@extends('layouts.app')

@section('title', 'Register')

@push('styles')
<style>
    body {
        background-color: #f8f9fa; /* Warna background netral */
        margin: 0;
        padding: 0;
    }

    .background-top-fullwidth {
        width: 100%;
        height: 40vh; /* Sesuaikan tinggi ini sesuai kebutuhan */
        background-image: url('{{ asset('images/BG/BG.png') }}'); /* Gambar latar belakang atas */
        background-repeat: no-repeat;
        background-position: top center;
        background-size: cover;
        position: relative;
        z-index: 1;
    }

    .register-wrapper {
        width: 100%;
        min-height: calc(100vh - var(--header-actual-height) - 40vh - var(--footer-padding-y));
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        position: relative;
        z-index: 2;
        background-image: url('{{ asset('images/BG/BG 2.png') }}'); /* Gambar latar belakang bawah */
        background-repeat: no-repeat;
        background-position: bottom center;
        background-size: contain;
        padding-top: 50px;
        padding-bottom: 50px;
    }

    .register-container {
        padding: 0;
        height: auto;
        flex-grow: 1;
    }

    .register-card {
        background-color: #ffffff;
        border: none;
        border-radius: 10px;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
        padding: 40px 60px; /* Padding disesuaikan */
        width: 100%;
        max-width: 800px; /* Max-width lebih besar untuk layout 2 kolom jika diinginkan */
        text-align: center;
    }

    .register-card h2 {
        font-family: var(--font-primary);
        color: var(--color-text-dark);
        margin-bottom: 5px;
        font-size: 2.5rem;
        font-weight: 700;
    }

    .register-card p.text-muted {
        font-size: 1rem;
        margin-bottom: 50px;
    }

    /* Gaya untuk form control, label, group, dan password toggle tetap sama dengan halaman login */
    .form-control {
        border-radius: 0;
        padding: 10px 0;
        font-size: 1rem;
        border: none;
        border-bottom: 1px solid #ced4da;
        background-color: transparent;
    }

    .form-control:focus {
        border-color: var(--color-brand);
        box-shadow: none;
        outline: none;
        border-bottom: 2px solid var(--color-brand);
        background-color: transparent;
    }

    .form-label {
        color: var(--color-text-dark);
        text-align: left;
        width: 100%;
        margin-bottom: 5px;
        font-size: 18px;
    }

    .form-group {
        margin-bottom: 30px;
        position: relative;
    }

    .form-group .password-toggle {
        position: absolute;
        right: 0;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        color: #6c757d;
        padding: 0 5px;
    }

    .btn-register { /* Menggunakan btn-register agar terpisah dari login */
        background-color: var(--color-brand);
        color: #ffffff;
        padding: 10px 30px;
        border-radius: 5px;
        font-size: 1.1rem;
        font-weight: 600;
        width: auto;
        transition: background-color 0.3s ease;
        border: none;
    }

    .btn-register:hover {
        background-color: #9a8a5e;
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

    .login-link { /* Untuk link 'Already have an account?' */
        margin-top: 20px;
        font-size: 0.95rem;
    }

    /* Font Classes (tetap sama, perlu dimasukkan di app.blade.php jika belum) */
    .font-serif-italic {
        font-family: var(--font-primary);
        font-weight: 400;
        font-style: italic;
    }

    .font-sans-thin-italic {
        font-family: var(--font-secondary);
        font-weight: 100;
        font-style: italic;
    }

    .font-serif-bold {
        font-family: var(--font-primary);
        font-weight: 700;
    }

    .header {
        font-size: 48px;
    }

    .sub-heading {
        font-size: 20px;
    }
</style>
@endpush

@section('content')
{{-- Gambar BG.png sebagai latar belakang lebar penuh di bagian atas --}}
<div class="background-top-fullwidth">
    {{-- Anda bisa menambahkan konten di sini jika diperlukan --}}
</div>

{{-- Wrapper baru untuk kartu registrasi dan gambar BG 2.png di bagian bawah --}}
<div class="register-wrapper">
    <div class="container-fluid register-container">
        <div class="register-card">
            <span class="font-serif-italic header m-0">Register</span>
            <p class="text-muted font-sans-thin-italic mb-4">Create your new account</p>
            <form action="{{ route('register') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6 text-start"> {{-- Kolom Kiri --}}
                        <div class="form-group">
                            <label for="name" class="form-label font-sans-thin-italic">Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
                            @error('name')
                                <div class="invalid-feedback text-start">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="email" class="form-label font-sans-thin-italic">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required autocomplete="email">
                            @error('email')
                                <div class="invalid-feedback text-start">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6 text-start"> {{-- Kolom Kanan --}}
                        <div class="form-group">
                            <label for="password" class="form-label font-sans-thin-italic">Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required autocomplete="new-password">
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

                        <div class="form-group">
                            <label for="password-confirm" class="form-label font-sans-thin-italic">Confirm Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password-confirm" name="password_confirmation" required autocomplete="new-password">
                                <span class="password-toggle" id="password-confirm-toggle">
                                    <i class="fa-solid fa-eye-slash"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div> {{-- End of Row --}}

                <div class="d-flex justify-content-start mt-3"> {{-- Tombol Register --}}
                    <button type="submit" class="btn btn-register font-serif-bold">Register</button>
                </div>

                {{-- Link Login --}}
                @if (Route::has('login'))
                    <p class="login-link text-muted text-start">Already have an account? <a href="{{ route('login') }}" class="text-link">Login here</a></p>
                @endif
            </form>
        </div>
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