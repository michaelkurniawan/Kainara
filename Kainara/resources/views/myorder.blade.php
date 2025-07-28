@extends('layouts.app')

@section('title', 'My Order')

@push('styles')
<style>
    body {
        font-family: 'AncizarSerif', serif;
        background-color: #ffffff; /* Light background for the page */
    }
    h1.display-5 {
        font-size: 4.5rem;
        font-weight: bold;
    }

    .order-card {
        background-color: #fff;
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        display: flex;
        flex-direction: column; /* Allow content to stack on small screens */
    }
    .order-header {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
        border-bottom: 1px dashed #eee;
        padding-bottom: 10px;
    }
    .order-header .icon {
        font-size: 24px;
        margin-right: 10px;
        color: #777;
    }
    .order-header .order-info strong {
        font-size: 1.1rem;
        color: #333;
    }
    .order-header .order-info span {
        font-size: 0.9rem;
        color: #888;
    }
    .order-status {
        background-color: #e0ffe0; /* Light green for "In Delivery" */
        color: #28a745; /* Darker green text */
        padding: 5px 10px;
        border-radius: 5px;
        font-weight: bold;
        font-size: 0.85rem;
        margin-left: auto; /* Push to the right */
    }

    .status-awaiting-payment { background-color: #fff3cd; color: #856404; } /* yellow */
    .status-order-confirmed { background-color: #d1ecf1; color: #0c5460; } /* light blue */
    .status-awaiting-shipment { background-color: #cce5ff; color: #004085; } /* blue */
    .status-shipped { background-color: #d4edda; color: #155724; } /* green */
    .status-delivered { background-color: #d4edda; color: #155724; } /* green */
    .status-completed { background-color: #d4edda; color: #155724; } /* green */
    .status-canceled { background-color: #f8d7da; color: #721c24; } /* red */
    .status-returned { background-color: #f8d7da; color: #721c24; } /* red */
    .status-refunded { background-color: #f8d7da; color: #721c24; } /* red */

    .order-details-summary {
        display: flex;
        align-items: flex-start;
        flex-grow: 1;
    }
    .order-details-summary img {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 5px;
        margin-right: 15px;
        flex-shrink: 0;
    }
    .order-item-info {
        flex-grow: 1;
    }
    .order-item-info h4 {
        font-size: 1.2rem;
        font-weight: bold;
        margin-bottom: 5px;
        color: #333;
    }
    .order-item-info p {
        font-size: 0.9rem;
        color: #666;
        margin-bottom: 3px;
    }
    .order-total {
        margin-left: auto; /* Push to the right */
        text-align: right;
        flex-shrink: 0;
        min-width: 120px; /* Ensure space for total */
    }
    .order-total strong {
        font-size: 1.3rem;
        color: #333;
    }
    .order-actions {
        display: flex;
        gap: 10px;
        margin-top: 15px;
        justify-content: flex-end; /* Align buttons to the right */
        width: 100%; /* Take full width */
    }
    .order-actions .btn {
        padding: 8px 15px;
        border-radius: 5px;
        font-size: 0.9rem;
    }
    .btn-transaction-detail {
        background-color: #B6B09F;
        color: white;
    }
    .btn-transaction-detail:hover {
        background-color: #9c9685;
        color: white;
    }
    .btn-track {
        background-color: rgb(138, 32, 32);
        color: white;
        border: none;
    }
    .btn-track:hover {
        background-color: rgb(117, 27, 27);
        color: white;
    }
    /* Gaya baru untuk tombol 'Complete Order' */
    .btn-complete-order {
        background-color: #28a745; /* Warna hijau */
        color: white;
        border: none;
    }
    .btn-complete-order:hover {
        background-color: #218838; /* Hijau lebih gelap saat hover */
        color: white;
    }
    /* Gaya untuk tombol saat disabled */
    .btn-complete-order:disabled {
        background-color: #a7a7a7; /* Warna abu-abu saat disabled */
        cursor: not-allowed;
    }
    .empty-order-card {
        background-color: #fff;
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 40px 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        text-align: center;
    }
    .empty-order-card p.lead {
        font-size: 1.25rem;
        color: #000;
    }
    .empty-order-card p {
        color: #777;
        margin-bottom: 10px;
    }
    .empty-order-card .btn {
        font-size: 1rem;
        padding: 10px 25px;
    }

    /* Gaya untuk bintang di modal review */
    #stars .fa-star {
        cursor: pointer;
        transition: color 0.2s ease;
        color: #ccc; /* Default empty star color */
    }
    #stars .fa-star.fas {
        color: #ffc107; /* Filled star color */
    }


    /* Responsive adjustments */
    @media (max-width: 768px) {
        .order-card {
            flex-direction: column;
            align-items: flex-start;
        }
        .order-header {
            flex-wrap: wrap; /* Allow header items to wrap */
        }
        .order-status {
            margin-left: 0; /* Remove auto-margin on small screens */
            margin-top: 10px; /* Add some space */
            width: fit-content; /* Adjust width */
        }
        .order-details-summary {
            flex-direction: column;
            width: 100%;
        }
        .order-details-summary img {
            margin-bottom: 10px;
            margin-right: 0;
        }
        /* Perbaikan: Atur text-align dan padding-top hanya jika perlu pada breakpoint ini */
        .order-total {
            margin-left: 0;
            width: 100%;
            text-align: left; /* Align total left on small screens */
            margin-top: 10px;
            border-top: 1px dashed #eee;
            padding-top: 10px;
        }
        .order-actions {
            flex-direction: column;
            align-items: stretch; /* Stretch buttons to full width */
        }
    }
</style>
@endpush

@section('content')
    <div class="container-fluid py-5 px-5">
        <x-bangga title="My Order" subtitle="Bangga Pakai Karya UMKM" />

        <div class="card-order mt-5">
            @forelse ($orders as $order)
                <div class="order-card">
                    <div class="order-header">
                        <i class="fas fa-shopping-basket icon me-3"></i>
                        <div class="order-info">
                            <strong>Order</strong> | {{ \Carbon\Carbon::parse($order->created_at)->format('d F Y') }}
                            <br>
                            <span>INV/{{ \Carbon\Carbon::parse($order->created_at)->format('Ymd') }}/M/{{ $order->id }}</span>
                        </div>
                        <span class="order-status status-{{ Str::slug($order->status) }}">{{ $order->status }}</span>
                    </div>

                    <div class="order-details-summary">
                        @php
                            $firstItem = $order->orderItems->first();
                            $remainingItemsCount = $order->orderItems->count() - 1;
                        @endphp

                        @if ($firstItem)
                            <img src="{{ asset('storage/' . $firstItem->product_image) }}" alt="{{ $firstItem->product_name }}">
                            <div class="order-item-info">
                                <h4>{{ $firstItem->product_name }}</h4>
                                <p>{{ $firstItem->quantity }} item x IDR {{ number_format($firstItem->price, 0, ',', '.') }}</p>
                                @if ($remainingItemsCount > 0)
                                    <p>+ {{ $remainingItemsCount }} other items</p>
                                @endif
                            </div>
                        @else
                            <div class="order-item-info">
                                <h4>No items in this order.</h4>
                            </div>
                        @endif

                        <div class="order-total">
                            Total
                            <br>
                            <strong>IDR {{ number_format($order->grand_total, 0, ',', '.') }}</strong>
                        </div>
                    </div>

                    <div class="order-actions">
                        @if ($order->status === 'Awaiting Payment')
                            <a href="{{ route('payment.continue', $order->id) }}" class="btn btn-track">Continue Payment</a>
                        @elseif ($order->status === 'Delivered')
                            {{-- Tombol "Complete Order" memicu modal --}}
                            <button type="button" class="btn btn-complete-order" data-bs-toggle="modal" data-bs-target="#reviewModal" data-order-id="{{ $order->id }}">
                                Complete Order
                            </button>
                            {{-- Opsi: tetap tampilkan Transaction Detail meskipun status Delivered --}}
                            <a href="{{ route('order.details', $order->id) }}" class="btn btn-transaction-detail">Transaction Detail</a>
                        @else
                            <a href="{{ route('order.details', $order->id) }}" class="btn btn-transaction-detail">Transaction Detail</a>
                        @endif

                        {{-- Tombol "Track" ditampilkan secara terpisah untuk status yang relevan --}}
                        @if (in_array($order->status, ['Awaiting Shipment', 'Shipped', 'In Delivery']))
                            <button class="btn btn-track">Track</button>
                        @endif
                    </div>
                </div>
            @empty
                <div class="empty-order-card">
                    <p class="lead">You don't have any orders yet.</p>
                    <p>Start shopping now and support local UMKM!</p>
                    <a href="{{ route('products.index') }}" class="btn btn-primary mt-3" style="background-color: #B6B09F; border-color: #B6B09F;">Start Shopping</a>
                </div>
            @endforelse
        </div>
    </div>

    <div class="modal fade" id="reviewModal" tabindex="-1" aria-labelledby="reviewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="reviewModalLabel">Submit Review & Complete Order</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="reviewForm">
                    @csrf
                    <input type="hidden" name="order_id" id="review_order_id">
                    <div class="modal-body">
                        <div class="mb-3 text-center">
                            <label for="rating" class="form-label fs-5">Your Rating</label>
                            <div id="stars" class="text-warning fs-3">
                                <i class="far fa-star" data-rating="1"></i>
                                <i class="far fa-star" data-rating="2"></i>
                                <i class="far fa-star" data-rating="3"></i>
                                <i class="far fa-star" data-rating="4"></i>
                                <i class="far fa-star" data-rating="5"></i>
                            </div>
                            <input type="hidden" name="rating" id="review_rating_input" value="0">
                            <small class="form-text text-muted" id="rating-text">Click on stars to rate</small>
                        </div>
                        <div class="mb-3">
                            <label for="comment" class="form-label">Your Comment (Optional)</label>
                            <textarea class="form-control" id="comment" name="comment" rows="3" placeholder="Share your experience..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-submit-review" style="background-color: #B6B09F; border-color: #B6B09F;">Submit Review</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const reviewModalElement = document.getElementById('reviewModal');
        // Pastikan Anda menginisialisasi modal Bootstrap dengan benar
        const reviewModal = new bootstrap.Modal(reviewModalElement);
        const reviewForm = document.getElementById('reviewForm');
        const reviewOrderIdInput = document.getElementById('review_order_id');
        const reviewRatingInput = document.getElementById('review_rating_input');
        const starIcons = reviewModalElement.querySelectorAll('#stars i.fa-star');
        const ratingText = document.getElementById('rating-text');
        const commentInput = document.getElementById('comment');
        const submitReviewButton = reviewForm.querySelector('.btn-submit-review'); // Ambil tombol submit

        let currentRating = 0;

        reviewModalElement.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const orderId = button.dataset.orderId;
            reviewOrderIdInput.value = orderId;

            // Reset modal fields for a fresh entry
            currentRating = 0;
            reviewRatingInput.value = 0;
            updateStarsDisplay(0);
            commentInput.value = '';
            ratingText.textContent = 'Click on stars to rate';
            submitReviewButton.disabled = false; // Pastikan tombol aktif saat modal dibuka
            submitReviewButton.textContent = 'Submit Review'; // Reset teks tombol
        });

        function updateStarsDisplay(rating) {
            starIcons.forEach(star => {
                const starValue = parseInt(star.dataset.rating);
                if (starValue <= rating) {
                    star.classList.remove('far');
                    star.classList.add('fas');
                } else {
                    star.classList.remove('fas');
                    star.classList.add('far');
                }
            });
        }

        starIcons.forEach(star => {
            star.addEventListener('click', function() {
                currentRating = parseInt(this.dataset.rating);
                reviewRatingInput.value = currentRating;
                updateStarsDisplay(currentRating);
                ratingText.textContent = `You rated: ${currentRating} Stars`;
            });

            star.addEventListener('mouseover', function() {
                const hoverRating = parseInt(this.dataset.rating);
                updateStarsDisplay(hoverRating);
            });
            star.addEventListener('mouseout', function() {
                updateStarsDisplay(currentRating);
            });
        });

        reviewForm.addEventListener('submit', function(e) {
            e.preventDefault();

            if (currentRating === 0) {
                alert('Please select a star rating before submitting your review.');
                return;
            }

            submitReviewButton.disabled = true; // Nonaktifkan tombol
            submitReviewButton.textContent = 'Submitting...'; // Ubah teks tombol

            const formData = new FormData(this);
            // Tidak perlu menambahkan _token lagi karena sudah ada di FormData dari @csrf
            // formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

            fetch('{{ route('reviews.store') }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                // Selalu coba parse sebagai JSON, karena server akan mengirim JSON untuk error juga
                return response.json().then(data => {
                    if (!response.ok) {
                        // Jika respons bukan 2xx (misal 400, 403, 500)
                        throw new Error(data.message || 'Server error occurred.');
                    }
                    return data; // Ini adalah data sukses
                });
            })
            .then(data => {
                // Ini akan dipanggil jika response.ok adalah true
                if (data.success) {
                    alert(data.message);
                    reviewModal.hide();
                    window.location.reload();
                } else {
                    // Ini seharusnya tidak terpanggil jika response.ok, tapi untuk jaga-jaga
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                // Ini akan menangani error dari network atau error yang dilempar dari .then pertama
                console.error('Fetch error:', error);
                alert('An error occurred: ' + error.message);
            })
            .finally(() => {
                // Pastikan tombol diaktifkan kembali setelah selesai (baik sukses atau gagal)
                submitReviewButton.disabled = false;
                submitReviewButton.textContent = 'Submit Review';
            });
        });
    });
</script>
@endpush