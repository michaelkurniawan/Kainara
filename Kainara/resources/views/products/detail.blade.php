@extends('layouts.app')

@section('title', $product->name . ' - Detail')

@push('styles')
<style>
    :root {
        --font-primary: 'Ancizar Serif', serif;
        --font-secondary: 'Ancizar Serif', serif;
    }

    .btn-link {
        text-decoration: none !important;
    }
    .btn-size {
        width: 48px;
        height: 48px;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 0;
        border: 1px solid #6c757d;
        color: #6c757d;
    }
    .card .text-muted {
        font-size: 0.9rem !important;
    }
    .text-justify {
        text-align: justify;
    }
    .review-user-name {
        text-align: center;
        font-weight: bold;
        margin-bottom: 0;
        font-size: 1.25rem;
    }
    .review-date {
        text-align: center;
        margin-bottom: 0.5rem;
        font-size: 0.85rem;
    }
    .review-rating {
        text-align: center;
        margin-bottom: 0.75rem;
    }

    .review-nav-btn {
        width: 120px;
        height: 45px;
        display: flex;
        justify-content: center;
        align-items: center;
        transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
    }
    #prev-review-btn:hover:not(:disabled) {
        background-color: #B6B09F;
        border-color: #B6B09F;
        color: white;
    }
    #next-review-btn:hover:not(:disabled) {
        background-color: #B6B09F;
        border-color: #B6B09F;
        color: white;
    }
    .review-nav-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .review-card-bg {
        background-color: #EAE4D5 !important;
    }

    .review-name-header-wrapper {
        background-color: #B6B09F;
        color: #333;
        padding: 0.75rem 1rem;
        border-top-left-radius: calc(0.375rem - 1px);
        border-top-right-radius: calc(0.375rem - 1px);
    }
    .card-body-content {
        padding: 1rem;
    }

    .review-comment {
        text-align: center;
        line-height: 1.8;
        padding: 0.5rem 0;
        min-height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .precise-star-rating {
        position: relative;
        display: inline-block;
        white-space: nowrap;
        line-height: 1;
        vertical-align: middle;
        overflow: hidden;
        color: #ccc;
    }
    .precise-star-rating .fas,
    .precise-star-rating .far {
        margin: 0;
        padding: 0;
        width: 1em;
        display: inline-block;
        text-align: center;
    }

    .precise-star-rating .precise-stars-filled,
    .precise-star-rating .precise-stars-empty {
        position: absolute;
        top: 0;
        left: 0;
        white-space: nowrap;
        height: 100%;
    }

    .precise-star-rating .precise-stars-filled {
        color: #ffc107;
        overflow: hidden;
    }

    .precise-star-rating .precise-stars-empty {
        color: #ccc;
        z-index: -1;
    }

    .btn-size.selected {
        background-color: #AD9D6C !important;
        color: white !important;
        border-color: #AD9D6C !important;
    }

    .btn-size:not(:disabled):hover {
        background-color: #AD9D6C;
        color: white;
        border-color: #AD9D6C;
        transition: background-color 0.2s ease-in-out, color 0.2s ease-in-out, border-color 0.2s ease-in-out;
    }

    .btn-size:disabled {
        background-color: #e9ecef;
        color: #6c757d;
        border-color: #e9ecef;
        cursor: not-allowed;
        opacity: 0.65;
    }

    .product-image-container {
        width: 100%;
        max-width: 736px;
        display: flex;
        justify-content: flex-start; 
        overflow: hidden;
        margin: 0;
    }

    .product-image-container img {
        width: 100%;
        height: auto;
        object-fit: contain;
        max-height: 736px;
    }

    @media (max-width: 768px) {
        .product-image-container {
            max-width: 100%;
        }
        .product-image-container img {
            max-height: 500px;
        }
    }

    .btn-add-to-cart, .btn-buy-it-now {
        font-size: 1.25rem;
        padding: 0.75rem 1.5rem;
    }

    .btn-add-to-cart:disabled, .btn-buy-it-now:disabled {
        opacity: 0.65;
        cursor: not-allowed;
    }
</style>
@endpush

@section('content')
    <div class="container-fluid px-5 py-5">

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row g-5 mb-5">
            <div class="col-lg-6">
                <div class="product-image-container">
                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="img-fluid" />
                </div>
            </div>

            <div class="col-lg-6">
                <h1 class="fw-bold mb-3 fs-1">{{ $product->name }}</h1>

                <div class="d-flex align-items-center text-secondary mb-3">
                    <i class="fas fa-location-dot fs-6 me-3"></i>
                    <span class="fs-5 d-flex align-items-center gap-2">
                        <span>{{ $product->origin }}</span>

                        @php
                            $colors = $product->variants->pluck('color')->unique();
                        @endphp

                        @if ($colors->isNotEmpty())
                            <div class="d-flex align-items-center gap-3 ms-2">
                                <span class="ms-2 me-2">|</span>
                                @foreach ($colors as $color)
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="rounded-circle d-inline-block"
                                            style="width: 16px; height: 16px; background-color: {{ strtolower($color) }}; border: 1px solid #999;">
                                        </span>
                                        <span class="text-capitalize ms-2">{{ $color }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </span>
                </div>

                <p class="text-muted text-justify mb-4 fs-5">
                    {{ $product->description }}
                </p>

                <p class="fs-3 fw-bold mb-2">IDR {{ number_format($product->price, 0, ',', '.') }}</p>

                <div class="d-flex align-items-center mb-4">
                    <div class="text-warning me-2 fs-4" id="average-rating-stars">
                    </div>
                    <span class="text-muted fs-5 ms-2" id="average-rating-text">
                    </span>
                </div>

                <p class="mb-4 fs-6 d-flex align-items-center gap-2 text-secondary" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#sizeChartModal">
                    <i class="fas fa-shirt fs-4"></i>
                    <span class="text-decoration-underline fs-4 ms-2">Size Chart</span>
                </p>

                <form action="{{ route('checkout.add') }}" method="POST" id="addToCheckoutForm">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="selected_size" id="selected_size_input">

                    @php
                        $availableSizesRaw = $product->variants->pluck('size')->unique();
                        $displayableSizes = $availableSizesRaw->filter(function ($size) {
                            return $size !== 'One Size';
                        })->sort()->toArray();
                        $hasOnlyOneSizeVariant = $availableSizesRaw->count() > 0 && count($displayableSizes) === 0;
                    @endphp

                    @if (!$hasOnlyOneSizeVariant)
                        <div class="d-flex align-items-center mb-5 fw-semibold gap-3 fs-4">
                            <span>Size</span>
                            <div class="d-flex gap-3">
                                @foreach(['XS', 'S', 'M', 'L', 'XL', 'XXL'] as $size)
                                    @if(in_array($size, $displayableSizes))
                                        <button type="button" class="btn btn-outline-secondary rounded-0 btn-size" data-size="{{ $size }}">{{ $size }}</button>
                                    @else
                                        <button type="button" class="btn btn-outline-secondary rounded-0 btn-size" disabled title="Not available for this product">{{ $size }}</button>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @else
                        <p class="mb-4 fs-4 fw-semibold">Size: One Size Only</p>
                        <input type="hidden" name="selected_size" id="selected_size_input_one_size" value="One Size">
                    @endif


                    <div class="mb-4" style="max-width: 57%;">
                        <div class="d-flex gap-2 mb-4">
                            <div class="d-flex border border-secondary px-3 py-2 justify-content-between align-items-center" style="width: 50%;">
                                <button type="button" class="btn btn-link text-dark p-0 fw-bold rounded-0 btn-minus" style="font-size: 1.5rem;">-</button>
                                <span id="quantity-display" class="fs-4">1</span>
                                <input type="hidden" name="quantity" id="quantity_input" value="1">
                                <button type="button" class="btn btn-link text-dark p-0 fw-bold rounded-0 btn-plus" style="font-size: 1.5rem;">+</button>
                            </div>
                            <button type="submit" name="action" value="add_to_cart" class="btn border-secondary rounded-0 btn-lg btn-add-to-cart" style="width: 50%;">Add to Cart</button>
                        </div>

                        <button type="submit" name="action" value="buy_now" class="btn rounded-0 btn-lg w-100 btn-buy-it-now" style="color: #FFFFFF; background-color:#B6B09F">Buy it now</button>
                    </div>
                </form>
            </div>
        </div>
        <hr>

        <div class="product-reviews-section mt-4">
            <h2 class="fw-bold mb-4 d-flex align-items-center justify-content-center fs-2">Customer Reviews (<span id="review-count-display">0</span>)</h2>

            <div id="reviews-container" class="row g-4 justify-content-center">
            </div>

            <div class="d-flex justify-content-center align-items-center mt-5 gap-3" id="pagination-controls" style="display: none;">
                <button id="prev-review-btn" class="btn btn-outline-secondary rounded-3 px-4 py-2 review-nav-btn" disabled>
                    <i class="fas fa-chevron-left me-2"></i>Previous
                </button>
                <span id="page-indicator" class="fs-5 fw-semibold text-secondary"></span>
                <button id="next-review-btn" class="btn btn-outline-dark rounded-3 px-4 py-2 review-nav-btn">
                    Next <i class="fas fa-chevron-right ms-2"></i>
                </button>
            </div>
        </div>

    </div>

    @include($sizeChartComponent)
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        let selectedSize = null;
        let quantity = 1;
        let maxQuantity = 0;

        const productVariants = @json($product->variants);
        const productReviews = @json($productReviews);
        const reviewsPerPage = 3;
        let currentPage = 0;

        const sizeStockMap = {};
        productVariants.forEach(variant => {
            if (variant.size) {
                sizeStockMap[variant.size] = (sizeStockMap[variant.size] || 0) + variant.stock;
            }
        });

        const sizeButtons = document.querySelectorAll('.btn-size');
        const selectedSizeInput = document.getElementById('selected_size_input');
        const selectedSizeInputOneSize = document.getElementById('selected_size_input_one_size');

        const minusBtn = document.querySelector('.btn-minus');
        const plusBtn = document.querySelector('.btn-plus');
        const quantityDisplay = document.querySelector('#quantity-display');
        const quantityInput = document.getElementById('quantity_input');

        const addToCartButton = document.querySelector('.btn-add-to-cart');
        const buyNowButton = document.querySelector('.btn-buy-it-now');

        const hasOnlyOneSizeVariant = {{ json_encode($hasOnlyOneSizeVariant) }};

        const reviewsContainer = document.getElementById('reviews-container');
        const prevBtn = document.getElementById('prev-review-btn');
        const nextBtn = document.getElementById('next-review-btn');
        const pageIndicator = document.getElementById('page-indicator');
        const reviewCountDisplay = document.getElementById('review-count-display');
        const paginationControls = document.getElementById('pagination-controls');
        const averageRatingStarsContainer = document.getElementById('average-rating-stars');
        const averageRatingTextContainer = document.getElementById('average-rating-text');


        function updateQuantityControls() {
            quantity = Math.min(quantity, maxQuantity);
            quantity = Math.max(1, quantity);

            quantityDisplay.innerText = quantity;
            quantityInput.value = quantity;
            quantityInput.setAttribute('max', maxQuantity);

            minusBtn.disabled = quantity <= 1;
            plusBtn.disabled = quantity >= maxQuantity;

            if (maxQuantity === 0 || (!selectedSize && !hasOnlyOneSizeVariant)) {
                addToCartButton.disabled = true;
                buyNowButton.disabled = true;
            } else {
                addToCartButton.disabled = false;
                buyNowButton.disabled = false;
            }
        }

        sizeButtons.forEach(button => {
            button.addEventListener('click', function () {
                sizeButtons.forEach(btn => btn.classList.remove('selected'));
                this.classList.add('selected');

                selectedSize = this.dataset.size;
                if (selectedSizeInput) {
                    selectedSizeInput.value = selectedSize;
                }

                maxQuantity = sizeStockMap[selectedSize] || 0;
                updateQuantityControls();
            });
        });

        minusBtn.addEventListener('click', function () {
            if (quantity > 1) {
                quantity--;
                updateQuantityControls();
            }
        });

        plusBtn.addEventListener('click', function () {
            if (quantity < maxQuantity) {
                quantity++;
                updateQuantityControls();
            }
        });

        if (hasOnlyOneSizeVariant) {
            selectedSize = 'One Size';
            if (selectedSizeInput) {
                selectedSizeInput.value = 'One Size';
            }
            if (selectedSizeInputOneSize) {
                selectedSizeInputOneSize.value = 'One Size';
            }

            const oneSizeTotalStock = productVariants.reduce((sum, variant) => {
                return variant.size === 'One Size' ? sum + variant.stock : sum;
            }, 0);
            maxQuantity = oneSizeTotalStock;
            updateQuantityControls();
        } else {
            maxQuantity = 0;
            selectedSize = null;
            updateQuantityControls();
        }

        function generateStarsHtml(rating) {
            let starsHtml = '';
            let fullStars = Math.floor(rating);
            let halfStar = (rating - fullStars) >= 0.25 && (rating - fullStars) < 0.75;
            let quarterRoundUp = (rating - fullStars) >= 0.75;
            let emptyStars = 5;

            for (let i = 0; i < fullStars; i++) {
                starsHtml += '<i class="fas fa-star"></i>';
                emptyStars--;
            }

            if (quarterRoundUp) {
                starsHtml += '<i class="fas fa-star"></i>';
                emptyStars--;
            } else if (halfStar) {
                starsHtml += '<i class="fas fa-star-half-alt"></i>';
                emptyStars--;
            }

            for (let i = 0; i < emptyStars; i++) {
                starsHtml += '<i class="far fa-star"></i>';
            }
            return starsHtml;
        }

        function renderReviews() {
            reviewsContainer.innerHTML = '';

            reviewCountDisplay.textContent = productReviews.length;

            if (productReviews.length === 0) {
                reviewsContainer.innerHTML = '<div class="col-12 text-center"><p class="text-muted fs-5">No reviews for this product yet. Be the first to review!</p></div>';
                paginationControls.style.display = 'none';
                updateAverageRatingDisplay(0, 0);
                return;
            }

            paginationControls.style.display = 'flex';

            const startIndex = currentPage * reviewsPerPage;
            const endIndex = startIndex + reviewsPerPage;
            const reviewsToDisplay = productReviews.slice(startIndex, endIndex);

            if (reviewsToDisplay.length === 0 && productReviews.length > 0 && currentPage > 0) {
                currentPage--;
                renderReviews();
                return;
            }

            reviewsToDisplay.forEach(review => {
                const colDiv = document.createElement('div');
                colDiv.className = 'col-lg-4 col-md-6 col-12 d-flex';

                const cardHtml = `
                    <div class="card w-100 rounded-3 shadow-sm review-card-bg">
                        <div class="review-name-header-wrapper">
                            <h5 class="review-user-name fs-4">${review.user_name}</h5>
                        </div>
                        <div class="card-body-content d-flex flex-column justify-content-between">
                            <div>
                                <p class="card-text text-muted mb-2 review-date">
                                    ${new Date(review.created_at).toLocaleString('en-US', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' })}
                                </p>
                                <div class="text-warning fs-5 review-rating">
                                    ${generateStarsHtml(review.rating)}
                                </div>
                            </div>
                            <p class="card-text fs-6 review-comment">${review.comment || '<span class="text-muted fst-italic">No comment provided.</span>'}</p>
                        </div>
                    </div>
                `;
                colDiv.innerHTML = cardHtml;
                reviewsContainer.appendChild(colDiv);
            });

            updatePaginationButtons();
            calculateAndDisplayAverageRating();
        }

        function updatePaginationButtons() {
            const totalPages = Math.ceil(productReviews.length / reviewsPerPage);
            if (productReviews.length > 0) {
                pageIndicator.textContent = `Page ${currentPage + 1} of ${totalPages}`;
            } else {
                pageIndicator.textContent = '';
            }

            prevBtn.disabled = currentPage === 0;
            nextBtn.disabled = (currentPage + 1) * reviewsPerPage >= productReviews.length;

            if (totalPages <= 1) {
                paginationControls.style.display = 'none';
            } else {
                paginationControls.style.display = 'flex';
            }
        }

        function calculateAndDisplayAverageRating() {
            if (productReviews.length === 0) {
                updateAverageRatingDisplay(0, 0);
                return;
            }

            let totalRatingSum = 0;
            productReviews.forEach(review => {
                totalRatingSum += review.rating;
            });
            const averageRating = totalRatingSum / productReviews.length;
            updateAverageRatingDisplay(averageRating, productReviews.length);
        }

        function updateAverageRatingDisplay(avgRating, reviewCount) {
            averageRatingStarsContainer.innerHTML = generateStarsHtml(avgRating);
            averageRatingTextContainer.textContent = `${avgRating.toFixed(1)} Stars | ${reviewCount} Reviews`;
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', () => {
                currentPage++;
                renderReviews();
            });
        }

        if (prevBtn) {
            prevBtn.addEventListener('click', () => {
                currentPage--;
                renderReviews();
            });
        }

        renderReviews();
    });
</script>
@endpush