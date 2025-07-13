<!-- Ini untuk komponen card dalam page Kainara's Stories -->

<div class="card h-100 shadow-sm story-card">
    <img src="{{ $image }}" class="card-img-top" alt="{{ $title }}">
    <div class="card-body d-flex flex-column position-relative">
        <h3 class="card-title">{{ $title }}</h3>
        <p class="card-text">{{ $description }}</p>

        <!-- Tanggal yang disembunyikan saat hover -->
        <div class="story-date mt-auto original-date">{{ $date }}</div>

        <!-- Read More muncul saat hover -->
        <div class="read-more-hover mt-auto text-center">Read More</div>
    </div>
</div>
