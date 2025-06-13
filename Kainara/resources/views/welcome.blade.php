@extends('layouts.app')

@section('title', 'Homepage Kainara')

@section('content')

@push('styles')
<style>
    /* Variabel CSS (pastikan didefinisikan global atau di sini) */
    :root {
        --font-primary: 'Ancizar Serif', serif;
        --font-secondary: 'Ancizar Serif', serif;
        /* Warna dan variabel lain yang Anda gunakan */
        --section-padding-y: 5rem; /* Padding atas bawah standar untuk section teks */
    }

    /* STYLING UNTUK MOTIF AWAN DEKORATIF */
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

     /* HERO SECTION STYLING */
    .hero-section-custom { /* Menggunakan nama kelas baru agar tidak konflik */
        min-height: 90vh;
        background-color: var(--color-bg-hero);
        display: flex;
        align-items: center; /* Pusatkan row secara vertikal */
        position: relative; /* Untuk awan absolut */
        overflow: hidden;   /* Agar awan yang keluar sedikit tidak menyebabkan scroll */
        /* padding-top: 3rem; Padding atas internal section */
        /* padding-bottom: 3rem;Padding bawah internal section */
    }

    /* Kontainer untuk teks dan tombol di hero section */
    .hero-section-custom .hero-text-content {
        /* Jika kolom parent (.col-lg-5) tidak text-center, kita pusatkan isinya dari sini */
        display: flex;
        flex-direction: column;
        /* align-items: center; jika ingin semua item di kolom ini center */
        align-items: flex-start;
        text-align: center;
        position: relative; /* Untuk z-index jika perlu */
        z-index: 2;
        padding-left: 4rem;
    }

    .hero-section-custom .hero-text-content h1 {
        font-family: var(--font-primary, 'Ancizar Serif', serif); /* Pastikan Ancizar Serif dimuat */
        color: var(--color-text-hero-title, #212529);
        font-size: 3rem; /* Sesuaikan dengan desain */
        font-weight: 700; /* Bootstrap fw-bold */
        line-height: 1.3;
        margin-bottom: 1.5rem;  /* Jarak kiri untuk teks */
        text-align: center;
        margin-left: 4rem;
    }

    .hero-section-custom .hero-text-content h1 .hero-title-italic {
        font-style: italic;
        font-weight: 300; /* Atau 400 (normal) jika font Anda punya weight ini. Ancizar Serif punya. */
                          /* Hindari font-weight: normal; jika ingin lebih spesifik */
    }

    .hero-section-custom .hero-text-content h1 .hero-title-bold-italic {
        font-style: italic;
        font-weight: 700; /* Bold */
    }

    .hero-section-custom .hero-text-content p.lead {
        font-family: var(--font-secondary);
        color: var(--color-text-hero-lead);
        font-size: 1.1rem; /* Bootstrap fs-5 */
        max-width: 450px; /* Batasi lebar subjudul */
        /* padding-left: 2rem; */
        margin-bottom: 1rem; /* Jarak kiri untuk teks */
        text-align: center;
        margin-left: 2rem;
    }

    .hero-section-custom .btn-kainara { /* Pastikan styling tombol ini ada */
        background-color: var(--color-brand);
        color: white;
        padding: 0.8rem 2.5rem; /* Sesuaikan padding tombol */
        border-radius: 4px; /* Sedikit rounded */
        text-transform: uppercase;
        font-weight: 500;
        letter-spacing: 0.5px;
        border: none;
        transition: background-color 0.3s ease;
        /* margin-left: 4rem; Jarak dari teks */
        margin-top: 2rem; /* Jarak dari teks */
        margin-left: 6rem;
    }
    .hero-section-custom .btn-kainara:hover {
        background-color: #a58e6a; /* Warna hover lebih gelap */
    }

    .hero-section-custom .hero-image-container img.hero-model-img {
        max-width: 100%; /* Pastikan tidak melebihi lebar kolomnya */
        height: auto;    /* Jaga aspek rasio */
        /* Coba atur max-height relatif terhadap viewport height,
           dikurangi sedikit untuk padding dan header */
        max-height: 90vh; /* Contoh: 85% vh dikurangi tinggi header */
        display: block; /* Untuk menghilangkan spasi bawah */
        margin-left: auto; /* Untuk mendorong ke kanan dalam kolomnya (default) */
        margin-right: auto; /* Jika kolomnya text-center, atau kita atur manual */
        transform: translateY(-35px); 
    }

    /* SECTION: EVERY PATTERN TELLS A STORY (Versi Teks Overlay) */
    .story-video-section-overlay {
        min-height: 90vh; /* Atau 90vh sesuai preferensi Anda */
        position: relative;
        background-color: #1a1a1a; /* Fallback */
        overflow: hidden;   /* PENTING */
        padding: 0;
        display: flex;          /* Section utama jadi flex container */
    }

    .story-video-section-overlay .video-bg-wrapper-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;  /* 100% dari section */
        height: 100%; /* 100% dari section */
        overflow: hidden;
        z-index: 1; /* Di belakang teks dan overlay eksplisit */
    }

    .story-video-section-overlay .video-bg-wrapper-overlay video#storyOverlayVideo {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        min-width: 100%;
        min-height: 100%;
        width: auto;
        height: auto;
        object-fit: cover; /* Pastikan video menutupi wrapper */
    }

    /* Overlay Gelap Eksplisit (Jika masih diperlukan di atas video di dalam wrapper) */
    .story-video-section-overlay .video-bg-wrapper-overlay .video-overlay-layer {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.50); /* Sesuaikan opasitas */
        /* z-index tidak perlu jika sudah di dalam wrapper yang sama dengan video */
    }

    .story-video-section-overlay .video-text-content-overlay {
        position: relative; /* Sekarang relatif terhadap flow flex, BUKAN ABSOLUT */
        z-index: 2;         /* Di atas video-bg-wrapper-overlay */
        color: #fff;
        text-align: center;
        padding: 2rem 1rem; /* Padding agar tidak mepet jika ada batas container */
        max-width: 700px;   /* Batasi lebar teks agar mudah dibaca */
        /* width: auto; atau biarkan default */
    }

    .story-video-section-overlay .video-text-content-overlay h2 {
        font-family: var(--font-primary, 'Playfair Display', serif);
        text-shadow: 2px 2px 6px rgba(0, 0, 0, 0.75); /* Shadow lebih tebal */
        font-size: 3.5rem; /* Dari desain Anda */
        font-weight: 700;  /* Bootstrap fw-bolder */
        margin-bottom: 1rem; /* Jarak antara judul dan paragraf */
        color: #fff !important;
        font-style: italic;
    }

    .story-video-section-overlay .video-text-content-overlay p.lead {
        font-family: var(--font-secondary, 'Lora', serif);
        text-shadow: 1px 1px 5px rgba(0, 0, 0, 0.65);
        line-height: 1.7;
        font-size: 1.2rem; /* Dari desain Anda */
        margin-bottom: 0;
        color: #fff !important;
    }

    /* JOIN THE MOVEMENT SECTION STYLING */
   .join-movement-section-custom {
        min-height: auto; /* Konten menentukan tinggi */
        background-color: var(--color-bg-join-movement, #FFFFFF);
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .join-movement-section-custom .section-title-join{ /* Kelas khusus untuk judul */
        font-family: var(--font-primary);
        color: var(--color-text-dark, #333);
        font-size: 2.5rem; /* Sesuaikan */
        font-weight: 700;
        /* margin-bottom: 3.5rem; Jarak ke item fitur */
        margin-top: 3.5rem; /* Jarak atas */
    }

    .join-movement-section-custom .section-title-join .join-title-bold-italic {
        font-weight: 700; /* Atau bold */
        font-style: italic;
    }

    .join-movement-section-custom .section-title-join .join-title-italic {
        font-weight: 400; /* Atau normal, atau weight yang lebih ringan jika font mendukung */
        font-style: italic;
    }

    .join-movement-section-custom .feature-item-join { /* Kelas khusus untuk item */
        margin: 3rem; /* Jarak antar item di mobile */
    }

    .join-movement-section-custom .feature-icon-join { /* Kelas khusus untuk ikon */
        max-height: 200px; /* Sesuaikan ukuran ikon */
        margin-bottom: 1rem;
    }

    .join-movement-section-custom .feature-title-join { /* Kelas khusus untuk judul fitur */
        font-family: var(--font-secondary);
        color: var(--color-text-dark, #333);
        font-size: 1.1rem; /* Sesuaikan */
        font-weight: 600; /* Sedikit tebal */
    }

    .join-movement-section-custom .btn-join-artisan { /* Kelas khusus untuk tombol */
        background-color: var(--color-brand, #B9A077);
        color: white;
        padding: 0.7rem 2.5rem; /* Padding tombol */
        border-radius: 4px;
        text-transform: uppercase;
        font-weight: 500;
        letter-spacing: 0.5px;
        border: none;
        transition: background-color 0.3s ease;
        margin-top: 0.5rem; /* Jarak dari item fitur */
    }
    .join-movement-section-custom .btn-join-artisan:hover {
        background-color: #a58e6a;
    }

    /* LATEST STORIES SECTION STYLING */
    .latest-stories-custom { /* Nama kelas baru */
        min-height: 80vh; /* Atau sesuaikan kebutuhan */
        background-color:rgb(255, 255, 255); /* Warna background abu-abu muda seperti desain */
        padding-top: var(--section-padding-y, 4rem);
        /* padding-bottom: var(--section-padding-y, 4rem); */
        position: relative; /* Untuk awan jika ada */
        overflow: hidden; /* Untuk awan jika ada */
        margin-top: 2rem; /* Jarak dari section sebelumnya */
    }

    .latest-stories-custom .section-title-stories { /* Kelas khusus untuk judul */
        font-family: var(--font-primary);
        color: var(--color-text-dark, #212529);
        font-size: 2.8rem; /* Sesuaikan */
        font-weight: 700;
    }

    .link-styled-as-button-text {
        color: var(--color-brand, #B9A077); /* Warna teks diambil dari variabel warna brand (warna background tombol) */
        font-family: var(--font-secondary, 'Lora', serif); /* Sesuaikan font jika perlu */
        font-weight: 600; /* Sedikit tebal agar menonjol seperti link penting */
        text-decoration: none; /* Hilangkan underline default link */
        text-transform: uppercase; /* Jika ingin tetap uppercase seperti tombol */
        letter-spacing: 0.5px; /* Jika ingin tetap ada letter-spacing */
        font-size: 0.95rem; /* Sesuaikan ukuran font */
        padding: 0.5rem 0; /* Beri sedikit padding vertikal agar mudah diklik dan align */
        transition: color 0.2s ease-in-out, opacity 0.2s ease-in-out;
        /* Bisa juga ditambahkan border-bottom saat hover jika ingin efek underline kustom */
    }

    .link-styled-as-button-text:hover {
        color: #a58e6a; /* Warna hover yang sedikit lebih gelap dari --color-brand */
        /* opacity: 0.8; */ /* Alternatif efek hover */
        /* text-decoration: underline; */ /* Jika ingin underline saat hover */
    }

    .latest-stories-custom .article-card-featured { /* Card besar di kiri */
        border: none;
        box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,.075); /* shadow-sm */
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .latest-stories-custom .article-card-featured:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0,0,0,.15) !important;
    }
    .latest-stories-custom .article-card-featured .card-img-top {
        height: 380px; /* Sesuaikan tinggi gambar utama */
        object-fit: cover;
        border-top-left-radius: 0.375rem; /* Bootstrap card radius */
        border-top-right-radius: 0.375rem;
    }
    .latest-stories-custom .article-card-featured .card-body {
        padding: 1.5rem;
    }
    .latest-stories-custom .article-card-featured .card-title a {
        font-family: var(--font-primary);
        color: var(--color-text-dark, #212529);
        font-weight: 700;
        font-size: 1.5rem; /* Sesuaikan */
        text-decoration: none;
    }
    .latest-stories-custom .article-card-featured .card-title a:hover {
        color: var(--color-brand, #B9A077);
    }
    .latest-stories-custom .article-card-featured .card-text.text-muted.small {
        font-family: var(--font-secondary);
        font-size: 0.8rem;
    }
    .latest-stories-custom .article-card-featured .card-text.article-excerpt {
        font-family: var(--font-secondary);
        font-size: 0.95rem;
        color: #495057;
        line-height: 1.6;
        /* Batasi jumlah baris jika perlu (CSS trick) */
        display: -webkit-box;
        -webkit-line-clamp: 5; /* Tampilkan maksimal 5 baris */
        -webkit-box-orient: vertical;
        overflow: hidden;
    }


    .latest-stories-custom .article-card-small { /* Card kecil di kanan */
        border: none;
        box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,.075);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        background-color: #fff; /* Pastikan ada background jika section abu-abu */
    }
    .latest-stories-custom .article-card-small:hover {
        transform: translateY(-3px);
        box-shadow: 0 0.3rem 0.8rem rgba(0,0,0,.12) !important;
    }
    .latest-stories-custom .article-card-small .article-img-small {
        height: 100%;
        min-height: 100px; /* Sesuaikan tinggi minimal gambar kecil */
        object-fit: cover;
        border-top-left-radius: 0.375rem;
        border-bottom-left-radius: 0.375rem;
    }
    .latest-stories-custom .article-card-small .card-body {
        padding: 0.75rem 1rem; /* Padding lebih kecil untuk card kecil */
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    .latest-stories-custom .article-card-small .card-title a {
        font-family: var(--font-primary);
        color: var(--color-text-dark, #212529);
        font-weight: 600;
        font-size: 1rem; /* Sesuaikan */
        text-decoration: none;
        display: -webkit-box;
        -webkit-line-clamp: 2; /* Maksimal 2 baris untuk judul kecil */
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .latest-stories-custom .article-card-small .card-title a:hover {
        color: var(--color-brand, #B9A077);
    }
    .latest-stories-custom .article-card-small .card-text.text-muted.small {
        font-family: var(--font-secondary);
        font-size: 0.75rem;
    }


    /* Responsivitas */
    @media (max-width: 991.98px) { /* Tablet */
        .story-video-section-overlay .video-text-content-overlay h2 { font-size: 3rem; }
        .story-video-section-overlay .video-text-content-overlay p.lead { font-size: 1.1rem; }
        .story-video-section-overlay .video-text-content-overlay .text-top { padding-top: 12vh; }
        .story-video-section-overlay .video-text-content-overlay .text-bottom { padding-bottom: 12vh; }
    }
    @media (max-width: 767.98px) { /* Mobile besar */
        .story-video-section-overlay .video-text-content-overlay h2 { font-size: 2.2rem; }
        .story-video-section-overlay .video-text-content-overlay p.lead { font-size: 1rem; max-width: 90%; }
        .story-video-section-overlay .video-text-content-overlay .text-top { padding-top: 10vh; }
        .story-video-section-overlay .video-text-content-overlay .text-bottom { padding-bottom: 10vh; }
    }
    @media (max-width: 575.98px) { /* Mobile kecil */
        .story-video-section-overlay .video-text-content-overlay h2 { font-size: 1.8rem; }
        .story-video-section-overlay .video-text-content-overlay p.lead { font-size: 0.9rem; }
        .story-video-section-overlay .video-text-content-overlay .text-top { padding-top: 8vh; }
        .story-video-section-overlay .video-text-content-overlay .text-bottom { padding-bottom: 8vh; }
    }

</style>
@endpush

    <!-- Hero Section -->
    <section class="hero-section-custom"> {{-- Ganti kelas, hapus kelas Bootstrap min-vh-100 dan py-5 jika diatur CSS --}}

        {{-- Motif Awan Dekoratif --}}
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
            <div class="row align-items-center">
                {{-- Kolom Teks --}}
                <div class="col-lg-5 col-md-6 hero-text-content order-md-1 order-2">
                    <h1 class="fw-bold">
                        <span class="hero-title-italic">Threads of</span> <span class="hero-title-bold-italic">Heritage,</span><br>
                        <span class="hero-title-italic">Woven for the</span> <span class="hero-title-bold-italic">Future</span>
                    </h1>
                    <p class="lead fs-5">
                        Bringing Indonesia's traditional fabrics to the world through
                        innovation and artistry.
                    </p>
                    <div class="hero-button-wrapper"> {{-- Pastikan wrapper ini di-align oleh .hero-text-content --}}
                        <a href="#" class="btn btn-kainara">Discover Our Collection</a>
                    </div>
                </div>


                {{-- Kolom Gambar Model --}}
                <div class="col-lg-7 col-md-6 hero-image-container text-center text-md-end order-md-2 order-1 mb-4 mb-md-0">
                    {{-- Penambahan lebar kolom gambar (misal dari col-lg-6 ke col-lg-7) --}}
                    {{-- text-md-start bisa digunakan jika ingin gambar rata kiri di kolomnya pada layar medium ke atas --}}
                    <img src="{{ asset('images/batik-couple.png') }}" alt="Kainara Models" class="img-fluid hero-model-img">
                </div>
            </div>
        </div>
    </section>


    <section class="story-video-section-overlay">
        <div class="video-bg-wrapper-overlay">
            <video playsinline="playsinline" autoplay="autoplay" muted="muted" loop="loop" id="storyOverlayVideo">
                <source src="{{ asset('videos/batik_story_video.mp4') }}" type="video/mp4">
                Your browser does not support the video tag.
            </video>
            <div class="video-overlay-layer"></div>
        </div>

        <div class="video-text-content-overlay container d-flex flex-column"> {{-- Tambah d-flex flex-column --}}
            <div class="text-top text-center"> {{-- Wrapper untuk teks atas --}}
                <h2 class="display-5 fw-bolder">"Every Pattern Tells a Story"</h2>
            </div>
            <div class="text-bottom text-center mt-auto"> {{-- Wrapper untuk teks bawah, mt-auto untuk mendorong ke bawah --}}
                <p class="lead fs-5 mx-auto" style="max-width: 650px;">
                    Go behind the scenes of Indonesian batik craftsmanship
                    where heritage, patience, and passion are woven into every thread.
                </p>
            </div>
        </div>
    </section>

    <section class="join-movement-section-custom"> {{-- Ganti kelas, hapus kelas utilitas Bootstrap jika diatur CSS --}}
        <div class="container">
            <h2 class="section-title-join">
                 <span class="join-title-bold-italic">Join the Movement.</span><span class="join-title-italic"> Share Your Craft with the World</span>
            </h2>
            <div class="row justify-content-center"> {{-- Pusatkan kolom jika tidak full --}}
                <div class="col-md-4 col-lg-3 feature-item-join"> {{-- Perkecil kolom sedikit agar ada spasi --}}
                    <img src="{{ asset('images/icon-signup.png') }}" alt="Sign Up Easily" class="img-fluid feature-icon-join">
                    <h5 class="feature-title-join">Sign Up Easily</h5>
                </div>
                <div class="col-md-4 col-lg-3 feature-item-join">
                    <img src="{{ asset('images/icon-preserve.png') }}" alt="Preserve Local Heritage" class="img-fluid feature-icon-join">
                    <h5 class="feature-title-join">Preserve Local Heritage</h5>
                </div>
                <div class="col-md-4 col-lg-3 feature-item-join">
                    <img src="{{ asset('images/icon-market.png') }}" alt="Reach a Wide Market" class="img-fluid feature-icon-join">
                    <h5 class="feature-title-join">Reach a Wide Market</h5>
                </div>
            </div>
            <div class="join-button-motif-wrapper text-center position-relative mt-4">
                <a href="#" class="btn btn-join-artisan">Join as Artisan</a>
            </div>
        </div>
        <div class="decorative-motif motif-latest-top-left">
            <img src="{{ asset('images/motif-batik.png') }}" alt="Motif Batik">
        </div>
        {{-- Motif Awan Bawah Kanan untuk Latest Stories --}}
        <div class="decorative-motif motif-latest-top-right">
            <img src="{{ asset('images/motif-batik.png') }}" alt="Motif Batik">
        </div>
    </section>
    {{-- AKHIR SECTION "JOIN THE MOVEMENT" --}}



    {{-- AWAL SECTION "LATEST STORIES" --}}
    <section class="latest-stories-custom"> {{-- Hapus kelas utilitas Bootstrap jika diatur CSS --}}

        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-4 pb-2">
                <h2 class="section-title-stories">Latest Stories</h2>
                <a href="#" class="link-styled-as-button-text">Read more articles!</a>
            </div>

            <div class="row">
                <!-- Featured Article (Kiri) -->
                <div class="col-lg-7 mb-4 mb-lg-0">
                    <div class="card article-card-featured h-100"> {{-- h-100 dari Bootstrap agar card sama tinggi jika perlu --}}
                        <a href="#">
                            <img src="{{ asset('images/articles/article-featured.jpg') }}" class="card-img-top" alt="Featured Article Image">
                        </a>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title mb-2">
                                <a href="#">The Gleaming Legacy: Songket, Indonesia's Golden Fabric</a>
                            </h5>
                            <p class="card-text text-muted small mb-3">21 May 2025</p>
                            <p class="card-text article-excerpt mb-auto"> {{-- mb-auto mendorong elemen berikutnya (jika ada) ke bawah --}}
                                Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. Lorem ipsum dolor sit amet...
                            </p>
                            {{-- Jika ingin tombol Read More di card besar: --}}
                            {{-- <a href="#" class="btn btn-sm btn-link p-0 mt-3 align-self-start text-decoration-none" style="color: var(--color-brand);">Read More →</a> --}}
                        </div>
                    </div>
                </div>

                <!-- List of Other Articles (Kanan) -->
                <div class="col-lg-5">
                    {{-- Artikel Kecil 1 --}}
                    <div class="card article-card-small mb-3">
                        <div class="row g-0">
                            <div class="col-4"> {{-- Gunakan col-4 atau col-sm-4 agar lebih sempit --}}
                                <a href="#">
                                    <img src="{{ asset('images/articles/article-small-1.jpg') }}" class="img-fluid rounded-start article-img-small" alt="Article Image 1">
                                </a>
                            </div>
                            <div class="col-8">
                                <div class="card-body">
                                    <h6 class="card-title mb-1">
                                        <a href="#">The Gleaming Legacy: Songket, Indonesia's Golden Fabric</a>
                                    </h6>
                                    <p class="card-text text-muted small">21 May 2025</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Artikel Kecil 2 --}}
                    <div class="card article-card-small mb-3">
                        <div class="row g-0">
                            <div class="col-4">
                                <a href="#">
                                    <img src="{{ asset('images/articles/article-small-2.jpg') }}" class="img-fluid rounded-start article-img-small" alt="Article Image 2">
                                </a>
                            </div>
                            <div class="col-8">
                                <div class="card-body">
                                    <h6 class="card-title mb-1">
                                        <a href="#">The Gleaming Legacy: Songket, Indonesia's Golden Fabric</a>
                                    </h6>
                                    <p class="card-text text-muted small">20 May 2025</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Artikel Kecil 3 --}}
                    <div class="card article-card-small">
                        <div class="row g-0">
                            <div class="col-4">
                                <a href="#">
                                    <img src="{{ asset('images/articles/article-small-3.jpg') }}" class="img-fluid rounded-start article-img-small" alt="Article Image 3">
                                </a>
                            </div>
                            <div class="col-8">
                                <div class="card-body">
                                    <h6 class="card-title mb-1">
                                        <a href="#">The Gleaming Legacy: Songket, Indonesia's Golden Fabric</a>
                                    </h6>
                                    <p class="card-text text-muted small">19 May 2025</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    {{-- AKHIR SECTION "LATEST STORIES" --}}

@endsection