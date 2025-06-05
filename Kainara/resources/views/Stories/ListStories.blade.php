<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kainara's Stories</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Ancizar+Serif&family=Open+Sans:wght@400&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Open Sans', sans-serif; /* General body font */
            background-color: #f8f8f8; /* Light grey background */
        }
        .container {
            max-width: 1200px;
        }
        header {
            text-align: center;
            padding: 40px 0;
            position: relative;
        }
        .cloud-left, .cloud-right {
            position: absolute;
            top: 20px; /* Adjust as needed */
            width: 150px; /* Adjust cloud size */
            height: 75px;
            background-repeat: no-repeat;
            background-size: contain;
            z-index: 1; /* Ensure clouds are behind text if text is also positioned */
        }
        .cloud-left {
            left: 5%; /* Adjust as needed */
            background-image: url('{{ asset('images/cloud_left.png') }}'); /* Placeholder for your cloud image */
        }
        .cloud-right {
            right: 5%; /* Adjust as needed */
            background-image: url('{{ asset('images/cloud_right.png') }}'); /* Placeholder for your cloud image */
        }
        h1 {
            font-family: 'Ancizar Serif', serif; /* Main title font */
            font-size: 3em;
            color: #333;
            margin-bottom: 5px;
            position: relative; /* To bring text above clouds if clouds use z-index */
            z-index: 2;
        }
        .tagline {
            font-size: 1.1em;
            color: #666;
            margin-top: 0;
            position: relative;
            z-index: 2;
        }

        /* Custom styles for story card */
        .story-card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            text-align: left;
            transition: transform 0.2s, background-color 0.3s ease; /* Add transition for background */
            position: relative; /* Needed for absolute positioning of overlay */
        }
        .story-card:hover {
            transform: translateY(-5px);
        }
        .story-card img {
            width: 100%;
            height: 200px; /* Fixed height for consistency */
            object-fit: cover;
            display: block;
        }
        .story-card .card-body {
            padding: 15px;
            transition: background-color 0.3s ease; /* Smooth background transition */
        }
        .story-card:hover .card-body {
            background-color: #e6e6e6; /* Lighter grey on hover, similar to image */
        }
        .story-card .card-title {
            font-family: 'Ancizar Serif', serif; /* Card title font */
            font-size: 1.3em;
            color: #333;
            margin-bottom: 10px;
        }
        .story-card .card-text {
            font-size: 0.95em;
            color: #555;
            line-height: 1.5;
            margin-bottom: 15px;
        }
        .story-card:hover .card-text { /* Change text color on hover for better contrast */
            color: #333;
        }
        .story-card .story-date {
            font-size: 0.85em;
            color: #888;
            text-align: right;
        }
        .story-card:hover .story-date { /* Hide or fade out date on hover if preferred */
            opacity: 0;
            transition: opacity 0.3s ease;
        }


        /* Hover Overlay Styles */
        .story-card .hover-overlay {
            position: absolute;
            bottom: 0; /* Position at the bottom of the card body */
            left: 0;
            width: 100%;
            height: 100%; /* Cover the entire card body */
            background-color: #e6e6e6; /* Match card body hover background */
            display: flex;
            justify-content: center;
            align-items: center;
            opacity: 0; /* Initially hidden */
            transition: opacity 0.3s ease;
            pointer-events: none; /* Allows clicks to pass through when hidden */
            flex-direction: column; /* To center button vertically */
            padding-bottom: 20px; /* Space for the button */
        }
        .story-card:hover .hover-overlay {
            opacity: 1; /* Visible on hover */
            pointer-events: auto; /* Enable clicks on hover */
        }

        .btn-read-more {
            background-color: #6c757d; /* A subtle grey button */
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.2s ease;
        }
        .btn-read-more:hover {
            background-color: #5a6268; /* Darker grey on hover */
            color: white; /* Ensure text stays white */
        }

        /* Adjust content visibility on hover */
        .story-card:hover .card-title,
        .story-card:hover .card-text {
            opacity: 0; /* Fade out existing text */
            transition: opacity 0.3s ease;
        }
        /* Make space for the overlay on hover */
        .story-card:hover .card-body {
            /* If elements inside need to move, you might adjust padding or height here */
        }

    </style>
</head>
<body>
    <div class="container">
        <header>
            <div class="cloud-left d-none d-md-block"></div>
            <div class="cloud-right d-none d-md-block"></div>
            <h1>Kainara's Stories</h1>
            <p class="tagline">Bangga Pakai Karya UMKM</p>
        </header>

        <main>
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4 py-4">
                @php
                    // Example data to simulate fetching stories from a database
                    // In a real application, you'd pass $stories from your controller
                    $stories = [
                        [
                            'image' => asset('images/hoffman_bali_pop.jpg'),
                            'title' => 'The Gleaming Legacy: Songket, Indonesia\'s Golden Fabric',
                            'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam non tellus nec libero consectetur posuere. Vivamus nec dui eget nisi fermentum dictum.',
                            'date' => 'May 21st, 2025'
                        ],
                        [
                            'image' => asset('images/hoffman_bali_pop.jpg'),
                            'title' => 'Timeless Weaves: Exploring Indonesian Songket Artistry',
                            'description' => 'Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip.',
                            'date' => 'May 22nd, 2025'
                        ],
                        [
                            'image' => asset('images/hoffman_bali_pop.jpg'),
                            'title' => 'Golden Threads of Heritage: A Journey Through Songket',
                            'description' => 'Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident.',
                            'date' => 'May 23rd, 2025'
                        ],
                        [
                            'image' => asset('images/hoffman_bali_pop.jpg'),
                            'title' => 'The Craft of Kings: Understanding Songket Fabric',
                            'description' => 'Aliquam erat volutpat. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.',
                            'date' => 'May 24th, 2025'
                        ],
                         [
                            'image' => asset('images/hoffman_bali_pop.jpg'),
                            'title' => 'Songket: Indonesia\'s Woven Masterpiece',
                            'description' => 'Curabitur pretium tincidunt lacus. Nulla facilisi. Nulla vel quam nec ipsum condimentum maximus a quis libero.',
                            'date' => 'May 25th, 2025'
                        ],
                        [
                            'image' => asset('images/hoffman_bali_pop.jpg'),
                            'title' => 'The Art of Songket: Beyond Just Fabric',
                            'description' => 'Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Donec eu massa et magna facilisis.',
                            'date' => 'May 26th, 2025'
                        ],
                        [
                            'image' => asset('images/hoffman_bali_pop.jpg'),
                            'title' => 'Cultural Threads: The Story of Songket',
                            'description' => 'Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Mauris vitae libero quis nulla.',
                            'date' => 'May 27th, 2025'
                        ],
                        [
                            'image' => asset('images/hoffman_bali_pop.jpg'),
                            'title' => 'The Richness of Songket: A Cultural Treasure',
                            'description' => 'Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus.',
                            'date' => 'May 28th, 2025'
                        ],
                    ];
                @endphp

                @foreach ($stories as $story)
                    <div class="col">
                        {{-- NOTE: Component call changed to x-stories.story-card --}}
                        <x-stories.story-card
                            :image="$story['image']"
                            :title="$story['title']"
                            :description="$story['description']"
                            :date="$story['date']"
                        />
                    </div>
                @endforeach
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eJq6tyIzN/Ajt9kU5eGfQ" crossorigin="anonymous"></script>
</body>
</html>