@extends('layouts.app')

@section('title', $product->name . ' - Detail')

@section('content')
    {{-- Data hardcode untuk ulasan --}}
    @php
        $hardcodedReviews = [
            [
                'user_name' => 'Budi Santoso',
                'rating' => 4.0,
                'comment' => 'Batiknya sangat bagus, kualitas kainnya premium dan warnanya cerah. Sangat direkomendasikan!',
                'created_at' => '2025-06-28 10:30:00',
            ],
            [
                'user_name' => 'Siti Aminah',
                'rating' => 4.5,
                'comment' => 'Desainnya unik dan modern. Agak tipis sedikit tapi masih nyaman dipakai.',
                'created_at' => '2025-06-25 14:15:00',
            ],
            [
                'user_name' => 'Joko Permana',
                'rating' => 5.0,
                'comment' => 'Pengiriman cepat, produk sesuai deskripsi. Istri saya sangat suka!',
                'created_at' => '2025-06-20 09:00:00',
            ],
            [
                'user_name' => 'Ayu Lestari',
                'rating' => 3.2,
                'comment' => 'Warnanya agak berbeda dari gambar, tapi masih oke. Cukup nyaman dipakai.',
                'created_at' => '2025-06-18 11:45:00',
            ],
            [
                'user_name' => 'Dewi Chandra',
                'rating' => 5.0,
                'comment' => 'Kainnya adem dan motifnya elegan. Akan beli lagi di lain waktu.',
                'created_at' => '2025-06-15 16:20:00',
            ],
            [
                'user_name' => 'Tomi Wijaya',
                'rating' => 4.8,
                'comment' => 'Ukuran pas, sesuai harapan. Warnanya juga tidak luntur setelah dicuci.',
                'created_at' => '2025-06-10 18:00:00',
            ],
            [
                'user_name' => 'Faisal Rahman',
                'rating' => 5.0,
                'comment' => 'Pelayanan memuaskan, admin responsif. Produk tiba dengan aman.',
                'created_at' => '2025-06-05 09:30:00',
            ],
            [
                'user_name' => 'Linda Susanti',
                'rating' => 2.5,
                'comment' => 'Sayang sekali, bahan kurang sesuai ekspektasi. Agak kasar.',
                'created_at' => '2025-06-01 13:00:00',
            ],
            [
                'user_name' => 'Rizky Pratama',
                'rating' => 5.0,
                'comment' => 'Produk istimewa! Sangat puas dengan pembelian ini.',
                'created_at' => '2025-05-28 17:00:00',
            ],
            [
                'user_name' => 'Sarah Wijaya',
                'rating' => 4.0,
                'comment' => 'Lumayan bagus, semoga awet.',
                'created_at' => '2025-05-25 10:00:00',
            ],
        ];

        // Hitung rata-rata rating.
        $totalRating = 0;
        foreach ($hardcodedReviews as $review) {
            $totalRating += $review['rating'];
        }
        $averageRating = count($hardcodedReviews) > 0 ? $totalRating / count($hardcodedReviews) : 0;
        $reviewCount = count($hardcodedReviews);
    @endphp

    <div class="container-fluid px-5 py-5">

        <div class="row g-5">
            <div class="col-lg-6 d-flex align-items-start justify-content-center">
                <img src="{{ asset('images/batik1.jpg') }}" alt="batik1" class="img-fluid object-fit-contain" />
            </div>

            <div class="col-lg-6">
                <h1 class="fw-bold mb-3">{{ $product->name }}</h1>

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
                    <div class="text-warning me-2 fs-4">
                        {{-- Logika bintang desimal untuk rata-rata rating (di bagian ringkasan produk) --}}
                        @php
                            $ratingHtmlSummary = '';
                            $fullStarsSummary = floor($averageRating);
                            $decimalPartSummary = $averageRating - $fullStarsSummary;

                            // Apply the quarter-fill logic for the summary as well
                            for ($i = 0; $i < $fullStarsSummary; $i++) {
                                $ratingHtmlSummary .= '<i class="fas fa-star"></i>'; // Full star
                            }

                            if ($decimalPartSummary >= 0.75) {
                                $ratingHtmlSummary .= '<i class="fas fa-star"></i>'; // Approximates full
                            } elseif ($decimalPartSummary >= 0.25) {
                                $ratingHtmlSummary .= '<i class="fas fa-star-half-alt"></i>'; // Approximates half/quarter
                            }

                            $starsRenderedSummary = floor($averageRating);
                            if ($decimalPartSummary >= 0.25) {
                                $starsRenderedSummary++; // Count partial as a "rendered" slot for empty stars
                            }
                            $emptyStarsSummary = 5 - $starsRenderedSummary;
                            for ($i = 0; $i < $emptyStarsSummary; $i++) {
                                $ratingHtmlSummary .= '<i class="far fa-star"></i>'; // Empty star
                            }
                        @endphp
                        {!! $ratingHtmlSummary !!}
                    </div>
                    <span class="text-muted fs-5">
                        {{ number_format($averageRating, 1) }} Stars | {{ $reviewCount }} Reviews
                    </span>
                </div>

                <p class="mb-4 fs-6 d-flex align-items-center gap-2 text-secondary" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#sizeChartModal">
                    <i class="fas fa-shirt fs-4"></i>
                    <span class="text-decoration-underline fs-4 ms-2">Size Chart</span>
                </p>

                <div class="d-flex align-items-center mb-5 fw-semibold gap-3 fs-4">
                    <span>Size</span>
                    <div class="d-flex gap-3">
                        @foreach (['XS','S','M','L','XL','XXL'] as $size)
                            <button type="button" class="btn btn-outline-secondary rounded-0 btn-size">{{ $size }}</button>
                        @endforeach
                    </div>
                </div>

                <div class="mb-4" style="max-width: 63.5%;">
                    <div class="d-flex gap-2 mb-3">
                        <div class="d-flex border border-secondary px-3 py-2 justify-content-between align-items-center" style="width: 50%;">
                            <button type="button" class="btn btn-link text-dark p-0 fw-bold rounded-0 btn-minus" style="font-size: 1.5rem;">-</button>
                            <span id="quantity-display" class="fs-4">1</span>
                            <button type="button" class="btn btn-link text-dark p-0 fw-bold rounded-0 btn-plus" style="font-size: 1.5rem;">+</button>
                        </div>
                        <button class="btn border-secondary rounded-0 btn-lg" style="width: 50%;">Add to Cart</button>
                    </div>

                    {{-- Changed Buy it now button color --}}
                    <button class="btn rounded-0 btn-lg w-100" style="color: #FFFFFF; background-color:#B6B09F">Buy it now</button>
                </div>
            </div>
        </div>

        ---

        <div class="product-reviews-section mt-3">
            <h2 class="fw-bold mb-4 d-flex align-items-center justify-content-center">Customer Reviews ({{ $reviewCount }})</h2>

            <div id="reviews-container" class="row g-4 justify-content-center">
            </div>

            @if ($reviewCount > 3)
            <div class="d-flex justify-content-center align-items-center mt-5 gap-3">
                <button id="prev-review-btn" class="btn btn-outline-secondary rounded-3 px-4 py-2 review-nav-btn" disabled>
                    <i class="fas fa-chevron-left me-2"></i>Previous
                </button>
                <span id="page-indicator" class="fs-5 fw-semibold text-secondary"></span>
                <button id="next-review-btn" class="btn btn-outline-dark rounded-3 px-4 py-2 review-nav-btn">
                    Next <i class="fas fa-chevron-right ms-2"></i>
                </button>
            </div>
            @endif
        </div>

    </div>

    <div class="modal fade" id="sizeChartModal" tabindex="-1" aria-labelledby="sizeChartModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content px-5">
                <div class="modal-header mt-3 border-bottom border-3">
                    <h4 class="modal-title fs-4" id="sizeChartModalLabel">Size Chart</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex justify-content-center align-items-center py-4 gap-5 flex-wrap text-center">
                        <img src="{{ asset('images/KemejaPanjang.png') }}" alt="Kemeja Panjang" class="img-fluid me-3" style="max-width: 45%;">
                        <img src="{{ asset('images/SizeKemejaPanjang.png') }}" alt="Size Kemeja Panjang" class="img-fluid" style="max-width: 45%;">
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<style>
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
    }
    .review-rating {
        text-align: center;
        margin-bottom: 0.75rem;
    }

    /* --- CSS Tombol Navigasi Ulasan --- */
    .review-nav-btn {
        width: 120px;
        height: 45px;
        display: flex;
        justify-content: center;
        align-items: center;
        transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
    }
    #prev-review-btn:hover:not(:disabled) {
        background-color: #6c757d;
        border-color: #6c757d;
        color: white;
    }
    #next-review-btn:hover:not(:disabled) {
        background-color: #343a40;
        border-color: #343a40;
        color: white;
    }
    .review-nav-btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    /* --- Warna Latar Belakang Kotak Ulasan --- */
    .review-card-bg {
        background-color: #EAE4D5 !important;
    }

    /* --- Wrapper untuk Header Nama Pengguna dalam Kartu --- */
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

    /* --- CSS untuk Komentar (Rata Tengah dan Tinggi Lebih Besar) --- */
    .review-comment {
        text-align: center;
        line-height: 1.8;
        padding: 0.5rem 0;
        min-height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* --- CSS Baru untuk Rating Bintang Presisi --- */
    .precise-star-rating {
        position: relative;
        display: inline-block;
        white-space: nowrap; /* Penting untuk menjaga bintang sejajar */
        line-height: 1; /* Menghilangkan spasi ekstra di sekitar ikon */
        vertical-align: middle; /* Untuk menyelaraskan dengan teks lain jika ada */
        overflow: hidden; /* Crucial: Hanya bagian yang terisi yang terlihat */
        /* Ukuran font akan ditentukan oleh parent (fs-4 atau fs-5) */
        color: #ccc; /* Warna default untuk bintang kosong */
    }
    .precise-star-rating .fas,
    .precise-star-rating .far {
        /* Memastikan ikon tidak memiliki margin atau padding bawaan */
        margin: 0;
        padding: 0;
        width: 1em; /* Menggunakan em agar ukuran konsisten dengan font-size */
        display: inline-block; /* Agar bisa diatur lebarnya jika perlu, meski di sini di handle overflow */
        text-align: center; /* Untuk menjaga ikon tetap di tengah ruang 1em nya */
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
        color: #ffc107; /* Warna kuning Bootstrap warning */
        overflow: hidden; /* Mengclip bintang terisi */
    }

    .precise-star-rating .precise-stars-empty {
        color: #ccc; /* Warna abu-abu untuk bintang kosong (latar belakang) */
        z-index: -1; /* Pastikan di belakang bintang yang terisi */
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        let selectedSize = null;
        let quantity = 1;

        const sizeButtons = document.querySelectorAll('.btn-size');
        sizeButtons.forEach(button => {
            button.addEventListener('click', function () {
                sizeButtons.forEach(btn => btn.classList.remove('bg-secondary', 'text-white'));
                this.classList.add('bg-secondary', 'text-white');
                selectedSize = this.innerText;
            });
        });

        const minusBtn = document.querySelector('.btn-minus');
        const plusBtn = document.querySelector('.btn-plus');
        const quantityDisplay = document.querySelector('#quantity-display');

        minusBtn.addEventListener('click', function () {
            if (quantity > 1) {
                quantity--;
                quantityDisplay.innerText = quantity;
            }
        });

        plusBtn.addEventListener('click', function () {
            quantity++;
            quantityDisplay.innerText = quantity;
        });

        const hardcodedReviews = @json($hardcodedReviews);
        const reviewsPerPage = 3;
        let currentPage = 0;

        const reviewsContainer = document.getElementById('reviews-container');
        const prevBtn = document.getElementById('prev-review-btn');
        const nextBtn = document.getElementById('next-review-btn');
        const pageIndicator = document.getElementById('page-indicator');

        /**
         * Generates the HTML for star ratings, approximating quarter fills
         * using Font Awesome's full and half stars.
         *
         * @param {number} rating The rating value (e.g., 3.2, 4.5).
         * @returns {string} HTML string of Font Awesome star icons.
         */
        function generateStarsHtml(rating) {
            let starsHtml = '';
            let remainingRating = rating;

            for (let i = 0; i < 5; i++) {
                if (remainingRating >= 1) {
                    starsHtml += '<i class="fas fa-star"></i>'; // Full star
                    remainingRating--;
                } else if (remainingRating >= 0.75) {
                    starsHtml += '<i class="fas fa-star"></i>'; // Treat as full for visual quarter-up
                    remainingRating = 0; // Consume the rest
                } else if (remainingRating >= 0.25) {
                    starsHtml += '<i class="fas fa-star-half-alt"></i>'; // Treat as half for visual quarter
                    remainingRating = 0; // Consume the rest
                } else {
                    starsHtml += '<i class="far fa-star"></i>'; // Empty star
                }
            }
            return starsHtml;
        }

        function renderReviews() {
            reviewsContainer.innerHTML = '';

            const startIndex = currentPage * reviewsPerPage;
            const endIndex = startIndex + reviewsPerPage;
            const reviewsToDisplay = hardcodedReviews.slice(startIndex, endIndex);

            if (reviewsToDisplay.length === 0 && hardcodedReviews.length > 0 && currentPage > 0) {
                currentPage--;
                renderReviews();
                return;
            } else if (hardcodedReviews.length === 0) {
                reviewsContainer.innerHTML = '<div class="col-12"><p class="text-muted fs-5">No reviews yet for this product. Be the first to review!</p></div>';
                updatePaginationButtons();
                return;
            }

            reviewsToDisplay.forEach(review => {
                const colDiv = document.createElement('div');
                colDiv.className = 'col-lg-4 col-md-6 col-12';

                const cardHtml = `
                    <div class="card h-100 rounded-3 shadow-sm review-card-bg">
                        {{-- Wrapper untuk nama pengguna dengan background warna --}}
                        <div class="review-name-header-wrapper">
                            <h5 class="review-user-name fs-4">${review.user_name}</h5>
                        </div>
                        {{-- Konten card-body lainnya dengan padding terpisah --}}
                        <div class="card-body-content">
                            <p class="card-text text-muted mb-2 review-date">
                                ${new Date(review.created_at).toLocaleString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' })}
                            </p>
                            <div class="text-warning fs-5 review-rating">
                                ${generateStarsHtml(review.rating)}
                            </div>
                            <p class="card-text fs-6 review-comment">${review.comment || '<span class="text-muted fst-italic">No comment provided.</span>'}</p>
                        </div>
                    </div>
                `;
                colDiv.innerHTML = cardHtml;
                reviewsContainer.appendChild(colDiv);
            });

            updatePaginationButtons();
        }

        function updatePaginationButtons() {
            const totalPages = Math.ceil(hardcodedReviews.length / reviewsPerPage);
            if (pageIndicator) {
                if (hardcodedReviews.length > 0) {
                    pageIndicator.textContent = `Page ${currentPage + 1} of ${totalPages}`;
                } else {
                    pageIndicator.textContent = '';
                }
            }

            if (prevBtn) {
                prevBtn.disabled = currentPage === 0;
            }
            if (nextBtn) {
                nextBtn.disabled = (currentPage + 1) * reviewsPerPage >= hardcodedReviews.length;
            }
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