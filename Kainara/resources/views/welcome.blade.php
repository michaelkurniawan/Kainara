@extends('layouts.app')

@section('title', 'Homepage Kainara')

@section('content')

@push('styles')
<style>
    :root {
        --font-primary: 'Ancizar Serif', serif;
        --font-secondary: 'Ancizar Serif', serif;
        --section-padding-y: 5rem;
    }

    .decorative-motif {
        position: absolute;
        z-index: 0;
        opacity: 1;
        pointer-events: none;
    }
    .decorative-motif img {
        display: block;
        width: 100%;
        height: auto;
    }

    .motif-hero-top-left {
        top: -30px;
        left: -70px;
        width: 280px;
    }
    .motif-hero-top-right {
        top: 80px;
        right: -50px;
        width: 300px;
    }
    .motif-hero-bottom-left {
        bottom: 60px;
        left: -40px;
        width: 240px;
    }
    .motif-hero-bottom-right {
        bottom: 10px;
        right: -60px;
        width: 260px;
    }

    .motif-latest-top-left {
        bottom: -20px;
        left: -70px;
        width: 280px;
    }
    .motif-latest-top-right {
        bottom: -100px;
        right: -120px;
        width: 300px;
    }

    .hero-section-custom {
        min-height: 90vh;
        background-color: var(--color-bg-hero);
        display: flex;
        align-items: center;
        position: relative;
        overflow: hidden;
    }

    .hero-section-custom .hero-text-content {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        text-align: center;
        position: relative;
        z-index: 2;
        padding-left: 4rem;
    }

    .hero-section-custom .hero-text-content h1 {
        font-family: var(--font-primary, 'Ancizar Serif', serif);
        color: var(--color-text-hero-title, #212529);
        font-size: 3rem;
        font-weight: 700;
        line-height: 1.3;
        margin-bottom: 1.5rem;
        text-align: center;
        margin-left: 4rem;
    }

    .hero-section-custom .hero-text-content h1 .hero-title-italic {
        font-style: italic;
        font-weight: 300;
    }

    .hero-section-custom .hero-text-content h1 .hero-title-bold-italic {
        font-style: italic;
        font-weight: 700;
    }

    .hero-section-custom .hero-text-content p.lead {
        font-family: var(--font-secondary);
        color: var(--color-text-hero-lead);
        font-size: 1.1rem;
        max-width: 450px;
        margin-bottom: 1rem;
        text-align: center;
        margin-left: 2rem;
    }

    .hero-section-custom .btn-kainara {
        background-color: var(--color-brand);
        color: white;
        padding: 0.8rem 2.5rem;
        border-radius: 4px;
        text-transform: uppercase;
        font-weight: 500;
        letter-spacing: 0.5px;
        border: none;
        transition: background-color 0.3s ease;
        margin-top: 2rem;
        margin-left: 6rem;
    }
    .hero-section-custom .btn-kainara:hover {
        background-color: #a58e6a;
    }

    .hero-section-custom .hero-image-container img.hero-model-img {
        max-width: 100%;
        height: auto;
        max-height: 90vh;
        display: block;
        margin-left: auto;
        margin-right: auto;
        transform: translateY(-35px);
    }

    .story-video-section-overlay {
        min-height: 90vh;
        position: relative;
        background-color: #1a1a1a;
        overflow: hidden;
        padding: 0;
        display: flex;
    }

    .story-video-section-overlay .video-bg-wrapper-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        overflow: hidden;
        z-index: 1;
    }

    .story-video-section-overlay .video-bg-wrapper-overlay video#storyOverlayVideo {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        min-width: 100%;
        min-height: 100%;
        width: auto;
        height: auto;
        object-fit: cover;
    }

    .story-video-section-overlay .video-bg-wrapper-overlay .video-overlay-layer {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.50);
    }

    .story-video-section-overlay .video-text-content-overlay {
        position: relative;
        z-index: 2;
        color: #fff;
        text-align: center;
        padding: 2rem 1rem;
        max-width: 700px;
    }

    .story-video-section-overlay .video-text-content-overlay h2 {
        font-family: var(--font-primary, 'Playfair Display', serif);
        text-shadow: 2px 2px 6px rgba(0, 0, 0, 0.75);
        font-size: 3.5rem;
        font-weight: 700;
        margin-bottom: 1rem;
        color: #fff !important;
        font-style: italic;
    }

    .story-video-section-overlay .video-text-content-overlay p.lead {
        font-family: var(--font-secondary, 'Lora', serif);
        text-shadow: 1px 1px 5px rgba(0, 0, 0, 0.65);
        line-height: 1.7;
        font-size: 1.2rem;
        margin-bottom: 0;
        color: #fff !important;
    }

   .join-movement-section-custom {
        min-height: auto;
        background-color: var(--color-bg-join-movement, #FFFFFF);
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .join-movement-section-custom .section-title-join{
        font-family: var(--font-primary);
        color: var(--color-text-dark, #333);
        font-size: 3rem;
        font-weight: 700;
        margin-top: 3.5rem;
    }

    .join-movement-section-custom .section-title-join .join-title-bold-italic {
        font-weight: 700;
        font-style: italic;
    }

    .join-movement-section-custom .section-title-join .join-title-italic {
        font-weight: 400;
        font-style: italic;
    }

    .join-movement-section-custom .feature-item-join {
        margin: 3rem;
    }

    .join-movement-section-custom .feature-icon-join {
        max-height: 270px;
        margin-bottom: 1rem;
    }

    .join-movement-section-custom .feature-title-join {
        font-family: var(--font-secondary);
        color: var(--color-text-dark, #333);
        font-size: 1.1rem;
        font-weight: 600;
    }

    .join-movement-section-custom .btn-join-artisan {
        background-color: var(--color-brand, #B9A077);
        color: white;
        padding: 0.7rem 2.5rem;
        border-radius: 4px;
        text-transform: uppercase;
        font-weight: 500;
        letter-spacing: 0.5px;
        border: none;
        transition: background-color 0.3s ease;
        margin-top: 0.5rem;
    }
    .join-movement-section-custom .btn-join-artisan:hover {
        background-color: #a58e6a;
    }

    .latest-stories-custom {
        background-color:rgb(255, 255, 255);
        padding-top: var(--section-padding-y, 4rem);
        position: relative;
        overflow: hidden;
        margin-top: 2rem;
    }

    .latest-stories-custom .section-title-stories {
        font-family: var(--font-primary);
        color: var(--color-text-dark, #212529);
        font-size: 2.8rem;
        font-weight: 700;
    }

    .link-styled-as-button-text {
        color: var(--color-brand, #B9A077);
        font-family: var(--font-secondary, 'Lora', serif);
        font-weight: 600;
        text-decoration: none;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-size: 0.95rem;
        padding: 0.5rem 0;
        transition: color 0.2s ease-in-out, opacity 0.2s ease-in-out;
    }

    .link-styled-as-button-text:hover {
        color: #a58e6a;
    }

    .latest-stories-custom .article-card-featured {
        border: none;
        box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,.075);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .latest-stories-custom .article-card-featured:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0,0,0,.15) !important;
    }
    .latest-stories-custom .article-card-featured .card-img-top {
        height: 350px;
        object-fit: cover;
        border-top-left-radius: 0.375rem;
        border-top-right-radius: 0.375rem;
    }
    .latest-stories-custom .article-card-featured .card-body {
        padding: 1.5rem;
    }
    .latest-stories-custom .article-card-featured .card-title a {
        font-family: var(--font-primary);
        color: var(--color-text-dark, #212529);
        font-weight: 700;
        font-size: 1.5rem;
        text-decoration: none;
    }
    .latest-stories-custom .article-card-featured .card-title a:hover {
        color: var(--color-brand, #B9A077);
    }
    .latest-stories-custom .article-card-featured .card-text.text-muted.small {
        font-family: var(--font-secondary);
        font-size: 0.8rem;
    }
    .latest-stories-custom .article-card-featured .card-text.article-excerpt {
        font-family: var(--font-secondary);
        font-size: 0.95rem;
        color: #495057;
        line-height: 1.6;
        display: -webkit-box;
        -webkit-line-clamp: 5;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .latest-stories-custom .article-card-small {
        border: none;
        box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,.075);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        background-color: #fff;
    }
    .latest-stories-custom .article-card-small:hover {
        transform: translateY(-3px);
        box-shadow: 0 0.3rem 0.8rem rgba(0,0,0,.12) !important;
    }
    .latest-stories-custom .article-card-small .article-img-small {
        height: 100%;
        min-height: 100px;
        object-fit: cover;
        border-top-left-radius: 0.375rem;
        border-bottom-left-radius: 0.375rem;
    }
    .latest-stories-custom .article-card-small .card-body {
        padding: 0.75rem 1rem;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    .latest-stories-custom .article-card-small .card-title a {
        font-family: var(--font-primary);
        color: var(--color-text-dark, #212529);
        font-weight: 600;
        font-size: 1rem;
        text-decoration: none;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .latest-stories-custom .article-card-small .card-title a:hover {
        color: var(--color-brand, #B9A077);
    }
    .latest-stories-custom .article-card-small .card-text.text-muted.small {
        font-family: var(--font-secondary);
        font-size: 0.75rem;
    }

    @media (max-width: 991.98px) {
        .story-video-section-overlay .video-text-content-overlay h2 { font-size: 3rem; }
        .story-video-section-overlay .video-text-content-overlay p.lead { font-size: 1.1rem; }
        .story-video-section-overlay .video-text-content-overlay .text-top { padding-top: 12vh; }
        .story-video-section-overlay .video-text-content-overlay .text-bottom { padding-bottom: 12vh; }
    }
    @media (max-width: 767.98px) {
        .story-video-section-overlay .video-text-content-overlay h2 { font-size: 2.2rem; }
        .story-video-section-overlay .video-text-content-overlay p.lead { font-size: 1rem; max-width: 90%; }
        .story-video-section-overlay .video-text-content-overlay .text-top { padding-top: 10vh; }
        .story-video-section-overlay .video-text-content-overlay .text-bottom { padding-bottom: 10vh; }
    }
    @media (max-width: 575.98px) {
        .story-video-section-overlay .video-text-content-overlay h2 { font-size: 1.8rem; }
        .story-video-section-overlay .video-text-content-overlay p.lead { font-size: 0.9rem; }
        .story-video-section-overlay .video-text-content-overlay .text-top { padding-top: 8vh; }
        .story-video-section-overlay .video-text-content-overlay .text-bottom { padding-bottom: 8vh; }
    }
</style>
@endpush

{{-- Hero Section --}}
<section class="hero-section-custom" style="padding-top:20px;">
    {{-- Decorative Motifs for Hero Section --}}
    <div class="decorative-motif motif-hero-top-left">
        <img src="{{ asset('images/motif-batik.png') }}" alt="Motif Batik">
    </div>
    <div class="decorative-motif motif-hero-top-right">
        <img src="{{ asset('images/motif-batik.png') }}" alt="Motif Batik">
    </div>
    <div class="decorative-motif motif-hero-bottom-left">
        <img src="{{ asset('images/motif-batik.png') }}" alt="Motif Batik">
    </div>
    <div class="decorative-motif motif-hero-bottom-right">
        <img src="{{ asset('images/motif-batik.png') }}" alt="Motif Batik">
    </div>

    <div class="container">
        <div class="row align-items-center">
            {{-- Hero Text Content --}}
            <div class="col-lg-5 col-md-6 hero-text-content order-md-1 order-2">
                <h1 class="fw-bold">
                    <span class="hero-title-italic">Threads of</span> <span class="hero-title-bold-italic">Heritage,</span><br>
                    <span class="hero-title-italic">Woven for the</span> <span class="hero-title-bold-italic">Future</span>
                </h1>
                <p class="lead fs-5">
                    Bringing Indonesia's traditional fabrics to the world through
                    innovation and artistry.
                </p>
                <div class="hero-button-wrapper">
                    <a href="{{ route('products.index') }}" class="btn btn-kainara">Discover Our Collection</a>
                </div>
            </div>

            {{-- Hero Image Container --}}
            <div class="col-lg-7 col-md-6 hero-image-container text-center text-md-end order-md-2 order-1">
                <img src="{{ asset('images/batik-couple.png') }}" alt="Kainara Models" class="img-fluid hero-model-img">
            </div>
        </div>
    </div>
</section>

{{-- Story Video Section --}}
<section class="story-video-section-overlay">
    <div class="video-bg-wrapper-overlay">
        <video playsinline="playsinline" autoplay="autoplay" muted="muted" loop="loop" id="storyOverlayVideo">
            <source src="{{ asset('videos/batik_story_video.mp4') }}" type="video/mp4">
            Your browser does not support the video tag.
        </video>
        <div class="video-overlay-layer"></div>
    </div>

    <div class="video-text-content-overlay container d-flex flex-column">
        <div class="text-top text-center">
            <h2 class="display-5 fw-bolder">"Every Pattern Tells a Story"</h2>
        </div>
        <div class="text-bottom text-center mt-auto">
            <p class="lead fs-5 mx-auto" style="max-width: 650px;">
                Go behind the scenes of Indonesian batik craftsmanship
                where heritage, patience, and passion are woven into every thread.
            </p>
        </div>
    </div>
</section>

{{-- Join Movement Section --}}
<section class="join-movement-section-custom">
    <div class="container-fluid">
        <h2 class="section-title-join mb-5">
            <span class="join-title-bold-italic">Join the Movement.</span><span class="join-title-italic"> Share Your Craft with the World</span>
        </h2>
        <div class="row justify-content-center">
            <div class="col-md-4 col-lg-3 feature-item-join">
                <img src="{{ asset('images/icon-signup.png') }}" alt="Sign Up Easily" class="img-fluid feature-icon-join">
                <h5 class="feature-title-join">Sign Up Easily</h5>
            </div>
            <div class="col-md-4 col-lg-3 feature-item-join">
                <img src="{{ asset('images/icon-preserve.png') }}" alt="Preserve Local Heritage" class="img-fluid feature-icon-join">
                <h5 class="feature-title-join">Preserve Local Heritage</h5>
            </div>
            <div class="col-md-4 col-lg-3 feature-item-join">
                <img src="{{ asset('images/icon-market.png') }}" alt="Reach a Wide Market" class="img-fluid feature-icon-join">
                <h5 class="feature-title-join">Reach a Wide Market</h5>
            </div>
        </div>
        <div class="join-button-motif-wrapper text-center position-relative mt-4">
            <a href="{{ route('artisan.register') }}" class="btn btn-join-artisan">Join as Artisan</a>  
        </div>
    </div>
    {{-- Decorative Motifs for Join Movement Section --}}
    <div class="decorative-motif motif-latest-top-left">
        <img src="{{ asset('images/motif-batik.png') }}" alt="Motif Batik">
    </div>
    <div class="decorative-motif motif-latest-top-right">
        <img src="{{ asset('images/motif-batik.png') }}" alt="Motif Batik">
    </div>
</section>

{{-- Latest Stories Section --}}
<section class="latest-stories-custom">
    <div class="container-fluid px-5 py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="section-title-stories">Latest Stories</h2>
            <a href="{{ route('Stories.ListStories') }}" class="link-styled-as-button-text">Read more articles!</a>
        </div>

        <div class="row">
            <div class="col-lg-7 mb-4 mb-lg-0">
                @if ($featuredArticle)
                    <a href="{{ route('Stories.DetailStories', $featuredArticle->slug) }}" class="card article-card-featured h-100 d-block text-decoration-none text-dark">
                        <img src="{{ asset('images/articles/' . $featuredArticle->thumbnail) }}" class="card-img-top" alt="{{ $featuredArticle->title }}">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title mb-2">
                                {{ $featuredArticle->title }}
                            </h5>
                            <p class="card-text text-muted small mb-3">{{ \Carbon\Carbon::parse($featuredArticle->created_at)->format('d M Y') }}</p>
                            <p class="card-text article-excerpt mb-0">
                                {{ Str::limit(strip_tags($featuredArticle->content), 300, '...') }}
                            </p>
                        </div>
                    </a>
                @else
                    <p>No featured article available.</p>
                @endif
            </div>

            <div class="col-lg-5">
                @forelse ($smallArticles as $article)
                    <a href="{{ route('Stories.DetailStories', $article->slug) }}" class="card article-card-small {{ $loop->last ? 'mb-0' : 'mb-3' }} d-block text-decoration-none text-dark">
                        <div class="row g-0">
                            <div class="col-4">
                                <img src="{{ asset('images/articles/' . $article->thumbnail) }}" class="img-fluid rounded-start article-img-small" alt="{{ $article->title }}">
                            </div>
                            <div class="col-8">
                                <div class="card-body">
                                    <h6 class="card-title mb-1">
                                        {{ $article->title }}
                                    </h6>
                                    <p class="card-text text-muted small">{{ \Carbon\Carbon::parse($article->created_at)->format('d M Y') }}</p>
                                </div>
                            </div>
                        </div>
                    </a> {{-- Closing <a> tag for the entire card --}}
                @empty
                    <p>No other articles available.</p>
                @endforelse
            </div>
        </div>
    </div>
</section>
@endsection