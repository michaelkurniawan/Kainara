<!-- Ini untuk komponen card dalam page Kainara's Stories -->

<div class="card h-100 shadow-sm story-card">
    <img src="{{ $image }}" class="card-img-top" alt="{{ $title }}">
    <div class="card-body d-flex flex-column">
        <h3 class="card-title">{{ $title }}</h3>
        <p class="card-text">{{ $description }}</p>
        <div class="story-date mt-auto">{{ $date }}</div>

        {{-- Added for hover effect --}}
        <div class="hover-overlay">
            <a href="#" class="btn btn-read-more">Read More</a>
        </div>
    </div>
</div>