@php
    $logosData = [
        ['src' => asset('images/logonavbar1.png'), 'class' => 'logo-size-medium'],
        ['src' => asset('images/logonavbar2.png'), 'class' => 'logo-size-large'],
    ];
@endphp

{{-- STYLE --}}
<style>
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

    header .container-fluid {
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

    /* --- UPDATED DROPDOWN STYLES --- */
    header .header-dropdown .dropdown-menu {
        background-color: white; /* Changed to white */
        border: 1px solid #e2e8f0; /* Added a subtle border for definition */
        border-radius: 0; /* Changed to square corners */
        box-shadow: 0 0.25rem 0.75rem rgba(0,0,0,0.15); /* Shadow halus */
        padding: 0.5rem 0; /* Padding atas/bawah internal menu */
        margin-top: 0.75rem !important; /* Beri sedikit jarak dari toggle (override Bootstrap) */
    }

    header .header-dropdown .dropdown-item {
        color: black; /* Changed to black */
        padding: 0.6rem 1.5rem; /* Padding item */
        font-family: var(--font-primary); /* Font yang lebih elegan untuk item dropdown */
        font-weight: 600; /* Sedikit tebal */
        font-size: 1.1rem; /* Ukuran font item dropdown */
        transition: background-color 0.2s ease-in-out, color 0.2s ease-in-out;
    }

    header .header-dropdown .dropdown-item:hover,
    header .header-dropdown .dropdown-item:focus {
        background-color: #f0f0f0; /* Light grey background on hover */
        color: black; /* Text remains black on hover */
    }
    /* --- END UPDATED DROPDOWN STYLES --- */


    header .nav-group-left .dropdown > .dropdown-toggle::after {
        margin-left: 0.4em; /* Jarak antara teks "Store" dan panah */
        vertical-align: 0.1em;
    }

    header .nav-group-left .dropdown > a.nav-link.dropdown-toggle::after {
        margin-left: 0.3em;  /* Jarak antara teks "Store" dan panah */
        margin-top: 0.2em;
        transition: transform 0.25s ease-in-out; /* Transisi untuk animasi balik panah */
    }

    header .nav-group-left .nav-link,
    header .icon-group-right .nav-icon-link {
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
        width: 180px;
        display: flex;
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
        width: auto;
        display: block; /* atau inline-block dengan vertical-align middle */
    }

    header #logo-rotator .logo-image.active {
        opacity: 1; /* Logo aktif terlihat penuh */
    }

    header img.logo-size-medium { height: var(--logo-height-medium, 70px) !important; }
    header img.logo-size-large { height: var(--logo-height-large, 100px) !important; }

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

    /* New styles for the user dropdown */
    .user-dropdown-toggle {
        display: flex;
        align-items: center;
        cursor: pointer;
        padding: 0.5rem; /* Adjust padding as needed */
        border-radius: 9999px; /* For a circular toggle */
        transition: background-color 0.2s ease-in-out;
    }

    .user-dropdown-toggle:hover {
        background-color: rgba(255, 255, 255, 0.1); /* Light hover effect */
    }

    .user-dropdown-toggle img {
        border-radius: 50%; /* Make profile picture circular */
        object-fit: cover;
        height: var(--target-icon-size); /* Match other icons */
        width: var(--target-icon-size); /* Match other icons */
    }
    
    /* New styles for the login button */
    .login-btn {
        background-color: transparent;
        color: var(--color-text-primary);
        border: 1px solid black;
        padding: 0.5rem 1.5rem;
        text-decoration: none;
        border-radius: 5px;
        font-weight: 600;
        transition: background-color 0.2s ease-in-out, color 0.2s ease-in-out;
    }
    .login-btn:hover {
        background-color: var(--color-text-primary);
                color: var(--color-brand);

    }
</style>


<header>
    <div class="container-fluid px-5">
        <div class="nav-group-left">
            <div class="dropdown header-dropdown me-3">
                <a class="nav-link fw-bold dropdown-toggle" href="#" role="button" id="storeDropdown" data-bs-toggle="dropdown" aria-expanded="false">Store</a>
                <ul class="dropdown-menu" aria-labelledby="storeDropdown">
                    <li><a class="dropdown-item" href="{{ route('products.gender.index', ['gender' => 'Male']) }}">Men</a></li>
                    <li><a class="dropdown-item" href="{{ route('products.gender.index', ['gender' => 'Female']) }}">Women</a></li>
                    <li><a class="dropdown-item" href="{{ route('products.category.index', ['category_name' => 'Fabric']) }}">Fabric</a></li>
                </ul>
            </div>
            <a href="{{ route('Stories.ListStories') }}" class="nav-link fw-bold me-3">Stories</a>
            <a href="{{ route('my.orders') }}" class="nav-link fw-bold me-3">My Order</a>
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

            @auth {{-- Display this section only if a user is logged in --}}
                <div class="dropdown header-dropdown">
                    <a class="nav-icon-link user-dropdown-toggle" href="#" role="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        {{-- Always display the generic account icon --}}
                        <img src="{{ asset('images/icons/icon-account.png') }}"
                                alt="Account"
                                class="header-icon">
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <li><h6 class="dropdown-header">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</h6></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="{{ route('profile.index') }}">Profile</a></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item">Logout</button>
                            </form>
                        </li>
                    </ul>
                </div>
            @else {{-- Display login/register links if no user is logged in --}}
                <a href="{{ route('login') }}" class="login-btn">Login</a>
            @endauth
        </div>
    </div>
</header>

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