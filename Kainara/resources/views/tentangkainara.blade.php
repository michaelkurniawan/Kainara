@extends('layouts.app')

@section('title', 'About Us')

@push('styles')
    <style>
        /* Menerapkan font yang sudah terdaftar secara global */
        /* GANTI DENGAN INI: */
        /* Aturan ini memastikan semua elemen teks utama menggunakan font AncizarSerif */
        body, h1, h2, h3, h4, h5, h6, p, a, li, span, strong, em {
            font-family: 'AncizarSerif', serif;
        }

        .hero-section-fullwidth {
            min-height: 90vh;
            background-image: url('{{ asset('images/atastentangkainara.svg') }}');
            background-size: cover; 
            background-position: center; 
            background-repeat: no-repeat; 
        }

        .content-section {
            background-color: #FFFFFF;
            padding-top: 50px;
        }
        
        .logo-banner {
            max-width: 100%;
            height: auto;
            margin-bottom: 1.5rem;
        }

        .card-custom {
            height: 100%;
            border: none;
            background-color: #F5F1E9;
            border-radius: 1rem;
            display: flex;
            flex-direction: column;
        }
        
        .card-custom .card-body {
            flex-grow: 1;
        }

        .card-img-top-custom {
            width: 250px;
            height: auto;
            align-self: center;
            margin-top: 2rem;
            margin-bottom: 1rem;
        }
        
        .card-text-custom-size {
            font-size: 1rem; /* Setara dengan sekitar 14.4px. Ganti nilainya sesuai keinginan Anda */
        }
        .card-img-cendrawasih-large {
            margin-bottom: 0rem;
            width: 190px; /* Ukuran baru khusus untuk gambar pertama */
        }

        /* --- PERUBAHAN DI SINI --- */
        .card-divider {
            border: 0;
            height: 2px; /* Diubah dari 1px */
            background-color: #B6B09F;
            margin: 0.5rem auto 1.5rem auto;
            width: 80%;
        }
        /* --- AKHIR PERUBAHAN --- */

        .section-kainara-story {
            background-image: url('{{ asset('images/backgroundbawah-tentangkainara.png') }}');
            background-color: rgba(255, 255, 255, 0.7);
            background-size: cover;
            background-position: center;
            background-blend-mode: lighten;
            position: relative;
            min-height: 100vh;
            display: flex;
            justify-content: center; /* Tetap tengahkan secara horizontal */
            padding: 15vh 1rem;
        }

        .section-kainara-story h1,
        .section-kainara-story p {
            text-align: center;
        }
        
        .section-kainara-story h1 {
            margin-bottom: 2rem;
            letter-spacing: 2px;
        }
        
        .section-kainara-story p {
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
            margin-bottom: 1.5rem;
            letter-spacing: 1px; /* Atur spasi huruf untuk paragraf */
            line-height: 1.8;
        }
    </style>
@endpush

@section('content')
    <!-- 1. Hero Section sekarang menjadi background full-screen -->
    <!-- PERHATIKAN: Tag <img> di dalamnya sudah dihapus -->
    <section class="hero-section-fullwidth">
        {{-- Section ini sengaja dikosongkan, karena tampilannya diatur oleh CSS background --}}
    </section>

    <!-- 2. Wrapper untuk konten yang tetap di tengah (dalam container) -->
    <div class="content-section">
        <div class="container-fluid px-5 py-5">
            <div class="row g-4 justify-content-center align-items-stretch">
                <div class="col-md-4 d-flex flex-column">
                    <div class="card p-3 card-custom shadow-sm flex-grow-1">
                        <img src="{{ asset('images/cendrawasih.png') }}" class="card-img-top-custom card-img-cendrawasih-large" alt="Cenderawasih">
                        <div class="card-body text-center">
                            <hr class="card-divider">
                            <h5 class="card-title fw-bold fst-italic fs-3">Cendrawasih</h5>
                            <p class="card-text card-text-custom-size">The bird of paradise is one of the many animals carved into batik motifs, especially Papuan batik, because this bird is considered a bird of heaven and has a strong philosophical meaning in Papuan culture. In addition, the beautiful and colorful feathers of the bird of paradise are an inspiration for batik artists to create unique and interesting motifs.</p>
                        </div>
                    </div>
                </div>

                {{-- KOLOM 2: Banner Logo + Card Canting --}}
                <div class="col-md-4 d-flex flex-column text-center">
                    <img src="{{ asset('images/titlepenjelasanlogo.png') }}" alt="Title Penjelasan Logo" class="logo-banner">
                    <div class="card p-3 card-custom shadow-sm flex-grow-1">
                        <img src="{{ asset('images/tangancanting.png') }}" class="card-img-top-custom" alt="Canting Batik">
                        <div class="card-body">
                            <hr class="card-divider">
                            <h5 class="card-title fw-bold fst-italic fs-3">Canting Batik</h5>
                            <p class="card-text card-text-custom-size mt-4">An essential tool in the creation of hand-drawn batik, used to apply hot wax onto fabric to form intricate batik patterns.</p>
                        </div>
                    </div>
                </div>
                
                {{-- KOLOM 3: Card Penjelasan Kainara --}}
                <div class="col-md-4 d-flex flex-column">
                    <div class="card p-3 card-custom shadow-sm flex-grow-1">
                        <img src="{{ asset('images/kainara.png') }}" class="card-img-top-custom" alt="Logo Kainara">
                        <div class="card-body">
                            <hr class="card-divider mt-2">
                            <p class="card-text card-text-custom-size text-center mt-4">"Kainara" is a combination of two words <br><em>kain</em> + <em>nara</em></p>
                            <p class="text-start card-text-custom-size mt-4"><strong>Kain</strong> = Represents the main product of this start-up, namely typical Indonesian fabrics such as batik, woven fabrics, lurik, and so on.</p>
                            <p class="text-start card-text-custom-size"><strong>Nara</strong> = Taken from the word ‘Nusantara’. Inspired by Sanskrit, which can mean “story” or “human”.</p>
                            <p class="card-text card-text-custom-size text-center mt-4">It can be interpreted as <em>"kain yang bercerita"</em> or <em>"kain yang punya jiwa dan koneksi dengan manusia"</em>.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Section cerita yang juga membentang penuh (Full Width) -->
    <section class="section-kainara-story">
        <div class="container">
            <h1 class="fs-1">What is<em> “Kainara”</em>?</h1>
            <p class="fs-5">
                Kainara is a digital platform that connects the beauty of traditional Indonesian textiles with the modern world through technology.
                We offer authentic fabrics—such as batik, tenun, and lurik—sourced directly from local artisans and small businesses across Indonesia.
            </p>
            <p class="fs-5">
                More than just a marketplace, Kainara is a collaborative space where designers and artisans create unique, meaningful, and sustainable fashion.
                We promote inclusive economic growth, empower women through technology, and support environmentally responsible production.
            </p>
            <p class="fs-5">
                Kainara celebrates Indonesia’s cultural heritage by telling the story behind every piece of fabric—blending tradition with innovation
                to shape a more equitable and sustainable future for fashion.
            </p>
        </div>
    </section>
@endsection