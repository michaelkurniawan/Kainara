<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $story->title ?? "Kainara's Story" }}</title>
    <link rel="stylesheet" href="{{ asset('css/fonts.css') }}">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />

    <style>
        body {
            position: relative; /* supaya anak-anak absolute bisa relatif ke body */
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
            color: black;        /* Warna tulisan hitam */
            text-decoration: none; /* Hilangkan underline */
            transition: all 0.3s ease; /* Animasi halus saat hover */
            font-family: 'AncizarSerif', serif;
        }

        a.nav-link-custom:hover {
            text-decoration: underline; /* Tambahkan underline saat hover */
        }

        /* Gambar motif di kiri bawah */
        .batik-left {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 150px; /* atur ukuran sesuai keinginan */
            opacity: 0.3; /* supaya nggak terlalu menonjol */
            pointer-events: none; /* supaya gak ganggu klik */
            z-index: 10;
        }

        /* Gambar motif di kanan atas */
        .batik-right {
            position: fixed;
            top: 0;
            right: 0;
            width: 150px;
            opacity: 0.3;
            pointer-events: none;
            z-index: 10;
        }
    </style>
</head>
<body>
    <!-- Motif Batik kiri bawah -->
    <img src="{{ asset('images/Detail_BatikKiri.png') }}" alt="Batik Motif Left" class="batik-left" />

    <!-- Motif Batik kanan atas -->
    <img src="{{ asset('images/Detail_BatikKanan.png') }}" alt="Batik Motif Right" class="batik-right" />

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">

                <!-- Header -->
                <header class="text-center mb-5">
                    <h1 class="fs-1 fw-bold text-dark">{{ $story->title }}</h1>
                    <p class="text-secondary medium">{{ $story->created_at->format('F j, Y') }}</p>
                </header>
                
                <!-- Image -->
                <div class="text-center mb-4">
                    @php
                        $imagePath = 'HardCode_KainSongket.jpeg';
                        $imageTitle = pathinfo($imagePath, PATHINFO_FILENAME);
                    @endphp
                    {{-- MASIH HARDCODE UNTUK IMAGE --}}
                    <img src="{{ asset('images/HardCode_KainSongket.jpeg') }}" class="img-fluid rounded shadow-sm mb-2 w-75" alt="{{ $story->image_alt ?? 'Story Image' }}">
                    <p class="text-muted small">{{ $imageTitle }}</p>
                </div>

                <!-- Content -->
                <div class="text-justify lh-lg fs-5">
                    {!! $story->content !!}
                </div>

                <!-- Navigation Links -->
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

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
