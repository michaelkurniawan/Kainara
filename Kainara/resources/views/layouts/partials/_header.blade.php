@php
    // Definisikan data logo di sini karena hanya digunakan oleh header
    $logosData = [
        ['src' => asset('storage/logo1.png'), 'class' => 'logo-size-medium'],
        ['src' => asset('storage/logo2.png'), 'class' => 'logo-size-large'],
    ];
@endphp

<!-- Header -->
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
            <a href="#" class="nav-link fw-bold">Stories</a>
            <a href="#" class="nav-link fw-bold">My Order</a>
            <a href="#" class="nav-link fw-bold">About Us</a>
        </div>

        {{-- Logo Tengah --}}
        <div id="logo-rotator" class="text-center">
            <img id="rotating-logo-current"
                 src="{{ $logosData[0]['src'] }}"
                 alt="Kainara Logo"
                 class="{{ $logosData[0]['class'] }} logo-image active">
            <img id="rotating-logo-next"
                 src=""
                 alt="Kainara Logo"
                 class="logo-image">
        </div>

        {{-- Grup Ikon Kanan --}}
        <div class="icon-group-right">
            <a href="#" class="nav-icon-link">
                <img src="{{ asset('images/icons/icon-cart.png') }}" alt="Cart" class="header-icon">
            </a>
            <a href="#" class="nav-icon-link" title="My Account">
                <img src="{{ asset('images/icons/icon-account.png') }}" alt="Account" class="header-icon">
            </a>
        </div>
    </div>
</header>