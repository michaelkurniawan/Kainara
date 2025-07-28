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
        font-size: 0.85rem; /* Make date slightly smaller */
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
        border-color: #B6B09F; /* Match hover border to background */
        color: white; /* Ensure text is white on hover */
    }
    #next-review-btn:hover:not(:disabled) {
        background-color: #B6B09F;
        border-color: #B6B09F; /* Match hover border to background */
        color: white; /* Ensure text is white on hover */
    }
    .review-nav-btn:disabled {
        opacity: 0.5; /* Slightly less opaque than 0.6 for better contrast */
        cursor: not-allowed;
    }

    .review-card-bg {
        background-color: #EAE4D5 !important;
    }

    .review-name-header-wrapper {
        background-color: #B6B09F;
        color: #333; /* Darker text for contrast */
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

    .product-image-container {
        width: 100%;
        max-width: 736px; /* Max width for consistency */
        display: flex;
        justify-content: center; /* Center the image within its container */
        align-items: center;
        overflow: hidden;
        margin: 0 auto;
    }

    .product-image-container img {
        width: 100%; /* Make image fill container width */
        height: auto; /* Maintain aspect ratio */
        object-fit: contain; /* Ensure image fits without cropping */
        max-height: 736px; /* Max height to prevent overly large images on taller screens */
    }

    /* Responsive adjustments for image container */
    @media (max-width: 768px) {
        .product-image-container {
            max-width: 100%; /* Full width on smaller screens */
        }
        .product-image-container img {
            max-height: 500px; /* Adjust max height for mobile */
        }
    }

    .btn-add-to-cart, .btn-buy-it-now {
        font-size: 1.25rem; /* Larger font for action buttons */
        padding: 0.75rem 1.5rem; /* More padding */
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
                <h1 class="font-serif-medium mb-3 fs-1">{{ $product->name }}</h1>

                <div class="d-flex align-items-center text-secondary mb-3">
                    <i class="fas fa-location-dot fs-6 me-3"></i>
                    <span class="fs-5 d-flex align-items-center gap-2">
                        <span>{{ $product->origin }}</span>
                        <div class="d-flex align-items-center gap-3 ms-2">
                            <span class="ms-2 me-2">|</span>
                            @php
                                // Get unique colors from product variants for fabric
                                $colors = $product->variants->pluck('color')->unique();
                            @endphp
                            @if ($colors->isNotEmpty())
                                @foreach ($colors as $color)
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="rounded-circle d-inline-block"
                                            style="width: 16px; height: 16px; background-color: {{ strtolower($color) }}; border: 1px solid #999;" title="{{ $color }}">
                                        </span>
                                        <span class="text-capitalize ms-2">{{ $color }}</span>
                                    </div>
                                @endforeach
                            @else
                                {{-- If no specific colors are defined, use the primary color from the product itself, or a default --}}
                                @if ($product->color)
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="rounded-circle d-inline-block"
                                            style="width: 16px; height: 16px; background-color: {{ strtolower($product->color) }}; border: 1px solid #999;" title="{{ $product->color }}">
                                        </span>
                                        <span class="text-capitalize ms-2">{{ $product->color }}</span>
                                    </div>
                                @else
                                    <span>Multi-color</span> {{-- Fallback if no specific colors are defined --}}
                                @endif
                            @endif
                        </div>
                    </span>
                </div>

                <p class="text-muted text-justify mb-4 fs-5">
                    {{ $product->description }}
                </p>

                <p class="fs-4 font-serif-light mb-2">Size 2 x 1 Meter</p>
                
                <p class="fs-3 font-serif mb-2">IDR {{ number_format($product->price, 0, ',', '.') }}</p>

                <div class="d-flex align-items-center mb-4">
                    <div class="text-warning me-2 fs-4" id="average-rating-stars">
                    </div>
                    <span class="text-muted fs-5 ms-2" id="average-rating-text">
                    </span>
                </div>

                <form action="{{ route('checkout.add') }}" method="POST" id="addToCheckoutForm">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="selected_size" id="selected_size_input_one_size" value="One Size"> 

                    <p class="mb-2 fs-4 fw-semibold">Fabric's Vendor</p>
                    <div class="d-flex align-items-center mb-4">
                        <i class="fas fa-store fs-5 me-3" ></i>
                        <span class="fs-5">{{ $product->vendor->name ?? 'Mitra Pengrajin' }}</span>
                    </div>


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
                {{-- Reviews will be dynamically inserted here by JavaScript --}}
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
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        let quantity = 1;
        let maxQuantity = 0; // Initialize to 0, will be set from product stock

        // Pass product data and reviews dynamically from the controller
        const product = @json($product);
        const productReviews = @json($productReviews);

        const reviewsPerPage = 3;
        let currentPage = 0;

        const minusBtn = document.querySelector('.btn-minus');
        const plusBtn = document.querySelector('.btn-plus');
        const quantityDisplay = document.querySelector('#quantity-display');
        const quantityInput = document.getElementById('quantity_input');

        const addToCartButton = document.querySelector('.btn-add-to-cart');
        const buyNowButton = document.querySelector('.btn-buy-it-now');

        const reviewsContainer = document.getElementById('reviews-container');
        const prevBtn = document.getElementById('prev-review-btn');
        const nextBtn = document.getElementById('next-review-btn');
        const pageIndicator = document.getElementById('page-indicator');
        const reviewCountDisplay = document.getElementById('review-count-display');
        const paginationControls = document.getElementById('pagination-controls');
        const averageRatingStarsContainer = document.getElementById('average-rating-stars');
        const averageRatingTextContainer = document.getElementById('average-rating-text');

        // Set maxQuantity based on the fabric product's stock.
        // For fabrics, we assume 'One Size' or that the 'stock' attribute of the Product model directly applies.
        if (product.variants && product.variants.length > 0) {
            // If fabric products still use variants (e.g., for color), sum up all stock for 'One Size'
            maxQuantity = product.variants.reduce((sum, variant) => {
                // Assuming 'One Size' is the size for all fabric variants
                return variant.size === 'One Size' ? sum + variant.stock : sum;
            }, 0);
        } else {
            // If fabric products do not have variants or stock is directly on the Product model
            maxQuantity = product.stock || 100; // Fallback to 100 if stock is not defined
        }
        
        // Ensure maxQuantity is at least 1 if the product exists
        if (maxQuantity === 0 && product.id) { // If product exists but stock is 0, make it at least 1 for display logic
            maxQuantity = 1; // Or, consider disabling buttons entirely if stock is truly 0
        }


        function updateQuantityControls() {
            quantity = Math.min(quantity, maxQuantity);
            quantity = Math.max(1, quantity);

            quantityDisplay.innerText = quantity;
            quantityInput.value = quantity;
            quantityInput.setAttribute('max', maxQuantity);

            minusBtn.disabled = quantity <= 1;
            plusBtn.disabled = quantity >= maxQuantity;

            // Enable/disable add to cart/buy now based on maxQuantity (stock availability)
            const isAvailable = maxQuantity > 0;
            addToCartButton.disabled = !isAvailable;
            buyNowButton.disabled = !isAvailable;
        }

        updateQuantityControls(); // Initial call to set up controls

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

        // --- Review Display Logic (mostly same as your existing) ---

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
            reviewsContainer.innerHTML = ''; // Clear previous reviews

            reviewCountDisplay.textContent = productReviews.length; // Update total review count

            if (productReviews.length === 0) {
                reviewsContainer.innerHTML = '<div class="col-12 text-center"><p class="text-muted fs-5">Belum ada review untuk produk ini. Jadilah yang pertama memberikan review!</p></div>';
                paginationControls.style.display = 'none'; // Hide pagination if no reviews
                updateAverageRatingDisplay(0, 0); // Update average rating to 0
                return;
            }

            paginationControls.style.display = 'flex'; // Show pagination if there are reviews

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
                colDiv.className = 'col-lg-4 col-md-6 col-12 d-flex'; // Add d-flex to ensure cards are same height

                const cardHtml = `
                    <div class="card w-100 rounded-3 shadow-sm review-card-bg">
                        <div class="review-name-header-wrapper">
                            <h5 class="review-user-name fs-4">${review.user_name}</h5>
                        </div>
                        <div class="card-body-content d-flex flex-column justify-content-between">
                            <div>
                                <p class="card-text text-muted mb-2 review-date">
                                    ${new Date(review.created_at).toLocaleString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' })}
                                </p>
                                <div class="text-warning fs-5 review-rating">
                                    ${generateStarsHtml(review.rating)}
                                </div>
                            </div>
                            <p class="card-text fs-6 review-comment">${review.comment || '<span class="text-muted fst-italic">Tidak ada komentar.</span>'}</p>
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
                pageIndicator.textContent = `Halaman ${currentPage + 1} dari ${totalPages}`;
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
            averageRatingTextContainer.textContent = `${avgRating.toFixed(1)} Bintang | ${reviewCount} Review`;
        }

        // Event listeners for pagination
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