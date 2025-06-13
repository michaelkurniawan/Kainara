<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kainara - @yield('title', 'Welcome')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Ancizar+Serif:ital,wght@0,300..900;1,300..900&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Bootstrap Icons CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            /* Variabel untuk ukuran logo yang berputar (digunakan oleh kelas .logo-size-*) */
            --logo-height-medium: 250px;  /* Contoh ukuran sedang */
            --logo-height-large: 170px;   /* Contoh ukuran besar */

            --target-icon-size: 24px;
            --header-actual-height: 90px; /* TARGET TINGGI BAR HEADER (tanpa memperhitungkan logo yang meluber) */

            --font-primary: 'Ancizar Serif', serif;
            --font-secondary: 'Ancizar Serif', serif;
            --color-brand: #AD9D6C;
            --color-text-dark: #000000;
            --color-text-nav: #000000;
            --color-text-light: #f8f9fa;
            --color-bg-header: #ffffff;
            --color-bg-footer: #000000;
            --header-shadow: 0 .125rem .25rem rgba(0,0,0,.075); /* Shadow dari Bootstrap .shadow-sm */
            --footer-logo-height: 250px;
            --footer-padding-y: 1rem;
            
            /* --color-brand: #AD9D6C; Warna emas/coklat untuk dropdown */
            --color-dropdown-text: #FFFFFF; /* Warna teks di dropdown */
            --color-dropdown-hover-bg: #9a8a5e; /* Warna background hover item dropdown (lebih gelap dari brand) */
            --header-dropdown-border-radius: 15px; /* Sesuai desain */
        }

        body {
            font-family: var(--font-secondary);
            color: var(--color-text-dark);
            /* padding-top: calc(var(--header-actual-height) + 2px); PENTING: Sesuaikan berdasarkan --header-actual-height */
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        main {
            flex-grow: 1;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: var(--font-primary);
        }

        /* Styling Header */
        header {
            background-color: var(--color-bg-header);
            box-shadow: var(--header-shadow);
            position: sticky;
            top: 0;
            z-index: 1030;
            width: 100%;
            height: var(--header-actual-height); /* Tinggi bar header */
            display: flex;
            align-items: center;
        }

        header .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            position: relative; /* Untuk logo absolut */
        }

        header .nav-group-left,
        header .icon-group-right {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        header .icon-group-right .nav-icon-link,
        header .icon-group-right .dropdown { /* Target div dropdown profil juga */
            display: flex; /* Jadikan link dan div dropdown sebagai flex item juga */
            align-items: center; /* Pusatkan konten di dalamnya (gambar ikon) */
        }

        header .nav-group-left .nav-link:hover {
            color: var(--color-brand);
        }

        /* Styling Umum untuk Dropdown di Header */
        header .header-dropdown .dropdown-menu {
            background-color: var(--color-brand); /* Warna background dropdown */
            border: none; /* Hilangkan border default Bootstrap */
            border-radius: var(--header-dropdown-border-radius); /* Rounded corner sesuai desain */
            box-shadow: 0 0.25rem 0.75rem rgba(0,0,0,0.15); /* Shadow halus */
            padding: 0.5rem 0; /* Padding atas/bawah internal menu */
            margin-top: 0.75rem !important; /* Beri sedikit jarak dari toggle (override Bootstrap) */
        }

        header .header-dropdown .dropdown-item {
            color: var(--color-dropdown-text); /* Warna teks item */
            padding: 0.6rem 1.5rem; /* Padding item */
            font-family: var(--font-primary); /* Font yang lebih elegan untuk item dropdown */
            font-weight: 600; /* Sedikit tebal */
            font-size: 1.1rem; /* Ukuran font item dropdown */
            transition: background-color 0.2s ease-in-out, color 0.2s ease-in-out;
        }

        header .header-dropdown .dropdown-item:hover,
        header .header-dropdown .dropdown-item:focus {
            background-color: var(--color-dropdown-hover-bg); /* Warna background saat hover */
            color: var(--color-dropdown-text); /* Warna teks tetap sama atau sedikit berubah */
        }

        header .nav-group-left .dropdown > .dropdown-toggle::after {
            /* Contoh: Mengubah warna panah agar serasi dengan teks navigasi */
            /* border-top-color: var(--color-text-nav); */
            /* vertical-align: 0.15em; */ /* Sedikit penyesuaian vertikal jika perlu */
            margin-left: 0.4em; /* Jarak antara teks "Store" dan panah */
            vertical-align: 0.1em; 
        }

        header .nav-group-left .dropdown > a.nav-link.dropdown-toggle::after {
            margin-left: 0.3em;  /* Jarak antara teks "Store" dan panah */
            margin-top: 0.2em;
            /* vertical-align: 100em; /* COBA SESUAIKAN NILAI INI (misal: 0, 0.05em, 0.15em, -0.05em, atau 'middle') */
            transition: transform 0.25s ease-in-out; /* Transisi untuk animasi balik panah */
            /* border-top-color: var(--color-text-nav); // Jika ingin mengubah warna panah */
        }

        /* Styling untuk link navigasi teks agar konsisten dengan ikon dropdown */
        header .nav-group-left .nav-link,
        header .icon-group-right .nav-icon-link {
            /* Pastikan padding dan line-height konsisten agar alignment vertikal baik */
            /* Sudah diatur di CSS header sebelumnya, cek kembali jika perlu */
        }
        header .nav-group-left .dropdown > .nav-link.dropdown-toggle {
            display: inline-flex; /* 1. Jadikan link sebagai flex container */
            align-items: center;
        }


        header #logo-rotator {
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            z-index: 1031;
            /* Penting: Beri dimensi pada #logo-rotator agar gambar di dalamnya bisa diposisikan absolut relatif terhadapnya */
            /* Kita akan set width dan height berdasarkan logo terbesar atau target yang diinginkan */
            width: var(--logo-height-large); /* Ambil dari variabel logo terbesar, atau set nilai tetap */
            height: var(--logo-height-large); /* Ambil dari variabel logo terbesar, atau set nilai tetap */
            display: flex; /* Untuk memusatkan gambar di dalamnya jika perlu */
            align-items: center;
            justify-content: center;
        }

        header #logo-rotator .logo-image {
            position: absolute; /* Agar bisa ditumpuk */
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%); /* Pusatkan gambar di dalam #logo-rotator */
            opacity: 0; /* Awalnya semua transparan */
            transition: opacity 0.5s ease-in-out; /* Transisi untuk efek fade */
            /* Tinggi dan lebar akan diatur oleh kelas spesifik ukuran (.logo-size-*) */
            /* Atau jika Anda tidak menggunakan kelas ukuran, atur height di sini dan JS akan menimpanya jika perlu */
            /* height: var(--logo-height-medium); */
            width: auto;
            display: block; /* atau inline-block dengan vertical-align middle */
            /* vertical-align: middle; */
        }

        header #logo-rotator .logo-image.active {
            opacity: 1; /* Logo aktif terlihat penuh */
        }
        
        /* Kelas untuk ukuran logo yang berputar */
        header img.logo-size-medium { height: var(--logo-height-medium, 70px) !important; }
        header img.logo-size-large { height: var(--logo-height-large, 90px) !important; }

        /* Fallback jika tidak ada kelas ukuran (opsional, bisa juga diatur di JS) */
        header img#rotating-logo {
             /* Default height jika tidak ada kelas, misal: */
             /* height: var(--logo-height-medium); */
             width: auto; display: block; vertical-align: middle;
        }


        header .icon-group-right .nav-icon-link {
            display: inline-flex;
            align-items: center;
            /* height: var(--target-logo-height); Hapus jika tidak relevan dengan tinggi logo yang meluber */
        }
        header .icon-group-right .header-icon {
            height: var(--target-icon-size);
            width: auto;
            display: block;
            transition: opacity 0.2s ease-in-out;
        }
        header .icon-group-right .nav-icon-link:hover .header-icon {
            opacity: 0.7;
        }

        /* Styling Footer (biarkan seperti sebelumnya jika sudah OK) */
        /* STYLING FOOTER BARU */
       .site-footer {
            background-color: var(--color-bg-footer, #121212);
            color: var(--color-text-muted-footer, #adb5bd);
            padding-top: var(--footer-padding-y, 3rem); /* Padding atas footer utama */
            padding-bottom: 1.5rem; /* Padding bawah keseluruhan footer dikurangi sedikit */
            font-family: var(--font-secondary);
            font-size: 0.9rem;
            line-height: 1.7;
        }

        .site-footer .footer-logo {
            max-height: var(--footer-logo-height, 70px); /* Sesuaikan jika perlu */
        }

        .site-footer .footer-heading {
            font-family: var(--font-primary);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 0.9rem;
            color: var(--color-text-light, #f8f9fa);
            margin-bottom: 1.25rem;
        }

        .site-footer .footer-links li {
            margin-bottom: 0.6rem;
        }

        .site-footer .footer-links a {
            color: var(--color-text-muted-footer, #adb5bd);
            text-decoration: none;
            transition: color 0.2s ease-in-out, padding-left 0.2s ease-in-out;
        }

        .site-footer .footer-links a:hover {
            color: var(--color-brand, #AD9D6C);
            padding-left: 5px;
        }

        .site-footer .footer-links a .bi {
            margin-right: 0.5rem;
            font-size: 1rem;
            vertical-align: -0.1em; /* Alignment ikon sedikit lebih baik */
        }

        .site-footer .address-text {
            margin-bottom: 0;
            font-size: 0.85rem; /* Ukuran font alamat bisa sedikit lebih kecil */
            line-height: 1.6;   /* Tambahkan line-height agar teks multi-baris lebih mudah dibaca */
            max-width: 300px
        }

        /* Styling untuk baris bawah footer (kontak, no.telp, copyright) */
        .site-footer .footer-bottom-row {
            border-top: 1px solid rgba(255, 255, 255, 0.1); /* Garis pemisah */
            padding-top: 1.5rem;  /* Jarak dari garis ke teks */
            margin-top: 2.5rem;   /* Jarak dari konten footer di atasnya ke garis pemisah */
            /* align-items-center sudah dari HTML */
        }

        .site-footer .contact-info a {
            color: var(--color-text-muted-footer, #adb5bd);
            text-decoration: none;
            display: inline-block;
            padding-bottom: 2px;
            border-bottom: 1px solid var(--color-text-muted-footer, #adb5bd);
            transition: color 0.2s ease-in-out, border-bottom-color 0.2s ease-in-out;
            font-size: 0.85rem; /* Ukuran font kontak */
        }

        .site-footer .contact-info a:hover {
            color: var(--color-brand, #AD9D6C);
            border-bottom-color: var(--color-brand, #AD9D6C);
        }

        .site-footer .footer-copyright small {
            color: var(--color-text-muted-footer, #adb5bd);
            opacity: 0.9;
            font-size: 0.8rem; /* Ukuran font copyright */
        }


    </style>
    @stack('styles') {{-- Tambahkan ini untuk CSS spesifik halaman --}}
</head>
<body>
    @php
        $logosData = [
            ['src' => asset('storage/logo1.png'), 'class' => 'logo-size-medium'],
            ['src' => asset('storage/logo2.png'), 'class' => 'logo-size-large'],
        ];
    @endphp

    <!-- Header -->
    <header>
        <div class="container"> {{-- Hapus kelas d-flex dll dari sini, sudah diatur oleh CSS header .container --}}
            {{-- Grup Navigasi Kiri --}}
            <div class="nav-group-left">
                <div class="dropdown header-dropdown"> {{-- Tambah kelas .header-dropdown --}}
                    <a class="nav-link fw-bold dropdown-toggle" href="#" role="button" id="storeDropdown" data-bs-toggle="dropdown" aria-expanded="false">Store</a>
                    <ul class="dropdown-menu" aria-labelledby="storeDropdown">
                        <li><a class="dropdown-item" href="#">Men</a></li>
                        <li><a class="dropdown-item" href="#">Women</a></li> {{-- Ganti urutan/nama jika perlu --}}
                        <li><a class="dropdown-item" href="#">Fabric</a></li>
                    </ul>
                </div>
                <a href="#" class="nav-link fw-bold">Stories</a>
                <a href="#" class="nav-link fw-bold">My Order</a>
                <a href="#" class="nav-link fw-bold">About Us</a>
            </div>

            <div id="logo-rotator" class="text-center">
                {{-- Gambar untuk logo saat ini --}}
                <img id="rotating-logo-current"
                    src="{{ $logosData[0]['src'] }}"
                    alt="Kainara Logo"
                    class="{{ $logosData[0]['class'] }} logo-image active"> {{-- Tambah kelas 'active' --}}

                {{-- Gambar untuk logo berikutnya (awalnya tersembunyi) --}}
                <img id="rotating-logo-next"
                    src="" {{-- Akan diisi oleh JavaScript --}}
                    alt="Kainara Logo"
                    class="logo-image"> {{-- Kelas umum untuk styling --}}
            </div>

            {{-- Grup Ikon Kanan --}}
            <div class="icon-group-right">
                <a href="#" class="nav-icon-link">
                    <img src="{{ asset('images/icons/icon-cart.png') }}" alt="Cart" class="header-icon">
                </a>
                {{-- Dropdown untuk Ikon Profil --}}
                {{-- Ikon Profil (Tanpa Dropdown) --}}
                <a href="#" class="nav-icon-link" title="My Account"> {{-- Tambahkan title untuk aksesibilitas atau href ke halaman profil --}}
                    <img src="{{ asset('images/icons/icon-account.png') }}" alt="Account" class="header-icon">
                </a>
            </div>
        </div>
    </header>

    <main class="py-4">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="site-footer">
        <div class="container">
            <div class="row gy-4 justify-content-between mt-3""> {{-- Tambah justify-content-between --}}
                {{-- Kolom Logo --}}
                <div class="col-lg-3 col-md-12 text-center text-lg-start footer-logo-section mb-4 mb-lg-0">
                    <img src="{{ asset('storage/logo3.png') }}" alt="Kainara Footer Logo" class="footer-logo">
                </div>

                {{-- Kolom Menu --}}
                <div class="col-lg-2 col-md-3 col-6">
                    <h6 class="footer-heading">MENU</h6>
                    <ul class="list-unstyled footer-links">
                        <li><a href="#">Woman</a></li>
                        <li><a href="#">Men</a></li>
                        <li><a href="#">My Order</a></li>
                        <li><a href="#">About Us</a></li>
                        <li><a href="#">Profile</a></li>
                    </ul>
                </div>

                {{-- Kolom Socials --}}
                <div class="col-lg-2 col-md-3 col-6">
                    <h6 class="footer-heading">SOCIALS</h6>
                    <ul class="list-unstyled footer-links">
                        <li><a href="#"><i class="bi bi-facebook"></i> Facebook</a></li>
                        <li><a href="#"><i class="bi bi-instagram"></i> Instagram</a></li>
                        <li><a href="#"><i class="bi bi-twitter"></i> Twitter</a></li>
                    </ul>
                </div>

                {{-- Kolom Alamat --}}
                <div class="col-lg-4 col-md-6 footer-address-section"> {{-- Perbesar sedikit kolom alamat --}}
                    <h6 class="footer-heading">BCA LEARNING INSTITUTE</h6>
                    <p class="address-text mb-0">Sentul City, Jl. Pakuan No.3, Sumur Batu, Babakan Madang, Bogor Regency, West Java 16810</p>
                </div>
            </div>

            {{-- BARIS BARU UNTUK KONTAK, NO.TELP, COPYRIGHT --}}
            <div class="row footer-bottom-row align-items-center">
                 <div class="col-md-4 text-center text-md-start mb-2 mb-md-0">
                    <p class="contact-info email-info mb-0"><a href="mailto:contact@kainara.co.id">contact@kainara.co.id</a></p>
                </div>
                <div class="col-md-4 text-center mb-2 mb-md-0">
                    <p class="contact-info phone-info mb-0"><a href="tel:+1234567890">(123) 456-7890</a></p>
                </div>
                <div class="col-md-4 text-center text-md-end">
                    <p class="footer-copyright mb-0"><small>© {{ date('Y') }} Kainara. All rights reserved.</small></p>
                </div>
            </div>
        </div>
    </footer>



    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <!-- Script Logo Rotator Anda -->
    <script>
        const logosDataForScriptOptimized = @json($logosData);
        let currentIndexOptimized = 0;
        const logoImg1 = document.getElementById('rotating-logo-current');
        const logoImg2 = document.getElementById('rotating-logo-next');
        const allSizeClassesOptimized = ['logo-size-small', 'logo-size-medium', 'logo-size-large']; // Sesuaikan

        if (logoImg1 && logoImg2 && logosDataForScriptOptimized && logosDataForScriptOptimized.length > 0) {
            // Inisialisasi logo pertama
            logoImg1.src = logosDataForScriptOptimized[0].src;
            allSizeClassesOptimized.forEach(cls => logoImg1.classList.remove(cls));
            if (logosDataForScriptOptimized[0].class) {
                logoImg1.classList.add(logosDataForScriptOptimized[0].class);
            }
            logoImg1.classList.add('active');
            logoImg2.classList.remove('active'); // Pastikan yang kedua tidak aktif

            let activeImage = logoImg1;
            let inactiveImage = logoImg2;

            setInterval(() => {
                currentIndexOptimized = (currentIndexOptimized + 1) % logosDataForScriptOptimized.length;
                const nextLogo = logosDataForScriptOptimized[currentIndexOptimized];

                // Siapkan gambar yang tidak aktif (inactiveImage)
                inactiveImage.src = nextLogo.src;
                allSizeClassesOptimized.forEach(cls => inactiveImage.classList.remove(cls));
                if (nextLogo.class) {
                    inactiveImage.classList.add(nextLogo.class);
                }

                // Mulai transisi
                activeImage.classList.remove('active'); // Fade out gambar yang aktif
                inactiveImage.classList.add('active');  // Fade in gambar yang baru

                // Tukar peran untuk iterasi berikutnya
                let temp = activeImage;
                activeImage = inactiveImage;
                inactiveImage = temp;

            }, 4000); // Ganti setiap 4 detik
        }

    </script>

    @stack('scripts')
</body>
</html>