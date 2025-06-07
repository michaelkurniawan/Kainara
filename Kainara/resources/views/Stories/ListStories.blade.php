<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Kainara's Stories</title>
    <link rel="stylesheet" href="{{ asset('css/fonts.css') }}">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body {
            font-family: 'AncizarSerif', serif;
            background-color: #f8f8f8;
            overflow-x: hidden;
        }

        .container {
            max-width: 1400px;
            width: 100%;
            padding-left: 15px;
            padding-right: 15px;
            margin: 0 auto;
            overflow-x: hidden; 
        }

        header {
            font-family: 'AncizarSerif', serif;
            position: relative;
            padding: 3vw 0;
            text-align: center;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 1.3vh;
            flex-wrap: nowrap; 
            overflow-x: hidden; 
        }

        /* Cloud gambar di kiri dan kanan */
        .cloud-img {
            height: auto;
            max-height: 40vh;
            max-width: 25vw;  /* Batasi agar nggak terlalu lebar */
            flex-shrink: 1;   /* Biar bisa mengecil saat dibutuhkan */
        }

        h1 {
            font-size: 5.5vw;
            color: #333;
            line-height: 1.2;
            font-style: italic;
            font-family: 'Ancizar Serif';
            letter-spacing: 0.5vw;
            word-break: break-word; 
            text-align: center;
        }

        .tagline {
            font-size: 1.5vw;
            color: #666;
            font-style: italic;
            font-weight: 100;
            font-family: 'Ancizar Serif';
        }

        /* Story card styles */
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
            background-color: #B6B09F;
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
            display: none;
            font-weight: bold;
            color: #fff;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 0.85em;
        }

        .story-card:hover .read-more-hover {
            display: block;
        }

        .story-card:hover .story-date {
            display: none;
        }

        .card-body {
            padding: 15px;
        }

        .card-title {
            font-family: 'Ancizar Serif', serif;
            font-size: 1.3em;
            color: #333;
        }

        .story-card:hover .card-title {
            color: white;
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
            color: white;
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
</head>

<body>
    <div class="container">
        <!-- Header -->
        <header>
            <!-- Gambar megamendung kiri -->
            <img src="{{ asset('images/megamendung.png') }}" alt="Megamendung" class="cloud-img" style="transform: scaleX(-1);" />

            <!-- Judul dan tagline di tengah -->
            <div>
                <h1>Kainara's Stories</h1>
                <p class="tagline">Bangga Pakai Karya UMKM</p>
            </div>

            <!-- Gambar megamendung kanan -->
            <img src="{{ asset('images/megamendung.png') }}" alt="Megamendung" class="cloud-img" />
        </header>

        <!-- Story Grid -->
        <main>
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4 py-4">
        @foreach ($articles as $story)
            <div class="col">
                {{-- Bungkus seluruh story-card dengan link --}}
                <a href="{{ route('Stories.DetailStories', $story->id) }}" class="text-decoration-none text-dark">
                    <div class="story-card h-100">
                        {{-- Pastikan path gambar benar, biasanya public/storage/images/ --}}
                        <img src="{{ asset('storage/' . $story->thumbnail) }}" alt="Story Image" class="img-fluid rounded-top" />
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">{{ $story->title }}</h5>
                            {{-- Gunakan strip_tags untuk membersihkan HTML dari konten sebelum membatasi teks --}}
                            <p class="card-text">{{ Str::limit(strip_tags($story->content), 100) }}</p>

                            <p class="story-date mt-auto">{{ $story->created_at->format('F j, Y') }}</p>
                            <p class="read-more-hover mt-auto text-end">Read More</p>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>
</main>

    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
