@extends('layouts.app')

@section('title', 'Our Products')

{{-- You can push page-specific styles here if needed --}}
@push('styles')
    <style>
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

        .origin-dropdown-menu {
            min-width: 92.5vw;
            max-width: 1382px;
        }

        .color-dropdown-menu {
            min-width: 85.2vw;
            max-width: 1277px;
        }

        .filter-dropdown-menu {
            min-width: 78.7vw;
            max-width: 1179px;
        }

        @media (max-width: 1200px) {
            .origin-dropdown-menu {
                max-width: 98vw;
            }
            .color-dropdown-menu {
                max-width: 95vw;
            }
            .filter-dropdown-menu {
                max-width: 80vw;
            }
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

        .btn-custom {
            background-color: #AD9D6C;
            color: white;
            border: none;
        }

        .btn-custom:hover {
            background-color: #EAE4D5;
            color: black;
        }

        .form-check-input:checked {
            background-color: #AD9D6C;
            border-color: #AD9D6C;
        }

        .dropdown-item:active,
        .dropdown-item.active,
        .dropdown-item:hover {
            background-color: #AD9D6C !important;
            color: white !important;
        }

        .color-option.selected {
            background-color: #AD9D6C;
            color: white;
        }

        .color-option:hover {
            background-color: #AD9D6C;
            color: white;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-5 px-5">
        <x-bangga title="Kainara's Products" subtitle="Bangga Pakai Karya UMKM" />
        <div class="filter-header d-flex mt-4">
            <div class="d-flex justify-content-end mb-1">
                <div class="filter-group position-relative me-4">
                    <div class="dropdown">
                        <a class="dropdown-toggle text-dark text-decoration-none filter-label d-flex align-items-center"
                            href="#" role="button" id="originDropdown" data-bs-toggle="dropdown" aria-expanded="false"
                            data-bs-display="static">
                            Origin <i class="bi bi-caret-down-fill custom-caret ms-1"></i>
                        </a>
                        <ul class="dropdown-menu origin-dropdown-menu fs-6 text-start mt-2 p-3" aria-labelledby="colorDropdown" style="max-width: 1382px;">
                            <div class="row row-cols-2 row-cols-md-6 g-2">
                                @foreach(collect($availableOrigins)->sort() as $origin)
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
                            <div class="d-flex justify-content-end mt-3">
                                <button class="btn btn-sm btn-custom apply-origin">Apply</button>
                            </div>
                        </ul>
                    </div>
                </div>

                <div class="filter-group position-relative me-4">
                    <div class="dropdown">
                        <a class="dropdown-toggle text-dark text-decoration-none filter-label d-flex align-items-center"
                            href="#" role="button" id="colorDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            Color
                            <i class="bi bi-caret-down-fill custom-caret ms-1"></i>
                        </a>
                       <ul class="dropdown-menu color-dropdown-menu fs-6 text-start mt-1 p-3" aria-labelledby="colorDropdown" style="max-width: 1277px;">
                            <div class="row row-cols-2 row-cols-md-6 g-2">
                                @foreach(collect($availableColors)->sort() as $color)
                                    <div class="col">
                                        <div class="form-check d-flex align-items-center">
                                            <input class="form-check-input color-checkbox me-2" type="checkbox"
                                                data-color="{{ $color }}"
                                                {{ in_array($color, request()->input('colors', [])) ? 'checked' : '' }}>
                                            <span class="rounded-circle d-inline-block"
                                                style="width: 16px; height: 16px; background-color: {{ strtolower($color) }}; border: 1px solid #999;"></span>
                                            <span class="text-capitalize ms-2">{{ $color }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="d-flex justify-content-end mt-3">
                                <button class="btn btn-sm btn-custom apply-color">Apply</button>
                            </div>
                        </ul>
                    </div>
                </div>

                <div class="filter-group position-relative">
                    <div class="dropdown">
                        <a class="dropdown-toggle text-dark text-decoration-none filter-label d-flex align-items-center"
                            href="#" role="button" id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-sliders2-vertical me-1"></i>
                            Filter
                            <i class="bi bi-caret-down-fill custom-caret ms-1"></i>
                        </a>
                        <ul class="dropdown-menu filter-dropdown-menu fs-6 text-start mt-1" aria-labelledby="filterDropdown" style="max-width: 1179px;">
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort' => 'az']) }}">Alphabetically, A-Z</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort' => 'za']) }}">Alphabetically, Z-A</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort' => 'price_low']) }}">Price, Low to High</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort' => 'price_high']) }}">Price, High to Low</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="w-100 position-absolute start-0" style="bottom: 0;">
                <div class="filter-line"></div>
            </div>
        </div>

        @if (!empty($originFilter) || !empty($colorFilter) || request()->has('sort'))
            <div class="d-flex flex-wrap align-items-center gap-3 mb-3">
                <h4 class="me-1" style="font-size: 1.25rem;">Filtered by:</h4>

                @foreach ((array) $originFilter as $selectedOrigin)
                    <form method="GET" action="{{ route('products.index') }}" class="d-inline">
                        @foreach ((array) $originFilter as $origin)
                            @if ($origin !== $selectedOrigin)
                                <input type="hidden" name="origins[]" value="{{ $origin }}">
                            @endif
                        @endforeach
                        @foreach ((array) $colorFilter as $color)
                                <input type="hidden" name="colors[]" value="{{ $color }}">
                        @endforeach
                        @if (request()->has('sort'))
                            <input type="hidden" name="sort" value="{{ is_array(request('sort')) ? request('sort')[0] : request('sort') }}">
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
                        @foreach ((array) $originFilter as $origin)
                            <input type="hidden" name="origins[]" value="{{ $origin }}">
                        @endforeach

                        @foreach ((array) $colorFilter as $color)
                            @if ($color !== $selectedColor)
                                <input type="hidden" name="colors[]" value="{{ $color }}">
                            @endif
                        @endforeach

                        @if (request()->has('sort'))
                            <input type="hidden" name="sort" value="{{ is_array(request('sort')) ? request('sort')[0] : request('sort') }}">
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
                    $rawSort = request('sort');
                    $sort = is_array($rawSort) ? $rawSort[0] : $rawSort;
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

                        @foreach ((array) $colorFilter as $color)
                            <input type="hidden" name="colors[]" value="{{ $color }}">
                        @endforeach

                        <button type="submit" class="btn border d-flex align-items-center px-3 py-2"
                            style="font-size: 1.1rem;">
                            <span class="me-3">{{ $sortLabel[$sort] }}</span>
                            <i class="bi bi-x-lg" style="font-size: 0.7rem;"></i>
                        </button>
                    </form>
                @endif

                <div class="ms-auto">
                    <a href="{{ route('products.index') }}" class="btn btn-outline-danger px-4 py-1"
                        style="font-size: 1.25rem;">Clear All</a>
                </div>
            </div>
        @endif

        <div class="row g-4 py-4 mb-4">
            @forelse ($products as $product)
                <div class="col-12 col-sm-6 col-md-4">
                    <a href="{{ route('products.show', $product->slug) }}" class="text-decoration-none text-dark">
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

        @include('Pagination.pagination-components', ['paginator' => $products])
    </div>

    <form id="filterForm" method="GET" class="d-none">
        {{-- These are for JavaScript to build the query parameters --}}
        @foreach($availableOrigins as $origin)
            <input type="hidden" name="origins[]" value="{{ $origin }}" disabled>
        @endforeach
        @foreach($availableColors as $color)
            <input type="hidden" name="colors[]" value="{{ $color }}" disabled>
        @endforeach
        {{-- Add a hidden input for sort if it's currently active, so it can be preserved by JS --}}
        @if (request()->has('sort'))
            <input type="hidden" name="sort" value="{{ is_array(request('sort')) ? request('sort')[0] : request('sort') }}" disabled>
        @endif
    </form>
@endsection

{{-- Push page-specific scripts here --}}
@push('scripts')
    <script>
        const form = document.getElementById('filterForm');
        const colorInputs = Array.from(form.querySelectorAll('input[name="colors[]"]'));
        const originInputs = Array.from(form.querySelectorAll('input[name="origins[]"]'));

        // Helper to get current URL parameters
        function getUrlParams() {
            return new URLSearchParams(window.location.search);
        }

        // Helper to redirect with intact filters
        function redirectWithFilters(updated = {}) {
            const params = getUrlParams();

            // Clear parameters that will be updated
            Object.keys(updated).forEach(key => {
                if (key === 'sort') {
                    params.delete(key); // 'sort' is not an array parameter
                } else {
                    params.delete(`${key}[]`); // Array parameters
                }
            });

            // Add new values without duplicates
            for (const [key, values] of Object.entries(updated)) {
                if (key === 'sort') {
                    if (values[0]) { // Only append if there's a sort value
                        params.append(key, values[0]);
                    }
                } else {
                    [...new Set(values)].forEach(val => {
                        params.append(`${key}[]`, val);
                    });
                }
            }

            // Redirect with all parameters intact
            const url = new URL(window.location.href);
            url.search = params.toString();
            window.location.href = url.toString();
        }

        // Apply Origin button handler
        document.querySelector('.apply-origin')?.addEventListener('click', () => {
            const selectedOrigins = Array.from(document.querySelectorAll('.origin-checkbox:checked'))
                .map(cb => cb.dataset.origin);

            const updated = {
                origins: selectedOrigins
            };

            // Preserve other filters
            const params = getUrlParams();
            if (params.has('colors[]')) {
                updated.colors = params.getAll('colors[]');
            }
            if (params.has('sort')) {
                updated.sort = [params.get('sort')];
            }

            redirectWithFilters(updated);
        });

        // Apply Color button handler
        document.querySelector('.apply-color')?.addEventListener('click', () => {
            const selectedColors = Array.from(document.querySelectorAll('.color-checkbox:checked'))
                .map(cb => cb.dataset.color);

            const updated = {
                colors: selectedColors
            };

            // Preserve other filters
            const params = getUrlParams();
            if (params.has('origins[]')) {
                updated.origins = params.getAll('origins[]');
            }
            if (params.has('sort')) {
                updated.sort = [params.get('sort')];
            }

            redirectWithFilters(updated);
        });

        // Initialize hidden form inputs based on current URL parameters
        window.addEventListener('load', () => {
            // Disable all hidden inputs in the form first
            originInputs.forEach(i => i.disabled = true);
            colorInputs.forEach(i => i.disabled = true);
            // Also handle the sort input if it exists
            const sortInput = form.querySelector('input[name="sort"]');
            if (sortInput) sortInput.disabled = true;

            // Enable only the ones that are currently checked/active
            document.querySelectorAll('.origin-checkbox:checked').forEach(cb => {
                const match = originInputs.find(i => i.value === cb.dataset.origin);
                if (match) match.disabled = false;
            });

            document.querySelectorAll('.color-checkbox:checked').forEach(cb => {
                const match = colorInputs.find(i => i.value === cb.dataset.color);
                if (match) match.disabled = false;
            });

            const currentSort = getUrlParams().get('sort');
            if (sortInput && currentSort) {
                sortInput.value = currentSort;
                sortInput.disabled = false;
            }
        });
    </script>
@endpush