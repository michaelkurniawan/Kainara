<!-- Footer -->
<footer class="site-footer">
    <div class="container">
        <div class="row gy-4 justify-content-between mt-3">
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
            <div class="col-lg-4 col-md-6 footer-address-section">
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