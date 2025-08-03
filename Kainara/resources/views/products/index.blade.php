@extends('layouts.app')

@section('title', 'Our Products')

@push('styles')
    <style>
        :root {
            --font-primary: 'Ancizar Serif', serif;
            --font-secondary: 'Ancizar Serif', serif;
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

        .product-container img {
            width: 100%;
            height: 500px;
            object-fit: cover;
            object-position: center; 
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
            cursor: pointer;
        }

        .custom-caret {
            font-size: 0.9rem;
            position: relative;
            top: 3px;
        }

        .btn.border {
            background-color: #fff;
            border: 1px solid #dee2e6; /* Ensure border is visible */
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
            padding: 1.5rem 2rem;
            color: #333;
            max-height: 60vh;
            overflow-y: auto;
        }

        .filter-modal .form-check-label {
            font-size: 1rem;
        }

        .filter-modal .modal-footer {
            border-top: 1px solid #ddd;
            padding: 0.5rem 1rem;
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
            flex-shrink: 0;
        }

        .filter-modal .modal-dialog {
            margin: 0;
            max-width: none;
            position: absolute;
            transform: none !important;
        }

        #sortFilterModal .modal-dialog {
            width: 300px;
            right: 0;
            left: auto;
            top: var(--filter-line-bottom);
        }

        #originFilterModal .modal-dialog,
        #colorFilterModal .modal-dialog {
            width: var(--filter-line-width);
            left: var(--filter-line-left);
            top: var(--filter-line-bottom);
            right: auto;
        }

        .filter-modal.fade .modal-dialog {
            transition: none;
        }
        .filter-modal.show .modal-dialog {
            transform: none;
        }

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
                <div class="filter-line" id="filterLine"></div>
            </div>
        </div>

        {{-- Displaying active filters --}}
        {{-- Kondisi utama untuk menampilkan bagian "Filtered by:"
             Sekarang hanya akan muncul jika ada filter Origin, Color, Sort, atau Category (jika Category dari query).
             Gender filter TIDAK akan pernah memicu tampilan "Filtered by". --}}
        @if (!empty($originFilter) || !empty($colorFilter) || request()->has('sort') || (request()->has('category_name') && !request()->route('category_name')))
            <div class="d-flex flex-wrap align-items-center gap-3 mb-3 mt-3">
                <h4 class="me-1" style="font-size: 1.25rem;">Filtered by:</h4>

                {{-- Chip filter Gender telah Dihapus sepenuhnya dari tampilan ini,
                     karena permintaan Anda adalah tidak menampilkannya sama sekali. --}}

                {{-- Category Name Filter chip (ONLY show if from query param, NOT route param) --}}
                @if (request()->has('category_name') && !request()->route('category_name'))
                    <form method="GET" action="{{ route('products.index') }}" class="d-inline">
                        {{-- Preserve all other filters --}}
                        @foreach ((array) $originFilter as $origin)
                            <input type="hidden" name="origins[]" value="{{ $origin }}">
                        @endforeach
                        @foreach ((array) $colorFilter as $color)
                            <input type="hidden" name="colors[]" value="{{ $color }}">
                        @endforeach
                        @if (request()->has('sort'))
                            <input type="hidden" name="sort" value="{{ is_array(request('sort')) ? request('sort')[0] : request('sort') }}">
                        @endif
                        {{-- Preserve gender if it's active (from route or query, if it somehow gets back in query) --}}
                        @if ($genderFilter)
                            <input type="hidden" name="gender" value="{{ $genderFilter }}">
                        @endif
                        <button type="submit" class="btn border d-flex align-items-center px-3 py-2" style="font-size: 1.1rem;">
                            <span class="me-3">Category: {{ $categoryNameFilter }}</span>
                            <i class="bi bi-x-lg" style="font-size: 0.7rem;"></i>
                        </button>
                    </form>
                @endif

                {{-- Origin Filters --}}
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
                        @if ($genderFilter)
                            <input type="hidden" name="gender" value="{{ $genderFilter }}">
                        @endif
                        @if ($categoryNameFilter)
                            <input type="hidden" name="category_name" value="{{ $categoryNameFilter }}">
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
                        @if ($genderFilter)
                            <input type="hidden" name="gender" value="{{ $genderFilter }}">
                        @endif
                        @if ($categoryNameFilter)
                            <input type="hidden" name="category_name" value="{{ $categoryNameFilter }}">
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
                        @if ($genderFilter)
                            <input type="hidden" name="gender" value="{{ $genderFilter }}">
                        @endif
                        @if ($categoryNameFilter)
                            <input type="hidden" name="category_name" value="{{ $categoryNameFilter }}">
                        @endif
                        <button type="submit" class="btn border d-flex align-items-center px-3 py-2"
                            style="font-size: 1.1rem;">
                            <span class="me-3">{{ $sortLabel[$sort] }}</span>
                            <i class="bi bi-x-lg" style="font-size: 0.7rem;"></i>
                        </button>
                    </form>
                @endif

                <div class="ms-auto">
                    @php
                        $clearAllParams = [];
                        if (request()->route('gender')) {
                            $clearAllParams['gender'] = request()->route('gender');
                        }
                        if (request()->route('category_name')) {
                            $clearAllParams['category_name'] = request()->route('category_name');
                        }
                    @endphp
                    <a href="{{ route('products.index', $clearAllParams) }}" class="btn btn-outline-danger px-4 py-1"
                        style="font-size: 1.25rem;">Clear All</a>
                </div>
            </div>
        @endif

        <div class="row g-4 py-4 mb-4">
            @forelse ($products as $product)
                <div class="col-12 col-sm-6 col-md-4">
                    {{-- THIS IS THE CORRECTED LINK --}}
                    {{-- All product clicks go to products.show, which then redirects if it's a 'Fabric' product. --}}
                    <a href="{{ route('products.show', $product->slug) }}" class="text-decoration-none text-dark">
                        <div class="product-container card h-100">
                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="img-fluid" />
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

        {{-- Pagination Component --}}
        {{-- Pass the paginator, genderFilter, and categoryNameFilter to the pagination component --}}
        @include('Pagination.pagination-components', ['paginator' => $products, 'genderFilter' => $genderFilter, 'categoryNameFilter' => $categoryNameFilter])
    </div>

    {{-- Filter Modals (Hidden Forms for JS) --}}
    <form id="filterForm" method="GET" class="d-none">
        {{-- These are placeholders for JS to read initial values and for "clear all" logic --}}
        {{-- They are disabled because the JS reconstructs the URL directly --}}
        @foreach($availableOrigins as $origin)
            <input type="hidden" name="origins[]" value="{{ $origin }}" disabled>
        @endforeach
        @foreach($availableColors as $color)
            <input type="hidden" name="colors[]" value="{{ $color }}" disabled>
        @endforeach
        @if (request()->has('sort'))
            <input type="hidden" name="sort" value="{{ is_array(request('sort')) ? request('sort')[0] : request('sort') }}" disabled>
        @endif
        {{-- These capture the gender/category from route/query params passed by the controller --}}
        @if ($genderFilter)
            <input type="hidden" name="gender" value="{{ $genderFilter }}" disabled>
        @endif
        @if ($categoryNameFilter)
            <input type="hidden" name="category_name" value="{{ $categoryNameFilter }}" disabled>
        @endif
    </form>

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

            params.delete('page');

            const currentOrigins = params.getAll('origins[]');
            const currentColors = params.getAll('colors[]');
            const currentSort = params.get('sort');

            params.delete('origins[]');
            params.delete('colors[]');
            params.delete('sort');

            const newOrigins = updatedParams.origins !== undefined ? updatedParams.origins : currentOrigins;
            newOrigins.forEach(o => params.append('origins[]', o));

            const newColors = updatedParams.colors !== undefined ? updatedParams.colors : currentColors;
            newColors.forEach(c => params.append('colors[]', c));

            const newSort = updatedParams.sort !== undefined ? updatedParams.sort : currentSort;
            if (newSort) {
                params.append('sort', newSort);
            }

            const url = new URL(window.location.href);
            url.search = params.toString();
            window.location.href = url.toString();
        }

        function setModalPositionAndWidth() {
            const filterLineRect = filterLine.getBoundingClientRect();
            const bodyRect = document.body.getBoundingClientRect();

            const topPosition = filterLineRect.bottom;
            const leftPosition = filterLineRect.left;
            const width = filterLineRect.width;

            document.documentElement.style.setProperty('--filter-line-width', `${width}px`);
            document.documentElement.style.setProperty('--filter-line-left', `${leftPosition}px`);
            document.documentElement.style.setProperty('--filter-line-bottom', `${topPosition}px`);

            const sortFilterModalDialog = document.querySelector('#sortFilterModal .modal-dialog');
            if (sortFilterModalDialog) {
                const filterLineRight = filterLineRect.right;
                const modalWidth = 300; // This should match the width set in CSS
                const desiredLeft = filterLineRight - modalWidth;

                sortFilterModalDialog.style.left = `${desiredLeft}px`;
                sortFilterModalDialog.style.top = `${topPosition}px`;
                sortFilterModalDialog.style.right = 'auto'; // Ensure it doesn't conflict with 'right: 0' in CSS if not handled
            }
        }

        window.addEventListener('load', setModalPositionAndWidth);
        window.addEventListener('resize', setModalPositionAndWidth);

        const filterModals = document.querySelectorAll('.filter-modal');
        filterModals.forEach(modal => {
            modal.addEventListener('show.bs.modal', setModalPositionAndWidth);
        });

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

        document.querySelector('#originFilterModal .apply-origin')?.addEventListener('click', () => {
            const selectedOrigins = Array.from(document.querySelectorAll('#originFilterModal .origin-checkbox:checked'))
                .map(cb => cb.dataset.origin);
            buildAndRedirect({
                origins: selectedOrigins
            });
        });

        document.querySelector('#colorFilterModal .apply-color')?.addEventListener('click', () => {
            const selectedColors = Array.from(document.querySelectorAll('#colorFilterModal .color-checkbox:checked'))
                .map(cb => cb.dataset.color);
            buildAndRedirect({
                colors: selectedColors
            });
        });

        document.querySelector('#sortFilterModal .apply-sort')?.addEventListener('click', () => {
            const selectedSort = document.querySelector('#sortFilterModal .sort-radio:checked')?.value || null;
            buildAndRedirect({
                sort: selectedSort
            });
        });

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