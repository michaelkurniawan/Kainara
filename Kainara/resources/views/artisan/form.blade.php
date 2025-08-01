@extends('layouts.app')

@section('title', 'Artisan Registration - Profile')

@section('content')

@push('styles')
<style>
    /* Variabel CSS (pastikan sudah ada di scope global atau definisikan di sini) */
    :root {
        --font-primary: 'Ancizar Serif', serif; /* atau Ancizar Serif */
        --font-secondary: 'Ancizar Serif', serif; /* atau Ancizar Serif */
        --color-brand: #AD9D6C; /* Warna emas/coklat muda (untuk Active & Completed) */
        --color-step-default: #343a40; /* Warna gelap untuk step yang belum dilalui */
        --color-text-muted: #6c757d; /*Warna emas/coklat muda */
        --color-brand-dark: #333333; /* Warna hitam untuk step aktif */
        --color-text-muted: #6c757d;
        --color-border-form: #333333;
        --color-bg-page: #FAF6F0; /* Warna background halaman seperti hero section */
    }

    .page-wrapper {
        position: relative;
        overflow-x: hidden;
    }

    .registration-form-page {
        background-color: #FFFFFF !important;
        padding-top: 4rem;
        padding-bottom: 5rem;
        /* position: relative; Untuk motif awan */
        /* overflow: hidden; */ /* Hapus jika ingin motif meluber */
    }

    /* Progress Bar / Stepper */
    .stepper {
        display: flex;
        justify-content: center;
        align-items: flex-start; /* Align item ke atas */
        list-style: none;
        padding: 0;
        margin-bottom: 4rem;
    }
    .stepper .step {
        display: flex;
        flex-direction: column;
        align-items: center;
        position: relative;
        text-align: center;
        width: 150px; /* Lebar setiap step */
    }
    .stepper .step-indicator {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-color: var(--color-step-default);
        border: 2px solid var(--color-step-default);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: var(--font-primary);
        font-weight: 700;
        margin-bottom: 0.5rem;
        position: relative;
        z-index: 2; /* Di atas garis konektor */
    }

    .stepper .step-indicator-number {
        font-size: 1.2rem;
        margin-bottom: 4px; /* Agar angka berada di tengah */
    }

    .stepper .step-label {
        font-family: var(--font-secondary);
        font-size: 0.9rem;
        color: var(--color-text-muted);
    }
    /* Styling untuk step yang aktif */
    .stepper .step.active .step-indicator {
        background-color: var(--color-brand);
        border-color: var(--color-brand);
        color: #fff;
    }
    .stepper .step.active .step-label {
        color: #333;
        font-weight: 600;
    }
    /* Styling untuk step yang sudah selesai */
    .stepper .step.completed .step-indicator {
        background-color: var(--color-brand);
        border-color: var(--color-brand);
        color: #fff;
    }
    .stepper .step.completed .step-label {
        color: #333;
    }
    /* Garis Konektor */
    .stepper .step:not(:last-child)::after {
        content: '';
        position: absolute;
        top: 20px; /* Di tengah-tengah lingkaran indikator */
        left: 50%;
        width: 100%;
        height: 2px;
        background-color: var(--color-text-muted);
        z-index: 1; /* Di belakang lingkaran */
    }
    .stepper .step.completed:not(:last-child)::after {
        background-color: var(--color-brand-dark);
    }


    /* Styling Form */
    .registration-form-container {
        max-width: 800px; /* Lebar maksimal form */
        margin: 0 auto;
    }
    .registration-form-container .form-section-title {
        font-family: var(--font-primary);
        font-size: 1.8rem;
        font-weight: 600;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid var(--color-border-form);
        display: inline-block; /* Agar border hanya selebar teks */
        margin-bottom: 3rem;
    }

    .completion-message{
        font-family: var(--font-primary, 'Ancizar Serif');
        font-size: 1.2rem;
        margin-bottom: 0.25rem;
        color: var(--color-text-dark, #333);
    }

    .completion-message .welcome-text {
        font-size: 1.8rem;
    }

    .completion-message .brand-name-text {
        font-style: italic; /* Tambahkan italic untuk penekanan */
        font-size: 4.5rem;
        margin-bottom: 2.5rem;
    }


    .form-group-custom {
        margin-bottom: 2rem;
    }

    .form-group-custom .form-control-custom:read-only,
    .form-group-custom .form-select-custom:disabled {
        background-color: #e9ecef; /* Warna abu-abu muda */
        cursor: not-allowed;
        opacity: 0.7; /* Sedikit pudar */
    }

    .form-group-custom label {
        font-family: var(--font-secondary);
        font-weight: 600;
        font-size: 0.9rem;
        margin-bottom: 0.5rem;
        display: block;
    }
    .form-group-custom .form-control-custom,
    .form-group-custom .form-select-custom {
        background-color: transparent;
        border: none;
        border-bottom: 1px solid var(--color-border-form);
        border-radius: 0;
        padding: 0.5rem 0;
        font-family: var(--font-secondary);
        font-size: 1rem;
        color: var(--color-text-dark);
        width: 100%;
    }
    .form-group-custom .form-control-custom:focus,
    .form-group-custom .form-select-custom:focus {
        outline: none;
        box-shadow: none;
        border-bottom-color: var(--color-brand);
    }
    .form-group-custom ::placeholder { /* Chrome, Firefox, Opera, Safari 10.1+ */
        color: var(--color-text-muted);
        opacity: 1; /* Firefox */
    }

    /* Styling Select Dropdown Arrow */
    .form-group-custom .form-select-custom {
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 0.25rem center;
        background-size: 16px 12px;
        padding-right: 1.5rem; /* Ruang untuk panah */
    }

        .registration-form-container .btn-back-step {
            background-color: var(--color-brand);
            color: white;
            padding: 0.6rem 2.5rem;
            border-radius: 6px;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.8px;
            border: none;
            transition: background-color 0.3s ease, transform 0.2s ease;
            font-size: 1rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        .registration-form-container .btn-back-step:hover {
            background-color: #a58e6a;
            transform: translateY(-2px);
        }
        .registration-form-container .btn-back-step .bi-arrow-right {
            font-size: 1.2rem; /* Ukuran ikon panah */
        }


        /* Tombol Next */
        .registration-form-container .btn-next-step {
            background-color: var(--color-brand);
            color: white;
            padding: 0.6rem 2.5rem;
            border-radius: 6px;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.8px;
            border: none;
            transition: background-color 0.3s ease, transform 0.2s ease;
            font-size: 1rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        .registration-form-container .btn-next-step:hover {
            background-color: #a58e6a;
            transform: translateY(-2px);
        }
        .registration-form-container .btn-next-step .bi-arrow-right {
            font-size: 1.2rem; /* Ukuran ikon panah */
        }
        .form-step {
        display: none;
    }
    .form-step.active {
        display: block;
    }

    /* Styling untuk area Drag & Drop foto */
    .file-drop-area {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        padding: 4rem 1rem;
        border: 2px dashed var(--color-text-muted, #6c757d);
        border-radius: 6px;
        background-color: #f8f9fa;
        transition: background-color 0.2s ease-in-out;
        text-align: center;
        color: var(--color-text-muted);
    }
    .file-drop-area.is-active {
        background-color: #e9ecef;
    }
    .file-drop-area .file-input {
        position: absolute;
        left: 0;
        top: 0;
        height: 100%;
        width: 100%;
        cursor: pointer;
        opacity: 0;
    }
    .file-drop-area .browse-btn {
        background-color: var(--color-brand, #B9A077);
        color: white;
        padding: 0.5rem 1.5rem;
        border-radius: 4px;
        border: none;
        margin-left: 0.5rem;
    }
        .form-group-custom .form-check {
            padding-left: 0; /* Hapus padding default form-check Bootstrap */
            display: flex;
            align-items: center;
            gap: 0.75rem; /* Jarak antara checkbox dan label */
        }
        .form-group-custom .form-check .form-check-input {
            /* Styling ulang checkbox agar lebih minimalis */
            float: none; /* Hapus float default */
            margin-top: 1px;
            margin-left: 0;
            width: 1.25em; /* Ukuran checkbox */
            height: 1.25em;
            border: 1px solid var(--color-border-form, #333);
            border-radius: 2px; /* Sedikit rounded atau 0 untuk kotak */
            cursor: pointer;
        }
        .form-group-custom .form-check .form-check-input:checked {
            background-color: var(--color-brand, #AD9D6C); /* Warna saat dicentang */
            border-color: var(--color-brand, #AD9D6C);
        }
        .form-group-custom .form-check .form-check-input:focus {
            box-shadow: 0 0 0 0.25rem rgba(173, 157, 108, 0.25); /* Shadow warna brand */
        }
        .form-group-custom .form-check .form-check-label {
            margin-bottom: 0; /* Hapus margin bawah dari label checkbox */
            font-weight: normal; /* Label checkbox tidak perlu bold */
            color: var(--color-text-dark);
        }
        .decorative-motif {
            position: absolute; /* Untuk penempatan bebas */
            z-index: 0;         /* Di belakang konten utama hero (teks dan model) */
            opacity: 1;      /* Sesuaikan opasitas */
            pointer-events: none; /* Agar tidak mengganggu interaksi */
        }
        .decorative-motif img {
            display: block;
            width: 100%;
            height: auto;
        }

        /* Posisi dan ukuran untuk masing-masing awan */
        .motif-hero-top-left {
            /* top: 0px;  Sesuaikan */
            top: -30px; /* Sedikit keluar dari atas */
            left: -70px; /* Sedikit keluar */
            width: 280px; /* Sesuaikan ukuran */
            /* transform: rotate(-5deg); */ /* Opsional: sedikit rotasi */
        }
        .motif-hero-top-right {
            top: 80px;
            right: -50px;
            width: 300px;
            /* transform: scaleX(-1); Balik horizontal jika menggunakan gambar yang sama */
            /* transform: scaleX(-1) rotate(3deg); */
        }
        .motif-hero-bottom-left {
            bottom: 60px;
            left: -40px;
            width: 240px;
        }
        .motif-hero-bottom-right {
            bottom: 10px;
            right: -60px;
            width: 260px;
            /* transform: scaleX(-1); Balik horizontal  */
        }

        .motif-latest-top-left {
            /* top: 0px;  Sesuaikan */
            bottom: -20px; /* Sedikit keluar dari atas */
            left: -70px; /* Sedikit keluar */
            width: 280px; /* Sesuaikan ukuran */
            /* transform: rotate(-5deg); */ /* Opsional: sedikit rotasi */
        }
        .motif-latest-top-right {
            bottom: -100px;
            right: -120px;
            width: 300px;
            /* transform: scaleX(-1); Balik horizontal jika menggunakan gambar yang sama */
            /* transform: scaleX(-1) rotate(3deg); */
        }
        /* Styling untuk pesan error validasi front-end */
        .form-group-custom .error-message {
            color: #dc3545; /* Warna merah Bootstrap untuk bahaya */
            font-size: 0.8rem;
            margin-top: 0.25rem;
            display: none; /* Awalnya disembunyikan */
        }

        /* Styling untuk input yang tidak valid */
        .form-group-custom .form-control-custom.is-invalid,
        .form-group-custom .form-select-custom.is-invalid {
            border-bottom-color: #dc3545; /* Garis bawah menjadi merah */
        }

        /* Kontainer untuk preview gambar */
        .image-preview-container {
            display: flex;
            flex-wrap: wrap; /* Agar gambar bisa pindah baris jika tidak muat */
            gap: 1rem; /* Jarak antar thumbnail */
        }
        .image-preview-item {
            position: relative;
            width: 100px; /* Ukuran thumbnail */
            height: 100px;
            border: 1px solid #ddd;
            border-radius: 4px;
            overflow: hidden;
        }
        .image-preview-item img {
            width: 100%;
            height: 100%;
            object-fit: cover; /* Pastikan gambar mengisi area thumbnail tanpa distorsi */
        }
        .image-preview-item .remove-btn {
            position: absolute;
            top: 2px;
            right: 2px;
            width: 20px;
            height: 20px;
            background-color: rgba(0, 0, 0, 0.6);
            color: white;
            border: none;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            line-height: 1;
            opacity: 0; /* Sembunyikan tombol hapus secara default */
            transition: opacity 0.2s ease;
        }
        .image-preview-item:hover .remove-btn {
            opacity: 1; /* Tampilkan tombol hapus saat mouse di atas gambar */
        }
    </style>
@endpush

<div class="page-wrapper">
    <div class="registration-form-page">
        {{-- Motif Awan (jika diinginkan) --}}
        <div class="decorative-motif motif-hero-top-left">
            <img src="{{ asset('images/motif-batik.png') }}" alt="Motif Batik">
        </div>
        <div class="decorative-motif motif-hero-top-right">
            <img src="{{ asset('images/motif-batik.png') }}" alt="Motif Batik">
        </div>
        <div class="decorative-motif motif-hero-bottom-left">
            <img src="{{ asset('images/motif-batik.png') }}" alt="Motif Batik">
        </div>
        <div class="decorative-motif motif-hero-bottom-right">
            <img src="{{ asset('images/motif-batik.png') }}" alt="Motif Batik">
        </div>

        <div class="container">
            {{-- Progress Bar Stepper --}}
            <div class="stepper">
                <div id="step-indicator-1" class="step active">
                    <div class="step-indicator">
                        <span class="step-indicator-number">1</span>
                    </div>
                    <div class="step-label">Registrant Profile</div>
                </div>
                <div id="step-indicator-2" class="step"> <div class="step-indicator"><span class="step-indicator-number">2</span></div> <div class="step-label">Business Information</div> </div>
                <div id="step-indicator-3" class="step"> <div class="step-indicator"><span class="step-indicator-number">3</span></div> <div class="step-label">Portfolio</div> </div>
                <div id="step-indicator-4" class="step"> <div class="step-indicator"><span class="step-indicator-number">4</span></div> <div class="step-label">Done</div> </div>
            </div>

            {{-- Form Container --}}
            <form action="{{ route('artisan.register.store') }}" method="POST" id="multi-step-form" enctype="multipart/form-data">
                @csrf

                {{-- STEP 1: Owner Profile --}}
                <div id="step-1" class="form-step active">
                    <div class="registration-form-container">
                        <h2 class="form-section-title">Owner Profile</h2>
                        {{-- Full Name --}}
                        <div class="row">
                            <div class="col-12 form-group-custom">
                                <label for="full_name">Full Name</label>
                                <input type="text" id="full_name" name="full_name" class="form-control-custom" placeholder="Enter your full name" required>
                                <div class="error-message"></div>
                            </div>

                            {{-- Date of Birth --}}
                            <div class="col-md-6 form-group-custom">
                                <label for="date_of_birth">Date of Birth</label>
                                <input type="date" id="date_of_birth" name="date_of_birth" class="form-control-custom" placeholder="Select your date of birth" required pattern="\d{4}-\d{2}-\d{2}">
                                <div class="error-message"></div>
                            </div>

                            {{-- Gender --}}
                            <div class="col-md-6 form-group-custom">
                                <label for="gender">Gender</label>
                                <select id="gender" name="gender" class="form-select-custom" required>
                                    <option value="" selected disabled>Select your gender</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                </select>
                                <div class="error-message"></div>
                            </div>

                            {{-- Phone Number --}}
                            <div class="col-md-6 form-group-custom">
                                <label for="phone_number">Phone Number</label>
                                <input type="tel" id="phone_number" name="phone_number" class="form-control-custom" placeholder="Enter your phone number" required>
                                <div class="error-message"></div>
                            </div>

                            {{-- Email Address --}}
                            <div class="col-md-6 form-group-custom">
                                <label for="email_address">Email Address</label>
                                <input type="email" id="email_address" name="email_address" class="form-control-custom" placeholder="Enter your email address" required>
                                <div class="error-message"></div>
                            </div>

                            {{-- Address --}}
                            <div class="col-12 form-group-custom">
                                <label for="home_address">Address</label>
                                <input type="text" id="home_address" name="home_address" class="form-control-custom" placeholder="Enter your home address" required>
                                <div class="error-message"></div>
                            </div>

                            {{-- Province --}}
                            <div class="col-md-4 form-group-custom">
                                <label for="home_province">Province</label>
                                <select id="home_province" name="home_province" class="form-select-custom" required>
                                    <option value="" selected disabled>Select your province</option>
                                    @if(isset($provinces))
                                        @foreach ($provinces as $province)
                                            <option value="{{ $province }}">{{ $province }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <div class="error-message"></div>
                            </div>

                            {{-- City / Regency --}}
                            <div class="col-md-4 form-group-custom">
                                <label for="home_city">City / Regency</label>
                                <input type="text" id="home_city" name="home_city" class="form-control-custom" placeholder="Enter your city or regency" required>
                                <div class="error-message"></div>
                            </div>

                            {{-- Postal Code --}}
                            <div class="col-md-4 form-group-custom">
                                <label for="home_postal_code">Postal Code</label>
                                <input type="text" id="home_postal_code" name="home_postal_code" class="form-control-custom" placeholder="Enter your postal code" required>
                                <div class="error-message"></div>
                            </div>
                        </div>

                        {{-- Tombol Navigasi --}}
                        <div class="text-end mt-4">
                            <button type="button" class="btn btn-next-step" data-step="1"> Next <i class="bi bi-arrow-right"></i> </button>
                        </div>
                    </div>
                </div>

                {{-- STEP 2: Business Information (STRUKTUR DIPERBAIKI) --}}
                <div id="step-2" class="form-step">
                    <div class="registration-form-container">
                        <h2 class="form-section-title">Business Information</h2>

                        {{-- Baris pertama untuk info dasar bisnis --}}
                        <div class="row">
                            <div class="col-12 form-group-custom">
                                <label for="business_name">Business Name</label>
                                <input type="text" id="business_name" name="business_name" class="form-control-custom" placeholder="Enter your business name" required>
                                <div class="error-message"></div>
                            </div>
                            <div class="col-12 form-group-custom">
                                <label for="business_type">Business Type</label>
                                <select id="business_type" name="business_type" class="form-select-custom" required>
                                    <option value="" selected disabled>Select the type of business</option>
                                    <option value="batik_artisan">Batik Artisan</option>
                                    <option value="tenun_artisan">Tenun Artisan</option>
                                    <option value="lurik_artisan">Lurik Artisan</option>
                                    <option value="fabric_seller">Traditional Fabric Seller</option>
                                    <option value="fashion_designer">Fashion Designer</option>
                                    <option value="others">Others</option>
                                </select>
                                <div class="error-message"></div>
                            </div>
                            <div class="col-12 form-group-custom" id="other_business_type_wrapper" style="display: none;">
                                <label for="other_business_type">If Others, please specify:</label>
                                <input type="text" id="other_business_type" name="other_business_type" class="form-control-custom" placeholder="Enter your business type" required>
                                <div class="error-message"></div>
                            </div>
                            <div class="col-12 form-group-custom">
                                <label for="business_description">Business Description</label>
                                <textarea id="business_description" name="business_description" class="form-control-custom" rows="3" placeholder="Briefly describe your business" required></textarea>
                                <div class="error-message"></div>
                            </div>
                            <div class="col-md-6 form-group-custom">
                                <label for="business_phone_number">Business Phone Number</label>
                                <input type="tel" id="business_phone_number" name="business_phone_number" class="form-control-custom" placeholder="Enter your business phone number" required>
                                <div class="error-message"></div>
                            </div>
                            <div class="col-md-6 form-group-custom">
                                <label for="business_email">Business Email <span class="text-muted small fw-normal">(not required)</span></label>
                                <input type="email" id="business_email" name="business_email" class="form-control-custom" placeholder="Enter your business email address">
                            </div>
                            <div class="col-12 form-group-custom">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="same_as_home_address">
                                    <label class="form-check-label" for="same_as_home_address">
                                        Check if your company address is the same as your home address
                                    </label>
                                </div>
                            </div>
                        </div> {{-- Akhir dari div.row pertama --}}

                        {{-- Kontainer untuk field alamat bisnis, dibungkus dalam ID-nya sendiri --}}
                        <div id="business_address_fields">
                            <div class="row">
                                <div class="col-12 form-group-custom">
                                    <label for="business_address">Business Address</label>
                                    <input type="text" id="business_address" name="business_address" class="form-control-custom" placeholder="Enter your business address" required>
                                    <div class="error-message"></div>
                                </div>
                                <div class="col-md-4 form-group-custom">
                                    <label for="business_province">Province</label>
                                    <select id="business_province" name="business_province" class="form-select-custom" required>
                                        <option value="" selected disabled>Select your province</option>
                                        @if(isset($provinces))
                                            @foreach ($provinces as $province)
                                                <option value="{{ $province }}">{{ $province }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <div class="error-message"></div>
                                </div>
                                <div class="col-md-4 form-group-custom">
                                    <label for="business_city">City / Regency</label>
                                    <input type="text" id="business_city" name="business_city" class="form-control-custom" placeholder="Enter your city or regency" required>
                                    <div class="error-message"></div>
                                </div>
                                <div class="col-md-4 form-group-custom">
                                    <label for="business_postal_code">Postal Code</label>
                                    <input type="text" id="business_postal_code" name="business_postal_code" class="form-control-custom" placeholder="Enter your postal code" required>
                                    <div class="error-message"></div>
                                </div>
                            </div> {{-- Akhir dari div.row untuk alamat --}}
                        </div> {{-- Akhir dari div#business_address_fields --}}

                        {{-- Tombol Navigasi --}}
                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-back-step" data-step="2"> <i class="bi bi-arrow-left"></i> Back </button>
                            <button type="button" class="btn btn-next-step" data-step="2"> Next <i class="bi bi-arrow-right"></i> </button>
                        </div>

                    </div> {{-- Akhir dari .registration-form-container --}}
                </div> {{-- Akhir dari #step-2 --}}

                {{-- STEP 3: Portfolio (Sesuai Desain Anda) --}}
                <div id="step-3" class="form-step">
                    <div class="registration-form-container">
                        <h2 class="form-section-title">Portfolio</h2>
                        <div class="row">
                            {{-- Project Title --}}
                            <div class="col-12 form-group-custom">
                                <label for="project_title">Project Title</label>
                                <input type="text" id="project_title" name="project_title" class="form-control-custom" placeholder="Enter the title of your project" required>
                                <div class="error-message"></div>
                            </div>
                            {{-- Project Description --}}
                            <div class="col-12 form-group-custom">
                                <label for="project_description">Project Description</label>
                                <textarea id="project_description" name="project_description" class="form-control-custom" rows="3" placeholder="Briefly describe the project" required></textarea>
                                <div class="error-message"></div>
                            </div>
                            {{-- Fabric Type --}}
                            <div class="col-md-6 form-group-custom">
                                <label for="fabric_type">Fabric Type</label>
                                <select id="fabric_type" name="fabric_type" class="form-select-custom" required>
                                    <option value="" selected disabled>Select the type of fabric or product</option>
                                    <option value="cotton">Cotton</option>
                                    <option value="silk">Silk</option>
                                    <option value="linen">Linen</option>
                                    <option value="wool">Wool</option>
                                    <option value="rayon">Rayon</option>
                                    <option value="polyester">Polyester</option>
                                    <option value="others">Others</option> {{-- Value untuk memicu input teks --}}
                                </select>
                                <div class="error-message"></div>
                            </div>

                            {{-- Input untuk "Others" Fabric Type (awalnya disembunyikan) --}}
                            <div class="col-md-6 form-group-custom" id="other_fabric_type_wrapper" style="display: none;">
                                <label for="other_fabric_type">If Others, please specify:</label>
                                <input type="text" id="other_fabric_type" name="other_fabric_type" class="form-control-custom" placeholder="Enter your fabric or product type" required>
                                <div class="error-message"></div>
                            </div>
                            {{-- Year Created --}}
                            <div class="col-md-6 form-group-custom">
                                <label for="year_created">Year Created</label>
                                <input type="number" id="year_created" name="year_created" class="form-control-custom" placeholder="Enter the year the project was created" required min="1900" max="{{ date('Y') }}" maxlength="4">
                                <div class="error-message"></div>
                            </div>

                            {{-- Upload Photo --}}
                            <div class="col-12 form-group-custom">
                                <label for="upload_photo">Upload Photo</label>
                                <div id="file-drop-area" class="file-drop-area">
                                    <span class="file-message">Drag & drop files here or</span>
                                    <span class="browse-btn">Browse</span>
                                    <input type="file" id="upload_photo" name="upload_photo[]" class="file-input" multiple required accept="image/*">
                                </div>

                                {{-- Kontainer untuk menampilkan preview gambar --}}
                                <div id="image-preview-container" class="image-preview-container mt-3">
                                    {{-- Gambar preview akan muncul di sini via JavaScript --}}
                                </div>

                                {{-- Tempat untuk pesan error validasi --}}
                                <div class="error-message"></div>
                            </div>

                            {{-- Video Link --}}
                            <div class="col-12 form-group-custom">
                                <label for="video_link">Video link (optional)</label>
                                <input type="url" id="video_link" name="video_link" class="form-control-custom" placeholder="Paste the video link (optional)">
                                <div class="error-message"></div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-back-step" data-step="3"> <i class="bi bi-arrow-left"></i> Back </button>
                            {{-- Tombol FINISH: Ubah type menjadi submit dan hapus data-step --}}
                            <button type="submit" class="btn btn-next-step form-submit-btn"> FINISH </button>
                        </div>
                    </div>
                </div>

                {{-- STEP 4: Done --}}
                <div id="step-4" class="form-step">
                    <div class="registration-form-container">
                        <h2 class="form-section-title">Done</h2>

                        <div class="completion-message text-center">
                            <p class="welcome-text">Welcome to</p>
                            <h3 class="brand-name-text">Kainara</h3>
                            <p class="thank-you-paragraph mx-auto">
                                Thank you for registering as a fabric supplier with Kainara. We're excited to have you on board as part of our
                                mission to connect Indonesia's traditional fabrics with the global fashion scene. Your information has been
                                successfully received. Our team will review your submission and get in touch with you shortly for the next
                                steps. Together, let's preserve cultural heritage, empower local communities, and inspire the world through the
                                beauty of Indonesian textiles.
                            </p>

                            <div class="signature-block text-end">
                                <p class="mb-0">Warm regards,</p>
                                <p class="mb-0">The Kainara Team</p>
                            </div>
                        </div>

                        <div class="text-center mt-5">
                            <a href="{{ route('welcome') }}" class="btn btn-next-step">
                                Back to Homepage
                            </a>
                        </div>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // --- Elemen & Variabel Utama ---
    const form = document.getElementById('multi-step-form');
    const nextButtons = document.querySelectorAll('.btn-next-step');
    const backButtons = document.querySelectorAll('.btn-back-step');
    const formSteps = document.querySelectorAll('.form-step');
    const stepIndicators = document.querySelectorAll('.stepper .step');
    let currentStep = 1;

    // --- FUNGSI UTAMA ---
    function updateFormSteps() {
        formSteps.forEach(step => step.classList.remove('active'));
        document.getElementById('step-' + currentStep)?.classList.add('active');
    }

    function updateStepIndicator() {
        stepIndicators.forEach((indicator, index) => {
            indicator.classList.remove('active', 'completed');
            if (index < currentStep - 1) {
                indicator.classList.add('completed');
            } else if (index === currentStep - 1) {
                indicator.classList.add('active');
            }
        });
    }

    function validateStep(stepNumber) {
        let isValid = true;
        const currentStepElement = document.querySelector('#step-' + stepNumber);

        // Bersihkan semua error di step saat ini
        const errorMessages = currentStepElement.querySelectorAll('.error-message');
        errorMessages.forEach(el => {
            el.textContent = '';
            el.style.display = 'none';
        });

        const invalidInputs = currentStepElement.querySelectorAll('.is-invalid');
        invalidInputs.forEach(el => {
            if (el.classList.contains('form-control-custom') || el.classList.contains('form-select-custom')) {
                el.classList.remove('is-invalid');
            } else if (el.classList.contains('file-drop-area')) {
                el.classList.remove('is-invalid');
            }
        });

        // Validasi semua input yang required
        const requiredInputs = currentStepElement.querySelectorAll('[required]');
        requiredInputs.forEach(input => {
            const isVisible = input.offsetParent !== null; // Check if element is actually visible

            let inputIsValid = true;
            let errorMessage = '';

            if (isVisible) { // Only validate visible required fields
                if (input.type === 'file') {
                    // Specific validation for file input
                    if (input.files.length === 0) {
                        inputIsValid = false;
                        errorMessage = 'Please upload at least one photo.';
                        input.closest('.file-drop-area').classList.add('is-invalid');
                    }
                } else if (input.tagName === 'SELECT') {
                    if (!input.value) {
                        inputIsValid = false;
                        errorMessage = 'Please select an option.';
                    }
                } else if (!input.value.trim()) {
                    inputIsValid = false;
                    errorMessage = 'This field is required.';
                } else if (input.id === 'date_of_birth') {
                    // Validasi format tanggal dd/mm/yyyy di JS (setelah input type=date dikonversi ke string YYYY-MM-DD)
                    // Input type="date" secara native sudah memastikan YYYY-MM-DD
                    // Jika Anda ingin format DD/MM/YYYY saat user mengetik, Anda perlu library mask input.
                    // Untuk saat ini, kita validasi nilai yang dihasilkan oleh input type="date"
                    const datePattern = /^\d{4}-\d{2}-\d{2}$/; // YYYY-MM-DD
                    if (!datePattern.test(input.value)) {
                        inputIsValid = false;
                        errorMessage = 'Please enter a valid date (YYYY-MM-DD).'; // Atau sesuaikan pesan
                    }
                } else if (input.id === 'year_created') {
                    // Validasi tahun 4 digit
                    const yearPattern = /^\d{4}$/;
                    if (!yearPattern.test(input.value)) {
                        inputIsValid = false;
                        errorMessage = 'Please enter a valid 4-digit year.';
                    }
                    // Juga cek min/max dari atribut HTML
                    if (parseInt(input.value) < parseInt(input.min) || parseInt(input.value) > parseInt(input.max)) {
                         inputIsValid = false;
                         errorMessage = `Year must be between ${input.min} and ${input.max}.`;
                    }
                }
            }

            if (!inputIsValid) {
                isValid = false;
                const formGroup = input.closest('.form-group-custom');
                if (formGroup) {
                    const errorElement = formGroup.querySelector('.error-message');
                    if (errorElement) {
                        errorElement.textContent = errorMessage;
                        errorElement.style.display = 'block';
                    }
                }
                // Tambahkan kelas is-invalid ke input (kecuali file input, karena sudah di dropArea)
                if (input.type !== 'file') {
                    input.classList.add('is-invalid');
                }
            }
        });

        return isValid;
    }

    // --- LOGIKA PREVIEW GAMBAR (TERMASUK DRAG & DROP) ---
    const fileInput = document.getElementById('upload_photo');
    const dropArea = document.getElementById('file-drop-area');
    const previewContainer = document.getElementById('image-preview-container');

    // Buat objek DataTransfer untuk mengelola file
    const dataTransfer = new DataTransfer();

    function handleFiles(files) {
        // Clear previous previews if new files are selected directly, or if it's the first set of files
        // but not if dragging additional files.
        // For this multi-step form, if user comes back and re-uploads, we should replace them.
        // If they just add more, we append.
        if (fileInput.files.length === 0) { // If no files previously selected via input
             previewContainer.innerHTML = ''; // Clear previews
             dataTransfer.clearData(); // Clear DataTransfer object
        }


        for (const file of files) {
            if (!file.type.startsWith('image/')) continue;

            // Check for duplicates before adding
            let isDuplicate = false;
            for (let i = 0; i < dataTransfer.files.length; i++) {
                if (dataTransfer.files[i].name === file.name && dataTransfer.files[i].size === file.size) {
                    isDuplicate = true;
                    break;
                }
            }
            if (isDuplicate) continue; // Skip if duplicate

            // Tambahkan file ke objek DataTransfer
            dataTransfer.items.add(file);

            const reader = new FileReader();
            reader.onload = (e) => {
                const previewWrapper = document.createElement('div');
                previewWrapper.classList.add('image-preview-item');

                const previewImage = document.createElement('img');
                previewImage.src = e.target.result;

                const removeBtn = document.createElement('button');
                removeBtn.classList.add('remove-btn');
                removeBtn.innerHTML = '×';
                removeBtn.type = 'button';
                removeBtn.addEventListener('click', () => {
                    removeFile(file, previewWrapper); // Pass the actual file object
                });

                previewWrapper.appendChild(previewImage);
                previewWrapper.appendChild(removeBtn);
                previewContainer.appendChild(previewWrapper);
            };
            reader.readAsDataURL(file);
        }
        // Update input file dengan file dari DataTransfer
        fileInput.files = dataTransfer.files;

        // Trigger validation check for file input after handling files
        validateStep(currentStep);
    }

    function removeFile(fileToRemove, elementToRemove) {
        // Find the index of the file to remove in the DataTransfer object
        let fileIndex = -1;
        for (let i = 0; i < dataTransfer.files.length; i++) {
            if (dataTransfer.files[i] === fileToRemove) {
                fileIndex = i;
                break;
            }
        }

        if (fileIndex > -1) {
            dataTransfer.items.remove(fileIndex); // Remove the file from DataTransfer
            fileInput.files = dataTransfer.files; // Update the actual input files
            elementToRemove.remove(); // Remove the preview element from DOM
        }
         // Trigger validation check for file input after removing files
        validateStep(currentStep);
    }

    if (fileInput && dropArea && previewContainer) {
        fileInput.addEventListener('change', () => handleFiles(fileInput.files));

        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropArea.addEventListener(eventName, e => e.preventDefault());
        });
        ['dragenter', 'dragover'].forEach(eventName => {
            dropArea.addEventListener(eventName, () => dropArea.classList.add('is-active'));
        });
        ['dragleave', 'drop'].forEach(eventName => {
            dropArea.addEventListener(eventName, () => dropArea.classList.remove('is-active'));
        });

        dropArea.addEventListener('drop', e => handleFiles(e.dataTransfer.files));
    }


    // --- EVENT LISTENERS ---
    nextButtons.forEach(button => {
        button.addEventListener('click', () => {
            const step = parseInt(button.dataset.step);
            if (validateStep(step)) { // Validasi step saat ini
                if (currentStep < formSteps.length) {
                    currentStep++;
                    updateFormSteps();
                    updateStepIndicator();
                }
            }
        });
    });

    backButtons.forEach(button => {
        button.addEventListener('click', () => {
            const step = parseInt(button.dataset.step);
            if (step > 1) {
                currentStep--;
                updateFormSteps();
                updateStepIndicator();
            }
        });
    });

    // --- Logika Helper (Alamat & Others) ---
    const sameAddressCheckbox = document.getElementById('same_as_home_address');
    const businessAddressFields = document.getElementById('business_address_fields');

    if (sameAddressCheckbox && businessAddressFields) {
        const businessAddressInputs = businessAddressFields.querySelectorAll('input, select');
        
        // Initial state on page load
        if (sameAddressCheckbox.checked) {
            businessAddressFields.style.display = 'none';
            businessAddressInputs.forEach(input => input.removeAttribute('required'));
        } else {
            businessAddressFields.style.display = 'block';
            businessAddressInputs.forEach(input => input.setAttribute('required', 'required'));
        }

        sameAddressCheckbox.addEventListener('change', function() {
            if (this.checked) {
                // Copy home address to business address (if needed)
                document.getElementById('business_address').value = document.getElementById('home_address').value;
                document.getElementById('business_province').value = document.getElementById('home_province').value;
                document.getElementById('business_city').value = document.getElementById('home_city').value;
                document.getElementById('business_postal_code').value = document.getElementById('home_postal_code').value;

                // Hide and make non-required
                businessAddressFields.style.display = 'none';
                businessAddressInputs.forEach(input => input.removeAttribute('required'));
            } else {
                // Show and make required again
                businessAddressFields.style.display = 'block';
                businessAddressInputs.forEach(input => input.setAttribute('required', 'required'));
                // Clear the copied values to avoid confusion if user unchecks
                document.getElementById('business_address').value = '';
                document.getElementById('business_province').value = '';
                document.getElementById('business_city').value = '';
                document.getElementById('business_postal_code').value = '';
            }
        });
    }

    function setupOtherFieldToggle(selectId, wrapperId) {
        const selectElement = document.getElementById(selectId);
        const wrapperElement = document.getElementById(wrapperId);
        const otherInput = wrapperElement?.querySelector('input');
        if (selectElement && wrapperElement && otherInput) {
            selectElement.addEventListener('change', function() {
                if (this.value === 'others') {
                    wrapperElement.style.display = 'block';
                    otherInput.required = true;
                } else {
                    wrapperElement.style.display = 'none';
                    otherInput.required = false;
                    otherInput.value = ''; // Clear value when hidden
                }
            });
            // Initial check in case "others" is pre-selected (e.g., old input)
            if (selectElement.value === 'others') {
                wrapperElement.style.display = 'block';
                otherInput.required = true;
            }
        }
    }
    setupOtherFieldToggle('business_type', 'other_business_type_wrapper');
    setupOtherFieldToggle('fabric_type', 'other_fabric_type_wrapper');


    // --- INISIALISASI HALAMAN ---
    // Cek apakah ada session 'registration_complete' dari controller
    const registrationComplete = {{ session('registration_complete') ? 'true' : 'false' }};
    if (registrationComplete) {
        currentStep = 4; 
    }

    updateFormSteps();
    updateStepIndicator();
});
</script>
@endpush

@endsection