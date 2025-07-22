@extends('layouts.app')

@section('title', 'Login')

@push('styles')
<style>
    body {
        background-color: #f8f9fa; /* Warna background netral */
    }

    .login-container {
        display: flex;
        flex-direction: column;
        /* justify-content: center; */
        align-items: center;
        background-image: url('{{ asset('images/BG/BG.png') }}');
        background-repeat: no-repeat;
        background-position: top center;
        background-size: 100% auto; /* Lebar penuh, tinggi mengikuti proporsi gambar */
        padding: 0;
        height: 55vh;
    }


    .login-card {
        /* background-color: #ffffff; */
        border: none;
        border-radius: 10px;
        /* box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1); */
        padding: 40px 60px; /* Padding lebih lebar di kiri/kanan untuk mengakomodasi 2 kolom */
        width: 100%;
        max-width: 800px; /* Max-width lebih besar untuk layout 2 kolom */
        text-align: center;
    }

    .form-control {
        border-radius: 0; /* Hilangkan border radius */
        padding: 10px 0; /* Padding vertikal sedikit, padding horizontal nol */
        font-size: 1rem;
        border: none; /* Hilangkan border penuh */
        border-bottom: 1px solid #ced4da; /* Hanya border bawah */
        background-color: transparent; /* Pastikan background transparan */
    }

    .form-control:focus {
        border-color: var(--color-brand);
        box-shadow: none; /* Hilangkan shadow saat focus */
        outline: none; /* Hilangkan outline default */
        border-bottom: 2px solid var(--color-brand); /* Border bawah lebih tebal saat focus */
        background-color: transparent;
    }

    .form-label {
        color: var(--color-text-dark);
        text-align: left;
        width: 100%;
        margin-bottom: 5px; /* Sesuaikan jarak label ke input */
        font-size: 18px;
    }

    .form-group {
        margin-bottom: 30px; /* Jarak antar grup form */
        position: relative; /* Untuk ikon mata */
    }

    .form-group .password-toggle {
        position: absolute;
        right: 0; /* Atur ke 0 agar mepet ke kanan input */
        top: 50%; /* Sesuaikan posisi ikon mata agar pas di tengah vertikal input */
        transform: translateY(-50%);
        cursor: pointer;
        color: #6c757d;
        padding: 0 5px; /* Sedikit padding agar ikon tidak terlalu mepet teks */
    }

    .btn-login {
        background-color: var(--color-brand);
        color: #ffffff;
        padding: 10px 30px; /* Sesuaikan padding tombol */
        border-radius: 5px;
        font-size: 1.1rem;
        font-weight: 600;
        width: auto; /* Lebar tombol menyesuaikan konten, bukan 100% */
        transition: background-color 0.3s ease;
        border: none;
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
        text-align: left; /* Biarkan ke kiri sesuai gambar */
        margin-top: 5px;
        font-size: 0.9rem;
    }

    .register-link {
        margin-top: 5px;
        font-size: 0.95rem;
    }

    .header {
        font-size: 48px;
    }

    .sub-heading {
        font-size: 20px;
    }

    .top-img {
        height: 40vh;
    }

    .form-control.is-invalid {
        border-color: #dc3545; /* Warna merah untuk error */
        background-image: none; /* Hilangkan ikon validasi default Bootstrap jika ada */
        padding-right: 0; /* Pastikan padding tidak berubah karena ikon */
    }

    .form-control.is-invalid:focus {
        border-color: #dc3545; /* Tetap merah saat focus */
        box-shadow: none; /* Hilangkan box-shadow default Bootstrap */
    }

    /* Override gaya default untuk invalid-feedback jika perlu */
    .invalid-feedback {
        display: block; /* Pastikan pesan error selalu terlihat */
        width: 100%;
        margin-top: 0.25rem;
        font-size: 0.875em;
        color: #dc3545;
    }
</style>
@endpush

@section('content')
<div class="w-100 top-img">
    <img src='{{ asset('images/BG/BG 2.png') }}' alt="" class="w-100 top-img">
</div>

<div class="container-fluid login-container flex-grow-1">
    <div class="login-card mt-4">
        <span class="font-serif-italic header m-0">Login</span>
        <p class="text-muted font-sans-thin-italic mb-4">Sign in with existing account</p>
        <form action="{{ route('login') }}" method="POST">
            @csrf
            {{-- Menggunakan satu Bootstrap Row untuk email dan password berdampingan --}}
            <div class="row">
                {{-- Kolom untuk Email --}}
                <div class="col-md-6 text-start">
                    <div class="form-group">
                        <label for="email" class="form-label font-sans-thin-italic">Email</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror font-sans-italic" id="email" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                        @error('email')
                            <div class="invalid-feedback text-start">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

                {{-- Kolom untuk Password --}}
                <div class="col-md-6 text-start">
                    <div class="form-group">
                        <label for="password" class="form-label font-sans-thin-italic">Password</label>
                        <div class="input-group">
                            <input type="password" class="form-control @error('password') is-invalid @enderror font-sans-italic" id="password" name="password" required autocomplete="current-password">
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
                </div>

            {{-- Tombol Login dan Teks Register (Di luar row email/password, mungkin di tengah atau di kiri/kanan bawah) --}}
            <div class="d-flex justify-content-start mt-2"> {{-- Margin top untuk memberi jarak dari input --}}
                <button type="submit" class="btn btn-login font-serif-bold">Login</button>
            </div>

            @if (Route::has('register'))
                <p class="register-link text-muted text-start">Don't have an account yet? <a href="{{ route('register') }}" class="text-link">Register now</a></p>
            @endif

        </form>
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