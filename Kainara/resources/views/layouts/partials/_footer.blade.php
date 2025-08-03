<style>
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

<footer class="site-footer">
    <div class="container py-5">
        <div class="row gy-4 justify-content-between">
            {{-- Kolom Logo --}}
            <div class="col-lg-3 col-md-12 text-center text-lg-start footer-logo-section mb-4 mb-lg-0">
                <img src="{{ asset('images/logofooter.png') }}" alt="Kainara Footer Logo" class="footer-logo">
            </div>

            {{-- Kolom Menu --}}
            <div class="col-lg-2 col-md-4 col-6">
                <h6 class="footer-heading">MENU</h6>
                <ul class="list-unstyled footer-links">
                    <li><a href="{{ route('products.gender.index', ['gender' => 'Male']) }}">Men</a></li>
                    <li><a href="{{ route('products.gender.index', ['gender' => 'Female']) }}">Women</a></li>
                    <li><a href="{{ route('products.category.index', ['category_name' => 'fabric']) }}">Fabric</a></li>
                    <li><a href="{{ route('my.orders') }}">My Order</a></li>
                </ul>
            </div>

            {{-- Kolom Info Lainnya --}}
            <div class="col-lg-2 col-md-4 col-6">
                <h6 class="footer-heading">INFO</h6>
                <ul class="list-unstyled footer-links">
                    <li><a href="{{ route('tentangkainara') }}">About Us</a></li>
                    <li><a href="{{ route('profile.index') }}">Profile</a></li>
                </ul>
            </div>

            {{-- Kolom Alamat --}}
            <div class="col-lg-4 col-md-4 footer-address-section">
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
                <p class="footer-copyright mb-0"><small>© 2025 Kainara. All rights reserved.</small></p>
            </div>
        </div>
    </div>
</footer>