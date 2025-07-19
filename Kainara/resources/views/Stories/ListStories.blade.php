@extends('layouts.app') 

@section('title', "Kainara's Stories") 

@push('styles')
<style>

    h1.display-5 {
        font-size: 4.5rem;
        font-weight: bold;
    }

    p.text-muted {
        font-size: 1.5rem;
    }

    .story-card {
        font-family: 'AncizarSerif', serif;
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        transition: transform 0.2s;
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

    .story-card .read-more-hover {
        font-family: 'AncizarSerif', serif;
        display: none; /* Hidden by default */
        font-weight: bold;
        color: #fff; /* White text on hover */
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 0.85em;
    }

    .story-card:hover .read-more-hover {
        display: block; /* Shown on hover */
    }

    .story-card:hover .story-date {
        display: none; /* Hide date on hover */
    }

    .card-body {
        padding: 15px;
    }

    .card-title {
        font-family: 'Ancizar Serif', serif;
        font-size: 1.3em;
        color: #333; /* Default text color */
    }

    .story-card:hover .card-title {
        color: white; /* Text color on hover */
        font-weight: bold;
    }

    .card-text {
        margin-top: 0.5vh;
        font-family: 'AncizarSerif', serif;
        font-size: 0.85em;
        color: #555;
        line-height: 1.5;
        font-weight: 300;
        font-style: normal;
    }

    .story-card:hover .card-text {
        color: white; /* Text color on hover */
    }

    .story-date {
        font-family: 'AncizarSerif', serif;
        font-size: 0.80em;
        color: #545;
        text-align: right;
        font-weight: 400;
        font-style: normal;
    }
</style>
@endpush

@section('content')
    <div class="container-fluid py-5 px-5">
        <x-bangga title="Kainara's Stories" subtitle="Bangga Pakai Karya UMKM" />
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4 py-5">
            @forelse ($articles as $story)
                <div class="col">
                    <a href="{{ route('Stories.DetailStories', $story->slug) }}" class="text-decoration-none text-dark">
                        <div class="story-card h-100">
                            <img src="{{ asset('storage/' . $story->thumbnail) }}" alt="{{ $story->thumbnail }}" class="img-fluid rounded-top" />
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">{{ $story->title }}</h5>
                                <p class="card-text">{{ Str::limit(strip_tags($story->content), 100) }}</p>
                                <p class="story-date mt-auto">{{ $story->created_at->format('F j, Y') }}</p>
                                <p class="read-more-hover mt-auto text-end">Read More</p>
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
    </main>
</div>
@endsection

@push('scripts')
@endpush