@extends('layouts.app')

@section('title', 'Our Products')

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

        .dropdown-toggle::after {
            display: none; /* Hide default Bootstrap caret */
        }

        .filter-label {
            font-size: 1.5rem;
            /* Add pointer cursor to filter labels */
            cursor: pointer;
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

        /* --- Modal Styling for Consistency --- */
        .filter-modal .modal-content {
            background-color: #FFFFFF;
            overflow: hidden;
            border-radius: 0;
        }

        .filter-modal .modal-header {
            background-color: #FFFFFF;
            border-bottom: 1px solid #ddd;
            border-radius: 0;
        }

        .filter-modal .modal-header h5 {
            font-family: 'AncizarSerif', serif;
            font-weight: bold;
            font-size: 2.2rem;
            color: #333;
        }

        .filter-modal .modal-body {
            padding: 1.5rem 2rem; /* Consistent padding for all modal bodies */
            color: #333;
            max-height: 60vh;
            overflow-y: auto;
        }

        /* Ensure font size for form-check-label in modals is consistent */
        .filter-modal .form-check-label {
            font-size: 1rem; /* Consistent font size for labels */
        }

        .filter-modal .modal-footer {
            border-top: 1px solid #ddd;
            padding: 0.5rem 1rem; /* Smaller padding for modal footer */
            display: flex;
            justify-content: flex-end;
            background-color: #FFFFFF;
            gap: 1rem;
            border-radius: 0;
        }

        .filter-modal .btn-apply,
        .filter-modal .btn-clear {
            padding: 0.5rem 1.5rem;
            border-radius: 0;
            font-size: 1rem;
            font-weight: normal;
            transition: background-color 0.3s ease, border-color 0.3s ease, color 0.3s ease;
        }

        .filter-modal .btn-apply {
            background-color: #B6B09F;
            color: white;
            border: none;
        }

        .filter-modal .btn-apply:hover {
            background-color: #A09A87;
        }

        .filter-modal .btn-clear {
            background-color: transparent;
            border: 1px solid #ccc;
            color: #333;
        }

        .filter-modal .btn-clear:hover {
            background-color: #f0f0f0;
            border-color: #999;
            color: #000;
        }

        /* Specific styling for color circles in modal */
        .filter-modal .color-option-label {
            display: flex;
            align-items: center;
        }

        .filter-modal .color-option-label .color-circle {
            width: 16px;
            height: 16px;
            border-radius: 50%;
            border: 1px solid #999;
            margin-right: 0.5rem;
            flex-shrink: 0; /* Prevent shrinking */
        }

        /* Adjustments for modal positioning and width */
        .filter-modal .modal-dialog {
            margin: 0; /* Remove default margin */
            max-width: none; /* Allow custom width */
            position: absolute; /* Absolute positioning */
            transform: none !important; /* Override Bootstrap's center transform */
        }

        /* Specifically for sort filter modal to make it smaller and right-aligned */
        #sortFilterModal .modal-dialog {
            width: 300px; /* Adjust this value as needed for smaller width */
            right: 0; /* Align to the right */
            left: auto; /* Ensure it's not trying to align left as well */
            top: var(--filter-line-bottom); /* Maintain vertical position */
        }

        /* For origin and color filter modals, keep previous width */
        #originFilterModal .modal-dialog,
        #colorFilterModal .modal-dialog {
            width: var(--filter-line-width); /* Keep previous width */
            left: var(--filter-line-left); /* Keep previous left alignment */
            top: var(--filter-line-bottom); /* Keep previous top alignment */
            right: auto; /* Ensure it's not trying to align right */
        }


        /* Remove fade animation */
        .filter-modal.fade .modal-dialog {
            transition: none;
        }
        .filter-modal.show .modal-dialog {
            transform: none;
        }

        /* Make modal backdrop transparent */
        .modal-backdrop {
            opacity: 0 !important;
            background-color: transparent !important;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-5 px-5">
        <x-bangga title="Kainara's Products" subtitle="Bangga Pakai Karya UMKM" />
        <div class="filter-header d-flex mt-4" id="filterHeader">
            <div class="d-flex justify-content-start mb-1 flex-grow-1">
                <div class="filter-group position-relative me-5">
                    <div class="dropdown">
                        <a class="dropdown-toggle text-dark text-decoration-none filter-label d-flex align-items-center"
                            href="#" role="button" id="originDropdown" data-bs-toggle="modal"
                            data-bs-target="#originFilterModal" data-caret-id="originCaret">
                            Origin <i class="bi bi-caret-down-fill custom-caret ms-2" id="originCaret"></i>
                        </a>
                    </div>
                </div>

                {{-- Color Filter Button --}}
                <div class="filter-group position-relative me-4">
                    <div class="dropdown">
                        <a class="dropdown-toggle text-dark text-decoration-none filter-label d-flex align-items-center"
                            href="#" role="button" id="colorDropdown" data-bs-toggle="modal"
                            data-bs-target="#colorFilterModal" data-caret-id="colorCaret">
                            Color
                            <i class="bi bi-caret-down-fill custom-caret ms-2" id="colorCaret"></i>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Sort Filter Button --}}
            <div class="filter-group position-relative mb-1">
                <div class="dropdown">
                    <a class="dropdown-toggle text-dark text-decoration-none filter-label d-flex align-items-center"
                        href="#" role="button" id="filterDropdown" data-bs-toggle="modal"
                        data-bs-target="#sortFilterModal" data-caret-id="sortCaret">
                        <i class="bi bi-sliders2-vertical me-2"></i>
                        Filter
                        <i class="bi bi-caret-down-fill custom-caret ms-2" id="sortCaret"></i>
                    </a>
                </div>
            </div>

            <div class="w-100 position-absolute start-0" style="bottom: 0;">
                <div class="filter-line" id="filterLine"></div> {{-- Added ID for JavaScript --}}
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
                            <input type="hidden" name="sort"
                                value="{{ is_array(request('sort')) ? request('sort')[0] : request('sort') }}">
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
                            <input type="hidden" name="sort"
                                value="{{ is_array(request('sort')) ? request('sort')[0] : request('sort') }}">
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

    {{-- Modals for filters --}}
    <div class="modal filter-modal" id="originFilterModal" tabindex="-1" aria-labelledby="originFilterModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-body">
                    <div class="row row-cols-2 row-cols-md-3 g-3">
                        @foreach(collect($availableOrigins)->sort() as $origin)
                            <div class="col">
                                <div class="form-check">
                                    <input class="form-check-input origin-checkbox me-2" type="checkbox"
                                        id="origin-{{ Str::slug($origin) }}" data-origin="{{ $origin }}"
                                        {{ in_array($origin, request()->input('origins', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="origin-{{ Str::slug($origin) }}">
                                        {{ $origin }}
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-apply apply-origin">Apply</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal filter-modal" id="colorFilterModal" tabindex="-1" aria-labelledby="colorFilterModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-body">
                    <div class="row row-cols-2 row-cols-md-3 g-3">
                        @foreach(collect($availableColors)->sort() as $color)
                            <div class="col">
                                <div class="form-check">
                                    <input class="form-check-input color-checkbox me-2" type="checkbox"
                                        id="color-{{ Str::slug($color) }}" data-color="{{ $color }}"
                                        {{ in_array($color, request()->input('colors', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label color-option-label" for="color-{{ Str::slug($color) }}">
                                        <span class="color-circle" style="background-color: {{ strtolower($color) }};"></span>
                                        <span class="text-capitalize">{{ $color }}</span>
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-apply apply-color">Apply</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal filter-modal" id="sortFilterModal" tabindex="-1" aria-labelledby="sortFilterModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">

                <div class="modal-body">
                    @php
                        $sortOptions = [
                            'az' => 'Alphabetically, A-Z',
                            'za' => 'Alphabetically, Z-A',
                            'price_low' => 'Price, Low to High',
                            'price_high' => 'Price, High to Low',
                        ];
                        $currentSort = request('sort');
                    @endphp
                    <div class="d-flex flex-column gap-2">
                        @foreach($sortOptions as $value => $label)
                            <div class="form-check">
                                <input class="form-check-input sort-radio" type="radio" name="sort_option" id="sort-{{ $value }}" value="{{ $value }}" {{ $currentSort == $value ? 'checked' : '' }}>
                                <label class="form-check-label" for="sort-{{ $value }}">
                                    {{ $label }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-apply apply-sort">Apply</button>
                </div>
            </div>
        </div>
    </div>
@endsection

{{-- Push page-specific scripts here --}}
@push('scripts')
    <script>
        const filterForm = document.getElementById('filterForm');
        const filterHeader = document.getElementById('filterHeader');
        const filterLine = document.getElementById('filterLine');

        function getUrlParams() {
            return new URLSearchParams(window.location.search);
        }

        function buildAndRedirect(updatedParams = {}) {
            const params = getUrlParams();

            // Handle current page to reset to 1 if filters change
            params.delete('page');

            // Collect existing filters not being updated by this action
            const currentOrigins = params.getAll('origins[]');
            const currentColors = params.getAll('colors[]');
            const currentSort = params.get('sort');

            // Clear all existing filter parameters
            params.delete('origins[]');
            params.delete('colors[]');
            params.delete('sort');

            // Add origins
            const newOrigins = updatedParams.origins !== undefined ? updatedParams.origins : currentOrigins;
            newOrigins.forEach(o => params.append('origins[]', o));

            // Add colors
            const newColors = updatedParams.colors !== undefined ? updatedParams.colors : currentColors;
            newColors.forEach(c => params.append('colors[]', c));

            // Add sort
            const newSort = updatedParams.sort !== undefined ? updatedParams.sort : currentSort;
            if (newSort) {
                params.append('sort', newSort);
            }

            const url = new URL(window.location.href);
            url.search = params.toString();
            window.location.href = url.toString();
        }

        // Function to set modal position and width
        function setModalPositionAndWidth() {
            const filterLineRect = filterLine.getBoundingClientRect();
            const bodyRect = document.body.getBoundingClientRect();

            // Calculate the absolute top position relative to the viewport
            const topPosition = filterLineRect.bottom;
            const leftPosition = filterLineRect.left;
            const width = filterLineRect.width;

            // Set CSS variables for the modal dialogs
            // These are used by the origin and color modals
            document.documentElement.style.setProperty('--filter-line-width', `${width}px`);
            document.documentElement.style.setProperty('--filter-line-left', `${leftPosition}px`);
            document.documentElement.style.setProperty('--filter-line-bottom', `${topPosition}px`);

            // For the sort filter modal, we need to adjust its position to be right-aligned
            const sortFilterModalDialog = document.querySelector('#sortFilterModal .modal-dialog');
            if (sortFilterModalDialog) {
                // Get the right edge of the filter line and calculate the modal's left position
                const filterLineRight = filterLineRect.right;
                const modalWidth = 300; // This should match the width set in CSS
                const desiredLeft = filterLineRight - modalWidth;

                sortFilterModalDialog.style.left = `${desiredLeft}px`;
                sortFilterModalDialog.style.top = `${topPosition}px`;
                sortFilterModalDialog.style.right = 'auto'; // Ensure it doesn't conflict with 'right: 0' in CSS if not handled
            }
        }

        // Call the function on page load and resize
        window.addEventListener('load', setModalPositionAndWidth);
        window.addEventListener('resize', setModalPositionAndWidth);

        // Also call it when a modal is opened, in case the layout shifts slightly
        const filterModals = document.querySelectorAll('.filter-modal');
        filterModals.forEach(modal => {
            modal.addEventListener('show.bs.modal', setModalPositionAndWidth);
        });

        // --- Caret Icon Toggle Logic ---
        filterModals.forEach(modal => {
            modal.addEventListener('show.bs.modal', function(event) {
                const triggerButton = event.relatedTarget; // The button that triggered the modal
                const caretId = triggerButton.dataset.caretId;
                if (caretId) {
                    const caretIcon = document.getElementById(caretId);
                    if (caretIcon) {
                        caretIcon.classList.remove('bi-caret-down-fill');
                        caretIcon.classList.add('bi-caret-up-fill');
                    }
                }
            });

            modal.addEventListener('hidden.bs.modal', function(event) {
                // Find the triggering button (if modal was closed by clicking outside or close button)
                // This is a bit trickier than `show.bs.modal`'s `relatedTarget`.
                // We'll rely on the modal's ID to infer which caret to reset.
                let caretIdToReset;
                if (modal.id === 'originFilterModal') {
                    caretIdToReset = 'originCaret';
                } else if (modal.id === 'colorFilterModal') {
                    caretIdToReset = 'colorCaret';
                } else if (modal.id === 'sortFilterModal') {
                    caretIdToReset = 'sortCaret';
                }

                if (caretIdToReset) {
                    const caretIcon = document.getElementById(caretIdToReset);
                    if (caretIcon) {
                        caretIcon.classList.remove('bi-caret-up-fill');
                        caretIcon.classList.add('bi-caret-down-fill');
                    }
                }
            });
        });

        // --- Origin Filter Modal Logic ---
        document.querySelector('#originFilterModal .apply-origin')?.addEventListener('click', () => {
            const selectedOrigins = Array.from(document.querySelectorAll('#originFilterModal .origin-checkbox:checked'))
                .map(cb => cb.dataset.origin);
            buildAndRedirect({
                origins: selectedOrigins
            });
        });

        // The clear button for origin filter was commented out in your original code.
        // If you want to re-enable it, uncomment the following:
        /*
        document.querySelector('#originFilterModal .clear-origin')?.addEventListener('click', () => {
            document.querySelectorAll('#originFilterModal .origin-checkbox').forEach(cb => cb.checked = false);
            buildAndRedirect({
                origins: []
            }); // Clear filter
        });
        */

        // --- Color Filter Modal Logic ---
        document.querySelector('#colorFilterModal .apply-color')?.addEventListener('click', () => {
            const selectedColors = Array.from(document.querySelectorAll('#colorFilterModal .color-checkbox:checked'))
                .map(cb => cb.dataset.color);
            buildAndRedirect({
                colors: selectedColors
            });
        });

        // The clear button for color filter was commented out in your original code.
        // If you want to re-enable it, uncomment the following:
        /*
        document.querySelector('#colorFilterModal .clear-color')?.addEventListener('click', () => {
            document.querySelectorAll('#colorFilterModal .color-checkbox').forEach(cb => cb.checked = false);
            buildAndRedirect({
                colors: []
            }); // Clear filter
        });
        */

        // --- Sort Filter Modal Logic ---
        document.querySelector('#sortFilterModal .apply-sort')?.addEventListener('click', () => {
            const selectedSort = document.querySelector('#sortFilterModal .sort-radio:checked')?.value || null;
            buildAndRedirect({
                sort: selectedSort
            });
        });

        // The clear button for sort filter was commented out in your original code.
        // If you want to re-enable it, uncomment the following:
        /*
        document.querySelector('#sortFilterModal .clear-sort')?.addEventListener('click', () => {
            document.querySelectorAll('#sortFilterModal .sort-radio').forEach(radio => radio.checked = false);
            buildAndRedirect({
                sort: null
            }); // Clear sort
        });
        */

        // --- Initialize Checkboxes/Radios on modal open ---
        // This ensures the modals reflect current filters when opened
        document.getElementById('originFilterModal').addEventListener('show.bs.modal', () => {
            const currentOrigins = getUrlParams().getAll('origins[]');
            document.querySelectorAll('#originFilterModal .origin-checkbox').forEach(cb => {
                cb.checked = currentOrigins.includes(cb.dataset.origin);
            });
        });

        document.getElementById('colorFilterModal').addEventListener('show.bs.modal', () => {
            const currentColors = getUrlParams().getAll('colors[]');
            document.querySelectorAll('#colorFilterModal .color-checkbox').forEach(cb => {
                cb.checked = currentColors.includes(cb.dataset.color);
            });
        });

        document.getElementById('sortFilterModal').addEventListener('show.bs.modal', () => {
            const currentSort = getUrlParams().get('sort');
            document.querySelectorAll('#sortFilterModal .sort-radio').forEach(radio => {
                radio.checked = (radio.value === currentSort);
            });
        });
    </script>
@endpush