<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kainara - @yield('title', 'Welcome')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400..700;1,400..700&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            /* Variabel untuk ukuran logo yang berputar (digunakan oleh kelas .logo-size-*) */
            --logo-height-medium: 250px;  /* Contoh ukuran sedang */
            --logo-height-large: 170px;   /* Contoh ukuran besar */

            --target-icon-size: 24px;
            --header-actual-height: 90px; /* TARGET TINGGI BAR HEADER (tanpa memperhitungkan logo yang meluber) */

            --font-primary: 'Playfair Display', serif;
            --font-secondary: 'Lora', serif;
            --color-brand: #AD9D6C;
            --color-text-dark: #000000;
            --color-text-nav: #000000;
            --color-text-light: #f8f9fa;
            --color-bg-header: #ffffff;
            --color-bg-footer: #000000;
            --header-shadow: 0 .125rem .25rem rgba(0,0,0,.075); /* Shadow dari Bootstrap .shadow-sm */
            --footer-logo-height: 250px;
            --footer-padding-y: 3rem;

            /* --color-brand: #AD9D6C; Warna emas/coklat untuk dropdown */
            --color-dropdown-text: #FFFFFF; /* Warna teks di dropdown */
            --color-dropdown-hover-bg: #9a8a5e; /* Warna background hover item dropdown (lebih gelap dari brand) */
            --header-dropdown-border-radius: 15px; /* Sesuai desain */

            /* New custom color variable for the sidebar */
            --color-bg-sidebar: #B6B09F;
        }

        body {
            font-family: var(--font-secondary);
            color: var(--color-text-dark);
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

        /* Menghilangkan panah default Bootstrap pada dropdown toggle jika ikon sudah cukup */
        header .profile-dropdown > .dropdown-toggle::after {
            display: none; /* Sembunyikan panah Bootstrap */
        }

        header .nav-group-left .dropdown > .dropdown-toggle::after {
            margin-left: 0.35em; /* Jarak antara teks "Store" dan panah */
        }

        /* Styling Khusus untuk Dropdown Profil (jika perlu) */
        header .profile-dropdown .dropdown-menu {
            min-width: 10rem; /* Atur lebar minimal dropdown profil jika perlu */
        }

        /* Styling untuk link navigasi teks agar konsisten dengan ikon dropdown */
        header .nav-group-left .nav-link,
        header .icon-group-right .nav-icon-link {
            /* Pastikan padding dan line-height konsisten agar alignment vertikal baik */
            /* Sudah diatur di CSS header sebelumnya, cek kembali jika perlu */
        }
        header .nav-group-left .dropdown > .nav-link {
            /* Jika ingin styling khusus pada "Store" toggle */
        }

        header #logo-rotator {
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            z-index: 1031;
        }
        /* Kelas untuk ukuran logo yang berputar */
        header img#rotating-logo.logo-size-medium { height: var(--logo-height-medium); width: auto; display: block; vertical-align: middle; }
        header img#rotating-logo.logo-size-large { height: var(--logo-height-large); width: auto; display: block; vertical-align: middle; }
        /* Fallback jika tidak ada kelas ukuran (opsional, bisa juga diatur di JS) */
        header img#rotating-logo {
            width: auto; display: block; vertical-align: middle;
        }

        header .icon-group-right .nav-icon-link {
            display: inline-flex;
            align-items: center;
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

        ---

        /* Custom Sidebar Styles */
        /*
           To apply the background color #B6B09F to a sidebar,
           first define a class for it, and then apply it to your sidebar HTML element.
           If your sidebar is always present, you can target its ID or a unique class.
        */
        .custom-sidebar {
            background-color: var(--color-bg-sidebar); /* Using the new CSS variable */
            /* Add other sidebar styles here, e.g., width, padding, etc. */
            width: 250px; /* Example width */
            padding: 1rem;
            color: #ffffff; /* Example text color for sidebar */
        }
        /* Example for a Bootstrap offcanvas sidebar */
        .offcanvas.offcanvas-start {
             background-color: var(--color-bg-sidebar);
             color: #ffffff;
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

    <header>
        <div class="container">
            {{-- Grup Navigasi Kiri --}}
            <div class="nav-group-left">
                <div class="dropdown header-dropdown">
                    <a class="nav-link fw-bold dropdown-toggle" href="#" role="button" id="storeDropdown" data-bs-toggle="dropdown" aria-expanded="false">Store</a>
                    <ul class="dropdown-menu" aria-labelledby="storeDropdown">
                        <li><a class="dropdown-item" href="#">Men</a></li>
                        <li><a class="dropdown-item" href="#">Women</a></li>
                        <li><a class="dropdown-item" href="#">Fabric</a></li>
                    </ul>
                </div>
                <a href="#" class="nav-link fw-bold">Article</a>
                <a href="#" class="nav-link fw-bold">My Order</a>
                <a href="#" class="nav-link fw-bold">About Us</a>
            </div>

            <div id="logo-rotator" class="text-center">
                <img id="rotating-logo"
                    src="{{ $logosData[0]['src'] }}"
                    alt="Kainara Logo"
                    class="{{ $logosData[0]['class'] }}">
            </div>

            {{-- Grup Ikon Kanan --}}
            <div class="icon-group-right">
                <a href="#" class="nav-icon-link">
                    <img src="{{ asset('images/icons/icon-cart.png') }}" alt="Cart" class="header-icon">
                </a>
                {{-- Dropdown untuk Ikon Profil --}}
                <div class="dropdown header-dropdown profile-dropdown">
                    <a href="#" class="nav-icon-link dropdown-toggle" role="button" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="{{ asset('images/icons/icon-account.png') }}" alt="Account" class="header-icon">
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                        <li><a class="dropdown-item" href="#">Login</a></li>
                        <li><a class="dropdown-item" href="#">Register</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </header>

    {{-- Main content goes here --}}
    <main>
        @yield('content')
    </main>

    {{-- Optional: Footer if you have one --}}
    <footer>
        {{-- Your footer content --}}
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    @stack('scripts') {{-- Tambahkan ini untuk JS spesifik halaman --}}

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Your existing JavaScript for logo rotation or other functionalities
            const rotatingLogo = document.getElementById('rotating-logo');
            const logosData = @json($logosData);
            let currentLogoIndex = 0;

            function rotateLogo() {
                currentLogoIndex = (currentLogoIndex + 1) % logosData.length;
                const nextLogo = logosData[currentLogoIndex];
                rotatingLogo.src = nextLogo.src;
                rotatingLogo.className = nextLogo.class; // Update class for size
            }

            // Example: Rotate logo every 5 seconds
            // setInterval(rotateLogo, 5000);
        });
    </script>
</body>
</html>