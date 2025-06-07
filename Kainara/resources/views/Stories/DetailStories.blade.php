<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $story->title ?? "Kainara's Story" }}</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />

    <style>
        body {
            font-family: 'AncizarSerif', serif;
            background-image: url('{{ asset('images/background_pattern_left.png') }}'), url('{{ asset('images/background_pattern_right.png') }}');
            background-repeat: no-repeat;
            background-position: left top, right bottom;
            background-size: 150px auto, 150px auto;
        }

        /* Optional: If justify text is crucial, add this */
        .text-justify {
            text-align: justify;
        }
    </style>
</head>

<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">

                <!-- Header -->
                <header class="text-center mb-5">
                    <h1 class="fs-2 fw-bold text-dark">{{ $story->title ?? "The Gleaming Legacy: Songket, Indonesia's Golden Fabric" }}</h1>
                    <p class="text-secondary small">{{ $story->date ?? '21st May, 2023' }}</p>
                </header>

                <!-- Image -->
                <div class="text-center mb-4">
                    <img src="{{ $story->image_path ?? asset('images/songket_example.jpg') }}" class="img-fluid rounded shadow-sm mb-2" alt="{{ $story->image_alt ?? 'Kain Songket Asal Palembang' }}">
                    <p class="text-muted small">{{ $story->image_caption ?? 'Kain Songket Asal Palembang' }}</p>
                </div>

                <!-- Content -->
                <div class="text-justify lh-lg">
                    <p>
                        Indonesia is a land rich in culture and tradition, and among its many treasured crafts, songket stands out as one of the most luxurious and symbolic textiles. Often referred to as the "golden cloth," songket is a handwoven fabric traditionally made with silk or cotton threads, intricately woven with shimmering gold or silver threads that create stunning patterns and motifs.
                    </p>
                    <p>
                        Originating from the royal courts and aristocratic circles, songket was once worn only on special occasions such as weddings, religious ceremonies, and royal events. Its intricate process of weaving, which can take weeks or even months, is a testament to the skill and patience of the artisans who create it. Each region in Indonesia, especially in Sumatra and parts of Kalimantan, has its own distinct style and motifs that reflect local beliefs and heritage.
                    </p>
                    <p>
                        More than just a fabric, songket represents identity, status, and a deep connection to ancestral legacy. Today, while it continues to be a symbol of cultural pride, contemporary designers are also reimagining songket in modern fashion, allowing younger generations to embrace this heritage in new ways.
                    </p>
                    <p>
                        Preserving and celebrating songket is not only about honoring the past—it is about weaving tradition into the future. In every golden thread lies a story, a piece of identity, and a spark of pride that connects us to generations before us. By supporting local weavers, wearing songket proudly, and sharing its story with the world, we ensure that this shimmering legacy continues to shine—both today and for centuries to come.
                    </p>
                </div>

                <!-- Navigation Links -->
                <nav class="d-flex justify-content-between mt-5 pt-3 border-top">
                    <div>
                        @if ($previousStory)
                            <a href="{{ route('Stories.DetailStories', $previousStory->slug ?? $previousStory->id) }}" class="text-decoration-none text-dark">
                                &lt; Previous Post
                            </a>
                        @endif
                    </div>
                    <div>
                        @if ($nextStory)
                            <a href="{{ route('Stories.DetailStories', $nextStory->slug ?? $nextStory->id) }}" class="text-decoration-none text-dark">
                                Next Post &gt;
                            </a>
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
