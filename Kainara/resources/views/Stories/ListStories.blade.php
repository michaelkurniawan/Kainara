@extends('layouts.app') 

@section('title', "Kainara's Stories") 

@push('styles')
<style>
    :root {
        --font-primary: 'Ancizar Serif', serif;
        --font-secondary: 'Ancizar Serif', serif;
    }
    
    h1.display-5 {
        font-size: 4.5rem;
        font-weight: bold;
    }

    p.text-muted {
        font-size: 1.5rem;
    }

    .story-card {
        font-family: var(--font-secondary); /* Use defined variable */
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        transition: transform 0.2s, background-color 0.2s; /* Add background-color to transition */
        display: flex; /* Make it a flex container */
        flex-direction: column; /* Stack children vertically */
    }

    .story-card:hover {
        transform: translateY(-10px);
        background-color: #B6B09F; /* Hover background color */
    }

    .story-card img {
        width: 100%;
        max-width: 100%;
        height: 200px;
        object-fit: cover;
        display: block;
    }

    .story-card .card-body {
        padding: 15px;
        display: flex; /* Already flex-column from Blade, but ensure it */
        flex-direction: column;
        flex-grow: 1; /* Allow card-body to grow and take available space */
    }

    .card-title {
        font-family: var(--font-primary); /* Use defined variable */
        font-size: 1.3em;
        color: #333; /* Default text color */
        margin-bottom: 0.5rem; /* Add some space below title */
    }

    .story-card:hover .card-title {
        color: white; /* Text color on hover */
        font-weight: bold;
    }

    .card-text {
        font-family: var(--font-secondary); /* Use defined variable */
        font-size: 0.85em;
        color: #555;
        line-height: 1.5;
        font-weight: 300;
        font-style: normal;
        min-height: 5em; /* **KEY CHANGE:** Set a minimum height for content (adjust as needed) */
        flex-grow: 1; /* Allow content to grow if needed, but min-height is critical for alignment */
    }

    .story-card:hover .card-text {
        color: white; /* Text color on hover */
    }

    .story-date, .read-more-hover {
        font-family: var(--font-secondary); /* Use defined variable */
        font-size: 0.80em;
        font-weight: 400;
        font-style: normal;
        text-align: right;
        margin-top: auto; /* Push to the bottom */
    }

    .story-date {
        color: #545;
    }

    .read-more-hover {
        display: none; /* Hidden by default */
        font-weight: bold;
        color: #fff; /* White text on hover */
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .story-card:hover .read-more-hover {
        display: block; /* Shown on hover */
    }

    .story-card:hover .story-date {
        display: none; /* Hide date on hover */
    }
</style>
@endpush

@section('content')
    <div class="container-fluid py-5 px-5">
        <x-bangga title="Kainara's Stories" subtitle="Bangga Pakai Karya UMKM" />
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4 mt-4">
            @forelse ($articles as $story)
                <div class="col d-flex"> {{-- Add d-flex here to make columns equal height --}}
                    <a href="{{ route('Stories.DetailStories', $story->slug) }}" class="text-decoration-none text-dark d-flex flex-grow-1"> {{-- Ensure the link also takes full height --}}
                        <div class="story-card h-100"> {{-- h-100 is crucial here --}}
                            <img src="{{ asset('storage/' . $story->thumbnail) }}" alt="{{ $story->thumbnail }}" class="img-fluid rounded-top" />
                            <div class="card-body"> {{-- d-flex flex-column is effectively applied via .story-card CSS --}}
                                <h5 class="card-title fw-bold">{{ $story->title }}</h5>
                                <p class="card-text">{{ Str::limit(strip_tags($story->content), 100) }}</p>
                                <p class="story-date">{{ $story->created_at->format('F j, Y') }}</p>
                                <p class="read-more-hover text-end">Read More</p> {{-- text-end for right alignment --}}
                            </div>
                        </div>
                    </a>
                </div>
            @empty
                <div class="col-12 text-center">
                    <p class="text-muted fs-4">No stories available yet.</p>
                </div>
            @endforelse
        </div>

        @include('Pagination.pagination-components', ['paginator' => $articles])
    </div>
</main> {{-- This closing tag was misplaced, assuming it should close the main section --}}
@endsection

@push('scripts')
@endpush