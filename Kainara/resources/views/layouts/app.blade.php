<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kainara - @yield('title', 'Welcome')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Ancizar+Serif:ital,wght@0,300..900;1,300..900&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --logo-height-medium: 250px;
            --logo-height-large: 170px;

            --target-icon-size: 24px;
            --header-actual-height: 90px;

            --font-primary: 'Ancizar Serif', serif;
            --font-secondary: 'Ancizar Serif', serif;
            --color-brand: #AD9D6C;
            --color-text-dark: #000000;
            --color-text-nav: #000000;
            --color-text-light: #f8f9fa;
            --color-bg-header: #ffffff;
            --color-bg-footer: #000000;
            --header-shadow: 0 .125rem .25rem rgba(0,0,0,.075);
            --footer-logo-height: 250px;
            --footer-padding-y: 1rem;
            --color-dropdown-text: #FFFFFF;
            --color-dropdown-hover-bg: #9a8a5e;
            --header-dropdown-border-radius: 15px;
        }

        body, h1, h2, h3, h4, h5, h6, p, a, li, span, strong, em {
            font-family: 'AncizarSerif', serif;
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

        header {
            background-color: var(--color-bg-header);
            box-shadow: var(--header-shadow);
            position: sticky;
            top: 0;
            z-index: 1030;
            width: 100%;
            height: var(--header-actual-height);
            display: flex;
            align-items: center;
        }

        header .container-fluid {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            position: relative;
        }

        header .nav-group-left,
        header .icon-group-right {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        header .icon-group-right .nav-icon-link,
        header .icon-group-right .dropdown {
            display: flex;
            align-items: center;
        }

        header .nav-group-left .nav-link:hover {
            color: var(--color-brand);
        }

        header .header-dropdown .dropdown-menu {
            background-color: var(--color-brand);
            border: none;
            border-radius: var(--header-dropdown-border-radius);
            box-shadow: 0 0.25rem 0.75rem rgba(0,0,0,0.15);
            padding: 0.5rem 0;
            margin-top: 0.75rem !important;
        }

        header .header-dropdown .dropdown-item {
            color: var(--color-dropdown-text);
            padding: 0.6rem 1.5rem;
            font-family: var(--font-primary);
            font-weight: 600;
            font-size: 1.1rem;
            transition: background-color 0.2s ease-in-out, color 0.2s ease-in-out;
        }

        header .header-dropdown .dropdown-item:hover,
        header .header-dropdown .dropdown-item:focus {
            background-color: var(--color-dropdown-hover-bg);
            color: var(--color-dropdown-text);
        }

        header .nav-group-left .dropdown > .dropdown-toggle::after {
            margin-left: 0.4em;
            vertical-align: 0.1em;
        }

        header .nav-group-left .dropdown > a.nav-link.dropdown-toggle::after {
            margin-left: 0.3em;
            margin-top: 0.2em;
            transition: transform 0.25s ease-in-out;
        }

        header .nav-group-left .dropdown > .nav-link.dropdown-toggle {
            display: inline-flex;
            align-items: center;
        }

        header #logo-rotator {
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            z-index: 1031;
            width: var(--logo-height-large);
            height: var(--logo-height-large);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        header #logo-rotator .logo-image {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            opacity: 0;
            transition: opacity 0.5s ease-in-out;
            width: auto;
            display: block;
        }

        header #logo-rotator .logo-image.active {
            opacity: 1;
        }

        header img.logo-size-medium { height: var(--logo-height-medium, 70px) !important; }
        header img.logo-size-large { height: var(--logo-height-large, 90px) !important; }

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

        .site-footer {
            background-color: var(--color-bg-footer, #121212);
            color: var(--color-text-muted-footer, #adb5bd);
            padding-top: var(--footer-padding-y, 3rem);
            padding-bottom: 1.5rem;
            font-family: var(--font-secondary);
            font-size: 0.9rem;
            line-height: 1.7;
        }

        .site-footer .footer-logo {
            max-height: var(--footer-logo-height, 70px);
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
            vertical-align: -0.1em;
        }

        .site-footer .address-text {
            margin-bottom: 0;
            font-size: 0.85rem;
            line-height: 1.6;
            max-width: 300px
        }

        .site-footer .footer-bottom-row {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 1.5rem;
            margin-top: 2.5rem;
        }

        .site-footer .contact-info a {
            color: var(--color-text-muted-footer, #adb5bd);
            text-decoration: none;
            display: inline-block;
            padding-bottom: 2px;
            border-bottom: 1px solid var(--color-text-muted-footer, #adb5bd);
            transition: color 0.2s ease-in-out, border-bottom-color 0.2s ease-in-out;
            font-size: 0.85rem;
        }

        .site-footer .contact-info a:hover {
            color: var(--color-brand, #AD9D6C);
            border-bottom-color: var(--color-brand, #AD9D6C);
        }

        .site-footer .footer-copyright small {
            color: var(--color-text-muted-footer, #adb5bd);
            opacity: 0.9;
            font-size: 0.8rem;
        }
    </style>
    @stack('styles')
</head>
<body>
    @php
        $logosData = [
            ['src' => asset('images/logonavbar1.png'), 'class' => 'logo-size-medium'],
            ['src' => asset('images/logonavbar2.png'), 'class' => 'logo-size-large'],
        ];
    @endphp

    <header>
        <div class="container-fluid px-5">
            <div class="nav-group-left">
                <div class="dropdown header-dropdown me-3">
                    <a class="nav-link fw-bold dropdown-toggle" href="#" role="button" id="storeDropdown" data-bs-toggle="dropdown" aria-expanded="false">Store</a>
                    <ul class="dropdown-menu" aria-labelledby="storeDropdown">
                        {{-- Memastikan tautan menggunakan route parameter gender --}}
                        <li><a class="dropdown-item" href="{{ route('products.gender.index', ['gender' => 'Male']) }}">Men</a></li>
                        <li><a class="dropdown-item" href="{{ route('products.gender.index', ['gender' => 'Female']) }}">Women</a></li>
                        {{-- Memastikan tautan menggunakan route parameter category_name --}}
                        <li><a class="dropdown-item" href="{{ route('products.category.index', ['category_name' => 'Fabric']) }}">Fabric</a></li>
                    </ul>
                </div>
                <a href="{{ route('Stories.ListStories') }}" class="nav-link fw-bold me-3">Stories</a>
                <a href="#" class="nav-link fw-bold me-3">My Order</a>
                <a href="{{ route('tentangkainara') }}" class="nav-link fw-bold">About Us</a>
            </div>

            <div id="logo-rotator" class="text-center">
                <a href="{{ route('welcome') }}">
                    <img id="rotating-logo-current"
                        src="{{ $logosData[0]['src'] }}"
                        alt="Kainara Logo"
                        class="{{ $logosData[0]['class'] }} logo-image active">

                    <img id="rotating-logo-next"
                        src=""
                        alt="Kainara Logo"
                        class="logo-image">
                </a>
            </div>

            <div class="icon-group-right">
                <a href="{{ route('cart.index') }}" class="nav-icon-link">
                    <img src="{{ asset('images/icons/icon-cart.png') }}" alt="Cart" class="header-icon">
                </a>
                <a href="#" class="nav-icon-link" title="My Account">
                    <img src="{{ asset('images/icons/icon-account.png') }}" alt="Account" class="header-icon">
                </a>
            </div>
        </div>
    </header>

    <main class="flex-grow-1 position-relative">
        @yield('content')
    </main>

    <footer class="site-footer flex-grow-1 position-relative">
        <div class="container-fluid px-5 py-4">
            <div class="row gy-4 justify-content-between mt-3">
                <div class="col-lg-3 col-md-12 text-center text-lg-start footer-logo-section mb-4 mb-lg-0">
                    <img src="{{ asset('images/logofooter.png') }}" alt="Kainara Footer Logo" class="footer-logo">
                </div>

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

                <div class="col-lg-2 col-md-3 col-6">
                    <h6 class="footer-heading">SOCIALS</h6>
                    <ul class="list-unstyled footer-links">
                        <li><a href="#"><i class="bi bi-facebook"></i> Facebook</a></li>
                        <li><a href="#"><i class="bi bi-instagram"></i> Instagram</a></li>
                        <li><a href="#"><i class="bi bi-twitter"></i> Twitter</a></li>
                    </ul>
                </div>

                <div class="col-lg-4 col-md-6 footer-address-section">
                    <h6 class="footer-heading">BCA LEARNING INSTITUTE</h6>
                    <p class="address-text mb-0">Sentul City, Jl. Pakuan No.3, Sumur Batu, Babakan Madang, Bogor Regency, West Java 16810</p>
                </div>
            </div>

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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <script>
        const logosDataForScriptOptimized = @json($logosData);
        let currentIndexOptimized = 0;
        const logoImg1 = document.getElementById('rotating-logo-current');
        const logoImg2 = document.getElementById('rotating-logo-next');
        const allSizeClassesOptimized = ['logo-size-small', 'logo-size-medium', 'logo-size-large'];

        if (logoImg1 && logoImg2 && logosDataForScriptOptimized && logosDataForScriptOptimized.length > 0) {
            // Initialize with the first logo
            logoImg1.src = logosDataForScriptOptimized[0].src;
            allSizeClassesOptimized.forEach(cls => logoImg1.classList.remove(cls));
            if (logosDataForScriptOptimized[0].class) {
                logoImg1.classList.add(logosDataForScriptOptimized[0].class);
            }
            logoImg1.classList.add('active');
            logoImg2.classList.remove('active'); // Ensure the 'next' is not active initially

            let activeImage = logoImg1;
            let inactiveImage = logoImg2;

            setInterval(() => {
                currentIndexOptimized = (currentIndexOptimized + 1) % logosDataForScriptOptimized.length;
                const nextLogo = logosDataForScriptOptimized[currentIndexOptimized];

                // Update the inactive image's source and classes
                inactiveImage.src = nextLogo.src;
                allSizeClassesOptimized.forEach(cls => inactiveImage.classList.remove(cls));
                if (nextLogo.class) {
                    inactiveImage.classList.add(nextLogo.class);
                }

                // Toggle active classes for smooth transition
                activeImage.classList.remove('active');
                inactiveImage.classList.add('active');

                // Swap active and inactive images
                let temp = activeImage;
                activeImage = inactiveImage;
                inactiveImage = temp;

            }, 4000); // Change logo every 4 seconds
        }
    </script>

    @stack('scripts')
</body>
</html>