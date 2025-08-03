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
            margin-bottom: 2px; /* Agar angka berada di tengah */
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
            font-family: var(--font-primary, 'Ancizar Serif'); /* <<--- UBAH DI SINI */
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
        opacity: 1;       /* Sesuaikan opasitas */
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
    .required-asterisk {
    color: #dc3545; /* Warna merah */
    font-weight: bold;
    margin-left: 2px;
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
                            <label for="full_name">Full Name<span class="required-asterisk">*</span></label>
                            <input type="text" id="full_name" name="full_name" class="form-control-custom" placeholder="Enter your full name" required>
                            <div class="error-message"></div> 
                        </div>

                        {{-- Date of Birth --}}
                        <div class="col-md-6 form-group-custom">
                            <label for="date_of_birth">Date of Birth<span class="required-asterisk">*</span></label>
                            <input type="text" onfocus="(this.type='date')" onblur="(this.type='text')" id="date_of_birth" name="date_of_birth" class="form-control-custom" placeholder="Select your date of birth" required>
                            <div class="error-message"></div>
                        </div>

                        {{-- Gender --}}
                        <div class="col-md-6 form-group-custom">
                            <label for="gender">Gender<span class="required-asterisk">*</span></label>
                            <select id="gender" name="gender" class="form-select-custom" required>
                                <option value="" selected disabled>Select your gender</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </select>
                            <div class="error-message"></div> 
                        </div>

                        {{-- Phone Number --}}
                        <div class="col-md-6 form-group-custom">
                            <label for="phone_number">Phone Number<span class="required-asterisk">*</span></label>
                            <input type="tel" id="phone_number" name="phone_number" class="form-control-custom" placeholder="Enter your phone number (e.g., 08123456789)" required minlength="10">
                            <div class="error-message"></div>
                        </div>

                        {{-- Email Address --}}
                        <div class="col-md-6 form-group-custom">
                            <label for="email_address">Email Address<span class="required-asterisk">*</span></label>
                            <input type="email" id="email_address" name="email_address" class="form-control-custom" placeholder="Enter your email address" required>
                            <div class="error-message"></div> {{-- <-- TAMBAHKAN INI --}}
                        </div>

                       {{-- Address --}}
                        <div class="col-12 form-group-custom">
                            <label for="home_address">Address<span class="required-asterisk">*</span></label>
                            <input type="text" id="home_address" name="home_address" class="form-control-custom" placeholder="Enter your home address" required>
                            <div class="error-message"></div> {{-- <-- TAMBAHKAN INI --}}
                        </div>

                        {{-- Province --}}
                        <div class="col-md-4 form-group-custom">
                            <label for="home_province">Province<span class="required-asterisk">*</span></label>
                            <select id="home_province" name="home_province" class="form-select-custom" required>
                                <option value="" selected disabled>Select your province</option>
                                @if(isset($provinces)) {{-- Pastikan provinces ada --}}
                                    @foreach ($provinces as $province)
                                        <option value="{{ $province }}">{{ $province }}</option>
                                    @endforeach
                                @endif
                            </select>
                            <div class="error-message"></div> {{-- <-- TAMBAHKAN INI --}}
                        </div>

                        {{-- City / Regency --}}
                        <div class="col-md-4 form-group-custom">
                            <label for="home_city">City / Regency<span class="required-asterisk">*</span></label>
                            <input type="text" id="home_city" name="home_city" class="form-control-custom" placeholder="Enter your city or regency" required>
                            <div class="error-message"></div> {{-- <-- TAMBAHKAN INI --}}
                        </div>

                        {{-- Postal Code --}}
                        <div class="col-md-4 form-group-custom">
                            <label for="home_postal_code">Postal Code<span class="required-asterisk">*</span></label>
                            <input type="text" id="home_postal_code" name="home_postal_code" class="form-control-custom" placeholder="Enter your postal code" required pattern="[0-9]{5}" title="5 digit postal code">
                            <div class="error-message"></div> {{-- <-- TAMBAHKAN INI --}}
                        </div>
                    </div>

                    {{-- Tombol Navigasi --}}
                    <div class="text-end mt-4">
                        <button type="button" class="btn btn-next-step" data-step="1"> Next <i class="bi bi-arrow-right"></i> </button>
                    </div>
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
                            <label for="business_name">Business Name<span class="required-asterisk">*</span></label>
                            <input type="text" id="business_name" name="business_name" class="form-control-custom" placeholder="Enter your business name" required>
                            <div class="error-message"></div> {{-- <-- TAMBAHKAN INI --}}
                        </div>
                        <div class="col-12 form-group-custom">
                            <label for="business_type">Business Type<span class="required-asterisk">*</span></label>
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
                            <label for="other_business_type">If Others, please specify:<span class="required-asterisk">*</span></label>
                            <input type="text" id="other_business_type" name="other_business_type" class="form-control-custom" placeholder="Enter your business type" required>
                            <div class="error-message"></div> 
                        </div>
                        <div class="col-12 form-group-custom">
                            <label for="business_description">Business Description<span class="required-asterisk">*</span></label>
                            <textarea id="business_description" name="business_description" class="form-control-custom" rows="3" placeholder="Briefly describe your business" required></textarea>
                            <div class="error-message"></div> 
                        </div>
                        <div class="col-md-6 form-group-custom">
                            <label for="business_phone_number">Business Phone Number<span class="required-asterisk">*</span></label>
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
                                <label for="business_address">Business Address<span class="required-asterisk">*</span></label>
                                <input type="text" id="business_address" name="business_address" class="form-control-custom" placeholder="Enter your business address" required>
                                <div class="error-message"></div> 
                            </div>
                            <div class="col-md-4 form-group-custom">
                                <label for="business_province">Province<span class="required-asterisk">*</span></label>
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
                                <label for="business_city">City / Regency<span class="required-asterisk">*</span></label>
                                <input type="text" id="business_city" name="business_city" class="form-control-custom" placeholder="Enter your city or regency" required>
                                <div class="error-message"></div> 
                            </div>
                            <div class="col-md-4 form-group-custom">
                                <label for="business_postal_code">Postal Code<span class="required-asterisk">*</span></label>
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
                                <label for="project_title">Project Title<span class="required-asterisk">*</span></label>
                                <input type="text" id="project_title" name="project_title" class="form-control-custom" placeholder="Enter the title of your project" required>
                                <div class="error-message"></div> {{-- <-- TAMBAHKAN INI --}}
                            </div>
                            {{-- Project Description --}}
                            <div class="col-12 form-group-custom">
                                <label for="project_description">Project Description<span class="required-asterisk">*</span></label>
                                <textarea id="project_description" name="project_description" class="form-control-custom" rows="3" placeholder="Briefly describe the project" required></textarea>
                                <div class="error-message"></div> {{-- <-- TAMBAHKAN INI --}}
                            </div>
                            {{-- Fabric Type --}}
                    <div class="col-md-6 form-group-custom">
                        <label for="fabric_type">Fabric Type<span class="required-asterisk">*</span></label>
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
                        <div class="error-message"></div> {{-- <-- TAMBAHKAN INI --}}
                    </div>

                    {{-- Input untuk "Others" Fabric Type (awalnya disembunyikan) --}}
                    <div class="col-md-6 form-group-custom" id="other_fabric_type_wrapper" style="display: none;">
                        <label for="other_fabric_type">If Others, please specify:<span class="required-asterisk">*</span></label>
                        <input type="text" id="other_fabric_type" name="other_fabric_type" class="form-control-custom" placeholder="Enter your fabric or product type" required>
                        <div class="error-message"></div> {{-- <-- TAMBAHKAN INI --}}
                    </div>
                            {{-- Year Created --}}
                            <div class="col-md-6 form-group-custom">
                                <label for="year_created">Year Created<span class="required-asterisk">*</span></label>
                                <input type="number" id="year_created" name="year_created" class="form-control-custom" placeholder="Enter the year the project was created" required min="1900" max="{{ date('Y') }}">
                                <div class="error-message"></div> {{-- <-- TAMBAHKAN INI --}}
                            </div>

                            {{-- Upload Photo --}}
                            {{-- GANTI SELURUH BLOK UPLOAD PHOTO DENGAN INI --}}
                            <div class="col-12 form-group-custom">
                                <label for="upload_photo">Upload Photo<span class="required-asterisk">*</span></label>
                                {{-- BERI ID PADA DROP AREA --}}
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

                            {{-- Checkbox untuk hak cipta --}}
                            {{-- Video Link --}}
                            <div class="col-12 form-group-custom">
                                <label for="video_link">Video link (optional)</label>
                                <input type="url" id="video_link" name="video_link" class="form-control-custom" placeholder="Paste the video link (optional)">
                                <div class="error-message"></div> {{-- <-- TAMBAHKAN INI --}}
                            </div>
                        </div>
                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-back-step" data-step="3"> <i class="bi bi-arrow-left"></i> Back </button>
                            <button type="submit" class="btn btn-next-step form-submit-btn" > FINISH </button>
                        </div>
                    </div>
                </div>

                {{-- STEP 4: Done (Contoh) --}}
    {{-- STEP 4: Done (KONTEN BARU) --}}
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

                {{-- Anda bisa menambahkan tombol untuk kembali ke homepage --}}
                <div class="text-center mt-5">
                    <a href="{{ route('welcome') }}" class="btn btn-next-step">
                        Back to Homepage
                    </a>
                </div>
            </div>
        </div>
        {{-- AKHIR STEP 4 --}}
            </form>
                
            </div>
        </div>
</div>

    @push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // --- Elemen & Variabel Utama ---
    const form = document.getElementById('multi-step-form');
    const nextButtons = document.querySelectorAll('.btn-next-step:not([type=submit])');
    const backButtons = document.querySelectorAll('.btn-back-step');
    const formSteps = document.querySelectorAll('.form-step');
    const stepIndicators = document.querySelectorAll('.stepper .step');
    let currentStep = 1; // Default ke step 1

    // --- Cek Kondisi Awal Halaman ---
    @if (session('registration_complete'))
        currentStep = 4;
    @elseif ($errors->any())
        @if ($errors->has('project_title') || $errors->has('upload_photo') || $errors->has('fabric_type') || $errors->has('year_created'))
            currentStep = 3;
        @elseif ($errors->has('business_name') || $errors->has('business_type') || $errors->has('business_address'))
            currentStep = 2;
        @else
            currentStep = 1;
        @endif
    @endif

    // --- Fungsi Utama Navigasi ---
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

    // --- FUNGSI VALIDASI DENGAN ATURAN SPESIFIK ---
    function validateStep(stepNumber) {
        let isValid = true;
        const currentStepElement = document.querySelector('#step-' + stepNumber);
        
        currentStepElement.querySelectorAll('.error-message').forEach(el => { el.textContent = ''; el.style.display = 'none'; });
        currentStepElement.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

        const requiredInputs = currentStepElement.querySelectorAll('[required]');
        
        requiredInputs.forEach(input => {
            const isVisible = input.offsetParent !== null;
            let errorMessage = '';

            if (isVisible) {
                // 1. Cek jika kosong
                if ((input.type !== 'file' && !input.value.trim()) || (input.type === 'file' && input.files.length === 0)) {
                    errorMessage = 'This field is required.';
                }
                
                // 2. Cek format spesifik JIKA sudah diisi
                if (input.value.trim() && !errorMessage) {
                    switch (input.type) {
                        case 'email':
                            const emailPattern = /^[^@\s]+@[^@\s]+\.[^@\s]+$/;
                            if (!emailPattern.test(input.value)) {
                                errorMessage = 'Please enter a valid email address.';
                            }
                            break;
                        
                        case 'tel':
                            const phonePattern = /^0[0-9]{9,14}$/;
                            if (!phonePattern.test(input.value.replace(/[\s-()]/g, ''))) {
                                errorMessage = 'Please enter a valid phone number starting with 0 (10–15 digits).';
                            }
                            break;

                        case 'number':
                            if (input.id === 'year_created') {
                                const year = parseInt(input.value);
                                const currentYear = new Date().getFullYear();
                                if (isNaN(year) || year < 1900 || year > currentYear) {
                                    errorMessage = `Year must be between 1400 and ${currentYear}.`;
                                }
                            }
                            break;
                        
                        case 'text':
                            if (input.id.includes('postal_code')) {
                                const postalPattern = /^[0-9]{5}$/;
                                if (!postalPattern.test(input.value)) {
                                    errorMessage = 'Postal code must be 5 digits.';
                                }
                            }
                            break;
                    }
                }
            }

            // 3. Jika ada pesan error, tampilkan
            if (errorMessage) {
                isValid = false;
                const formGroup = input.closest('.form-group-custom');
                if (formGroup) {
                    const errorElement = formGroup.querySelector('.error-message');
                    if (errorElement) {
                        errorElement.textContent = errorMessage;
                        errorElement.style.display = 'block';
                    }
                }
                
                if (input.type === 'file') {
                    input.closest('.file-drop-area').classList.add('is-invalid');
                } else {
                    input.classList.add('is-invalid');
                }
            }
        });

        return isValid;
    }

    // --- EVENT LISTENERS UNTUK TOMBOL NAVIGASI & SUBMIT ---
    nextButtons.forEach(button => {
        button.addEventListener('click', () => {
            const step = parseInt(button.dataset.step);
            if (validateStep(step)) {
                if (step < formSteps.length) {
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

    form.addEventListener('submit', function(e) {
        // Validasi step terakhir (Step 3) sebelum submit
        if (!validateStep(formSteps.length - 1)) {
            e.preventDefault(); // Hentikan submit jika validasi gagal
        }
    });

//     // --- [START] DEBUGGING CODE: FORM SUBMISSION HANDLER ---
//     form.addEventListener('submit', function(e) {
//     // 1. Selalu cegah pengiriman form agar kita bisa mengontrolnya
//     e.preventDefault();

//     // 2. Lakukan validasi pada step terakhir
//     if (validateStep(formSteps.length - 1)) {
//         // 3. Jika validasi LULUS, cetak data ke console
//         console.log("✅ --- DEBUG: DATA AKAN DIKIRIM --- ✅");
//         console.log("Skrip sekarang di-PAUSE. Periksa data di bawah ini.");
        
//         const formData = new FormData(form);
//         for (let [key, value] of formData.entries()) {
//             console.log(`'${key}':`, value);
//         }
//         console.log("---------------------------------------");
//         console.log("➡️ UNTUK MELANJUTKAN: Tekan tombol F8 atau ikon 'Resume script execution' (▶️) di Developer Tools.");

//         // 4. PAUSE! Skrip akan berhenti di sini jika Developer Tools terbuka.
//         debugger;

//         // 5. Kode ini baru akan berjalan SETELAH Anda menekan tombol "Resume"
//         console.log("Skrip dilanjutkan... Mengirim form.");
//         form.submit();

//     } else {
//         // Jika validasi GAGAL, cetak pesan error
//         console.error("❌ --- DEBUG: VALIDASI GAGAL, FORM TIDAK DIKIRIM --- ❌");
//     }
// });

    // --- [END] DEBUGGING CODE ---


    // --- Logika Helper (Alamat & Others) ---
    // (Kode untuk checkbox alamat dan dropdown "others" Anda sudah bagus,
    // jadi saya akan salin ke sini tanpa perubahan besar)
    const sameAddressCheckbox = document.getElementById('same_as_home_address');
    if (sameAddressCheckbox) {
        const homeAddress = document.getElementById('home_address');
        const homeProvince = document.getElementById('home_province');
        const homeCity = document.getElementById('home_city');
        const homePostalCode = document.getElementById('home_postal_code');
        
        const businessAddress = document.getElementById('business_address');
        const businessProvince = document.getElementById('business_province');
        const businessCity = document.getElementById('business_city');
        const businessPostalCode = document.getElementById('business_postal_code');

        const businessAddressInputs = [businessAddress, businessCity, businessPostalCode];
        
        // Pastikan semua elemen ditemukan
        if (homeAddress && homeProvince && homeCity && homePostalCode &&
            businessAddress && businessProvince && businessCity && businessPostalCode) {
            
            sameAddressCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    // 1. Salin nilai
                    businessAddress.value = homeAddress.value;
                    businessProvince.value = homeProvince.value;
                    businessCity.value = homeCity.value;
                    businessPostalCode.value = homePostalCode.value;
                    
                    // 2. Kunci (lock) field
                    businessAddressInputs.forEach(input => input.readOnly = true);
                    businessProvince.classList.add('locked');

                } else {
                    // 1. Kosongkan nilai
                    businessAddressInputs.forEach(input => input.value = '');
                    businessProvince.value = "";
                    
                    // 2. Buka kunci
                    businessAddressInputs.forEach(input => input.readOnly = false);
                    businessProvince.classList.remove('locked');
                }
            });
        } else {
            console.error("Address copy feature failed: One or more address field IDs are incorrect.");
        }
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
                }
            });
        }
    }
    setupOtherFieldToggle('business_type', 'other_business_type_wrapper');
    setupOtherFieldToggle('fabric_type', 'other_fabric_type_wrapper');

    
    // --- LOGIKA PREVIEW GAMBAR (TERMASUK DRAG & DROP) ---
    const fileInput = document.getElementById('upload_photo');
    const dropArea = document.getElementById('file-drop-area');
    const previewContainer = document.getElementById('image-preview-container');

    // Buat objek DataTransfer untuk mengelola file
    const dataTransfer = new DataTransfer();

    function handleFiles(files) {
        for (const file of files) {
            if (!file.type.startsWith('image/')) continue;
            
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
                    removeFile(file.name, previewWrapper);
                });

                previewWrapper.appendChild(previewImage);
                previewWrapper.appendChild(removeBtn);
                previewContainer.appendChild(previewWrapper);
            };
            reader.readAsDataURL(file);
        }
        // Update input file dengan file dari DataTransfer
        fileInput.files = dataTransfer.files;
    }

    function removeFile(fileName, elementToRemove) {
        const newFiles = Array.from(dataTransfer.files).filter(file => file.name !== fileName);
        dataTransfer.clearData();
        newFiles.forEach(file => dataTransfer.items.add(file));
        fileInput.files = dataTransfer.files;
        elementToRemove.remove();
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


    // --- INISIALISASI HALAMAN ---
    updateFormSteps();
    updateStepIndicator();
});
</script>
    @endpush

@endsection