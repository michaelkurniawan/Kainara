@extends('layouts.app')

@section('title', $story->title . ' Detail')

@section('content')
    <div class="container-fluid py-5 position-relative main-content-wrapper">
        <img src="{{ asset('images/Detail_BatikKiri.png') }}" alt="Batik Motif Left" class="batik-absolute batik-left" />
        <img src="{{ asset('images/Detail_BatikKanan.png') }}" alt="Batik Motif Right" class="batik-absolute batik-right" />

        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">

                <div class="text-center mb-5">
                    <h1 class="fs-1 fw-bold text-dark">{{ $story->title }}</h1>
                    <p class="text-secondary medium">{{ $story->created_at->format('F j, Y') }}</p>
                </div>
                
                <div class="text-center mb-4 d-flex justify-content-center">
                    <img src="{{ asset('storage/' . $story->thumbnail) }}" alt="{{ $story->thumbnail }}" class="img-fluid rounded shadow-sm mb-2 w-75" />
                </div>

                <div class="text-justify lh-lg fs-5">
                    {!! nl2br(e($story->content)) !!} 
                </div>

                <nav class="d-flex justify-content-between mt-5 pt-3 border-top">
                    <div>
                        @if ($previousStory)
                            <a href="{{ route('Stories.DetailStories', $previousStory->slug) }}" class="nav-link-custom">
                                ← Previous Page</a>
                        @endif
                    </div>
                    <div>
                        @if ($nextStory)
                            <a href="{{ route('Stories.DetailStories', $nextStory->slug) }}" class="nav-link-custom">
                                Next Page →</a>
                        @endif
                    </div>
                </nav>

            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        body {
            background-color: white;
            font-family: 'AncizarSerif', serif;
            background-image: url('{{ asset('images/background_pattern_left.png') }}'), url('{{ asset('images/background_pattern_right.png') }}');
            background-repeat: no-repeat;
            background-position: left top, right bottom;
            background-size: 150px auto, 150px auto;
        }

        .text-justify {
            text-align: justify;
        }

        a.nav-link-custom {
            color: black;
            text-decoration: none;
            transition: all 0.3s ease;
            font-family: 'AncizarSerif', serif;
        }

        a.nav-link-custom:hover {
            text-decoration: underline;
        }

        .main-content-wrapper {
            overflow: hidden;
        }

        .batik-absolute {
            position: absolute;
            width: 150px;
            opacity: 0.3;
            pointer-events: none;
            z-index: -1;
        }

        .batik-left {
            top: 0;
            left: 0;
        }

        .batik-right {
            bottom: 0;
            right: 0;
        }

    </style>
@endpush

@push('scripts')
@endpush