<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Our Products</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('css/fonts.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <style>
        body {
            font-family: 'AncizarSerif', serif;
        }

        .product-card {
            background-color: #e9dfcf;
            text-align: center;
            padding: 1rem;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .product-card .product-title {
            margin: 0.5rem 0;
        }

        .product-title {
            font-weight: bold;
            font-size: 1.5rem;
        }

        .product-price {
            font-size: 1.3rem;
        }

        .product-container {
            height: 100%;
        }

        h1.display-5 {
            font-size: 4.5rem;
            font-weight: bold;
        }

        p.text-muted {
            font-size: 1.5rem;
        }

        .filter-line {
            border-top: 1px solid #000;
        }


        .filter-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
        }

        .dropdown-toggle::after {
            display: none;
        }

        .filter-label {
            font-size: 1.5rem;
        }

        .custom-caret {
            font-size: 0.9rem;
            position: relative;
            top: 3px;
        }

        .btn.border {
            background-color: #fff;
            font-size: 1rem;
        }

        .btn.border:hover {
            background-color: #f8f9fa;
        }

        .product-card .card-body {
            padding: 1rem;
        }

    </style>
</head>

<body class="bg-white">
    <main class="container-fluid py-5 px-5 flex-grow-1 position-relative">
        <div class="container text-center">
            <div class="header">
                <div class="row align-items-center justify-content-center">
                    <div class="col-md-3 d-none d-md-block">
                        <img src="{{ asset('images/awan.png') }}" alt="cloud left" class="img-fluid cloud-image" />
                    </div>
                    <div class="col-md-6">
                        <h1 class="display-5">Our Products</h1>
                        <p class="text-muted lead">Bangga Pakai Karya UMKM</p>
                    </div>
                    <div class="col-md-3 d-none d-md-block">
                        <img src="{{ asset('images/awankanan.png') }}" alt="cloud right"
                            class="img-fluid cloud-image" />
                    </div>
                </div>
            </div>
        </div>

        <div class="filter-header d-flex justify-content-between">
            <div class="dropdown"> {{-- Changed to Bootstrap dropdown --}}
                <a class="dropdown-toggle text-dark text-decoration-none filter-label d-flex align-items-center"
                    href="#" role="button" id="originDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    Origin <i class="bi bi-caret-down-fill custom-caret ms-3"></i>
                </a>
                <ul class="dropdown-menu p-3" aria-labelledby="originDropdown" style="min-width: 1000px;">
                  <div class="row row-cols-2 row-cols-md-5 g-2 fs-5">
                      @foreach (['Central Java', 'Yogyakarta', 'East Nusa Tenggara', 'Papua', 'West Java'] as $origin)
                          <div class="col">
                            <div class="form-check d-flex align-items-center">
                                <input class="form-check-input origin-checkbox me-2" type="checkbox"
                                    data-origin="{{ $origin }}"
                                    {{ in_array($origin, request()->input('origins', [])) ? 'checked' : '' }}>
                                <label class="form-check-label">{{ $origin }}</label>
                            </div>
                          </div>
                      @endforeach
                  </div>
              </ul>
            </div>

            <div class="dropdown">
                <a class="dropdown-toggle text-dark text-decoration-none filter-label d-flex align-items-center"
                    href="#" role="button" id="colorDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    Color <i class="bi bi-caret-down-fill custom-caret ms-3"></i>
                </a>
                <div class="dropdown-menu p-3" aria-labelledby="colorDropdown" style="min-width: 400px;">
                    <div class="row row-cols-2 row-cols-md-3 g-2">
                        @foreach (['Red', 'Blue', 'Green', 'Yellow', 'Black', 'White'] as $color)
                          <div class="col">
                            <div class="color-option d-flex align-items-center cursor-pointer px-2 py-1 rounded"
                                data-color="{{ $color }}" style="cursor: pointer;">
                                <span class="rounded-circle d-inline-block"
                                    style="width: 16px; height: 16px; background-color: {{ strtolower($color) }}; border: 1px solid #999;"></span>
                                <span class="text-capitalize ms-2">{{ $color }}</span>
                            </div>
                          </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="dropdown">
                <a class="dropdown-toggle text-dark text-decoration-none filter-label d-flex align-items-center justify-content-end"
                    href="#" role="button" id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-sliders2-vertical me-3"></i>
                    Filter
                    <i class="bi bi-caret-down-fill custom-caret ms-3"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end fs-5 text-end" aria-labelledby="filterDropdown">
                    <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort' => 'az']) }}">Alphabetically,
                            A-Z</a></li>
                    <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort' => 'za']) }}">Alphabetically,
                            Z-A</a></li>
                    <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort' => 'price_low']) }}">Price,
                            Low to High</a></li>
                    <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort' => 'price_high']) }}">Price,
                            High to Low</a></li>
                </ul>
            </div>
        </div>


        <div class="filter-line"></div>


        {{-- Selected Filters --}}
        @if (!empty($originFilter) || !empty($colorFilter) || request()->has('sort'))
            <div class="d-flex flex-wrap align-items-center gap-3">
                <h4 class="me-1" style="font-size: 1.25rem;">Filtered by:</h4>

                {{-- Origin Filters --}}
                @foreach ((array) $originFilter as $selectedOrigin)
                    <form method="GET" action="{{ route('products.index') }}" class="d-inline">
                        @foreach ((array) $originFilter as $origin)
                            @if ($origin !== $selectedOrigin)
                                <input type="hidden" name="origins[]" value="{{ $origin }}">
                            @endif
                        @endforeach
                        @if (request()->has('sort'))
                            <input type="hidden" name="sort" value="{{ request('sort') }}">
                        @endif
                        <button type="submit" class="btn border d-flex align-items-center px-3 py-2"
                            style="font-size: 1.1rem;">
                            <span class="me-3">{{ $selectedOrigin }}</span>
                            <i class="bi bi-x-lg" style="font-size: 0.7rem;"></i>
                        </button>
                    </form>
                @endforeach

                {{-- Color Filters --}}
                @foreach ((array) $colorFilter as $selectedColor)
                    <form method="GET" action="{{ route('products.index') }}" class="d-inline">
                        @foreach ((array) $colorFilter as $color)
                            @if ($color !== $selectedColor)
                                <input type="hidden" name="colors[]" value="{{ $color }}">
                            @endif
                        @endforeach

                        @foreach ((array) $originFilter as $origin)
                            <input type="hidden" name="origins[]" value="{{ $origin }}">
                        @endforeach

                        @if (request()->has('sort'))
                            <input type="hidden" name="sort" value="{{ request('sort') }}">
                        @endif

                        <button type="submit" class="btn border d-flex align-items-center px-3 py-2"
                            style="font-size: 1.1rem;">
                            <span class="me-2 d-flex align-items-center">
                                <span class="rounded-circle d-inline-block"
                                    style="width: 16px; height: 16px; background-color: {{ strtolower($selectedColor) }}; border: 1px solid #999;"></span>
                                <span class="ms-2 text-capitalize">{{ $selectedColor }}</span>
                            </span>
                            <i class="bi bi-x-lg ms-2" style="font-size: 0.7rem;"></i>
                        </button>
                    </form>
                @endforeach

                {{-- Sort Filter --}}
                @php
                    $sort = request('sort');
                    $sortLabel = [
                        'az' => 'Alphabetically, A-Z',
                        'za' => 'Alphabetically, Z-A',
                        'price_low' => 'Price: Low to High',
                        'price_high' => 'Price: High to Low',
                    ];
                @endphp
                @if ($sort && isset($sortLabel[$sort]))
                    <form method="GET" action="{{ route('products.index') }}" class="d-inline">
                        @foreach ((array) $originFilter as $origin)
                            <input type="hidden" name="origins[]" value="{{ $origin }}">
                        @endforeach
                        <button type="submit" class="btn border d-flex align-items-center px-3 py-2"
                            style="font-size: 1.1rem;">
                            <span class="me-3">{{ $sortLabel[$sort] }}</span>
                            <i class="bi bi-x-lg" style="font-size: 0.7rem;"></i>
                        </button>
                    </form>
                @endif

                {{-- Clear All (paling kanan) --}}
                <div class="ms-auto">
                    <a href="{{ route('products.index') }}" class="btn btn-outline-danger px-4 py-1"
                        style="font-size: 1.25rem;">Clear All</a>
                </div>
            </div>
        @endif

        <div class="row g-5 ">
            @forelse ($products as $product)
                <div class="col-12 col-sm-6 col-md-4">
                    <a href="{{ url('products/' . $product->id) }}" class="text-decoration-none text-dark">
                        <div class="product-container card h-100">
                            <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" class="img-fluid" />
                            <div class="product-card">
                                <div class="product-title">{{ $product->name }}</div>
                                <div class="product-price">IDR {{ number_format($product->price, 0, ',', '.') }}</div>
                            </div>
                        </div>
                    </a>
                </div>
            @empty
                <div class="col-12 text-center">
                    <p class="text-muted fs-4">Produk belum tersedia.</p>
                </div>
            @endforelse
        </div>
    </main>

    <form id="filterForm" method="GET" class="d-none">
        @foreach (['Central Java', 'Yogyakarta', 'East Nusa Tenggara', 'Papua', 'West Java'] as $origin)
            <input type="hidden" name="origins[]" value="{{ $origin }}" disabled>
        @endforeach
        @foreach (['Red', 'Blue', 'Green', 'Yellow', 'Black', 'White'] as $color)
            <input type="hidden" name="colors[]" value="{{ $color }}" disabled>
        @endforeach
    </form>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const form = document.getElementById('filterForm');
        const colorInputs = Array.from(form.querySelectorAll('input[name="colors[]"]'));
        const originInputs = Array.from(form.querySelectorAll('input[name="origins[]"]')); // Get origin inputs

        function syncColorsToForm(selectedColors) {
            colorInputs.forEach(input => {
                input.disabled = !selectedColors.includes(input.value);
            });
        }

        // Initialize selected colors in the form on page load
        const initialSelectedColors = Array.from(document.querySelectorAll('.color-option.bg-light'))
            .map(el => el.dataset.color);
        syncColorsToForm(initialSelectedColors);


        document.querySelectorAll('.color-option').forEach(el => {
            el.addEventListener('click', () => {
                const color = el.dataset.color;
                el.classList.toggle('bg-light');

                const activeColors = Array.from(document.querySelectorAll('.color-option.bg-light'))
                    .map(el => el.dataset.color);

                syncColorsToForm(activeColors);
                form.submit();
            });
        });

        // Synchronize origin checkboxes to form
        document.querySelectorAll('.origin-checkbox').forEach((checkbox) => {
            checkbox.addEventListener('change', () => {
                originInputs.forEach(i => i.disabled = true); // Disable all origin inputs first
                document.querySelectorAll('.origin-checkbox:checked').forEach(cb => {
                    const match = originInputs.find(i => i.value === cb.dataset.origin);
                    if (match) match.disabled = false; // Enable only checked ones
                });
                form.submit();
            });
        });

        // Handle initial state of origin checkboxes
        window.addEventListener('load', () => {
            originInputs.forEach(i => i.disabled = true); // Disable all on load
            document.querySelectorAll('.origin-checkbox:checked').forEach(cb => {
                const match = originInputs.find(i => i.value === cb.dataset.origin);
                if (match) match.disabled = false; // Enable checked ones based on request
            });
        });
    </script>
</body>

</html>