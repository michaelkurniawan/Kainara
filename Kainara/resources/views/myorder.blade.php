@extends('layouts.app')

@section('title', 'My Order')

@push('styles')
<style>
    :root {
        --font-primary: 'Ancizar Serif', serif;
        --font-secondary: 'Ancizar Serif', serif;
    }
    body {
        font-family: 'AncizarSerif', serif;
        background-color: #ffffff;
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
        flex-direction: column;
    }

    .card-order .order-card:last-child {
        margin-bottom: 0;
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
        background-color: #e0ffe0;
        color: #28a745;
        padding: 5px 10px;
        border-radius: 5px;
        font-weight: bold;
        font-size: 0.85rem;
        margin-left: auto;
        text-transform: capitalize;
    }

    .status-awaiting-payment { background-color: #fff3cd; color: #856404; }
    .status-order-confirmed { background-color: #d1ecf1; color: #0c5460; }
    .status-awaiting-shipment { background-color: #cce5ff; color: #004085; }
    .status-shipped { background-color: #d4edda; color: #155724; }
    .status-delivered { background-color: #d4edda; color: #155724; }
    .status-completed { background-color: #e2f0d9; color: #4CAF50; }
    .status-canceled { background-color: #f8d7da; color: #721c24; }
    .status-returned { background-color: #fff3cd; color: #856404; }
    .status-refunded { background-color: #d1ecf1; color: #0c5460; }
    .status-partially-refunded { background-color: #e1f5fe; color: #03a9f4; }
    .status-refund-pending { background-color: #fffde7; color: #ffc107; }
    .status-refund-failed { background-color: #fce4ec; color: #e91e63; }
    .status-refund-rejected { background-color: #6c757d; color: #fff; } /* Added for Refund Rejected */


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
        margin-left: auto;
        text-align: right;
        flex-shrink: 0;
        min-width: 120px;
    }
    .order-total strong {
        font-size: 1.3rem;
        color: #333;
    }
    .order-actions {
        display: flex;
        gap: 10px;
        margin-top: 15px;
        justify-content: flex-end;
        width: 100%;
        align-items: center;
    }

    .order-actions form {
        display: inline-flex;
        margin: 0;
        padding: 0;
    }

    .order-actions .btn {
        padding: 8px 15px;
        border-radius: 5px;
        font-size: 0.9rem;
        flex-shrink: 0;
        flex-grow: 0;
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
    .btn-complete-order {
        background-color: #28a745;
        color: white;
        border: none;
    }
    .btn-complete-order:hover {
        background-color: #218838;
        color: white;
    }
    .btn-complete-order:disabled {
        background-color: #a7a7a7;
        cursor: not-allowed;
    }
    .btn-cancel-order {
        background-color: #dc3545;
        color: white;
        border: none;
    }
    .btn-cancel-order:hover {
        background-color: #c82333;
        color: white;
    }
    .btn-request-refund {
        background-color: #007bff;
        color: white;
        border: none;
    }
    .btn-request-refund:hover {
        background-color: #0056b3;
        color: white;
    }
    .btn-request-refund:disabled {
        background-color: #a7a7a7;
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

    @media (max-width: 768px) {
        .order-card {
            flex-direction: column;
            align-items: flex-start;
        }
        .order-header {
            flex-wrap: wrap;
        }
        .order-status {
            margin-left: 0;
            margin-top: 10px;
            width: fit-content;
        }
        .order-details-summary {
            flex-direction: column;
            width: 100%;
        }
        .order-details-summary img {
            margin-bottom: 10px;
            margin-right: 0;
        }
        .order-total {
            margin-left: 0;
            width: 100%;
            text-align: left;
            margin-top: 10px;
            border-top: 1px dashed #eee;
            padding-top: 10px;
        }
        .order-actions {
            flex-direction: column;
            align-items: stretch;
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
                            <span>INV/{{ \Carbon\Carbon::parse($order->created_at)->format('Ymd') }}/{{ $order->id }}</span>
                        </div>
                        {{-- LOGIC DIBERIKAN PERBAIKAN DI SINI --}}
                        @php
                            $displayStatus = $order->status;
                            $statusClass = Str::slug($order->status);

                            // Prioritize the latest refund status if available
                            if ($order->payment && $order->payment->refunds->isNotEmpty()) {
                                $latestRefund = $order->payment->refunds->sortByDesc('created_at')->first();
                                if ($latestRefund) {
                                    switch ($latestRefund->status) {
                                        case 'pending':
                                            $displayStatus = 'Refund Pending';
                                            $statusClass = 'refund-pending';
                                            break;
                                        case 'approved':
                                            $displayStatus = 'Refund Approved';
                                            $statusClass = 'approved';
                                            break;
                                        case 'rejected':
                                            $displayStatus = 'Refund Rejected';
                                            $statusClass = 'rejected';
                                            break;
                                        case 'succeeded':
                                            $displayStatus = 'Refunded'; // Fully refunded by Stripe
                                            $statusClass = 'refunded';
                                            break;
                                        case 'failed':
                                            $displayStatus = 'Refund Failed';
                                            $statusClass = 'refund-failed';
                                            break;
                                    }
                                }
                            }
                            // If no refund is active, use the order's status directly.
                            // This replaces the old logic that incorrectly showed "Order Confirmed".
                            else {
                                $displayStatus = $order->status;
                                $statusClass = Str::slug($order->status);
                            }
                        @endphp
                        <span class="order-status status-{{ $statusClass }}">{{ $displayStatus }}</span>
                    </div>

                    <div class="order-details-summary">
                        @php
                            $firstItem = $order->orderItems->first();
                            $remainingItemsCount = $order->orderItems->count() - 1;
                        @endphp

                        @if ($firstItem && $firstItem->product)
                            <img src="{{ asset('storage/' . $firstItem->product->image) }}" alt="{{ $firstItem->product->name }}">
                            <div class="order-item-info">
                                <h4>{{ $firstItem->product->name }}</h4>
                                <p>{{ $firstItem->quantity }} item x IDR {{ number_format($firstItem->price, 0, ',', '.') }}</p>
                                @if ($remainingItemsCount > 0)
                                    <p>+ {{ $remainingItemsCount }} other items</p>
                                @endif
                            </div>
                        @else
                            <div class="order-item-info">
                                <h4>No product details available.</h4>
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
                            <form action="{{ route('order.cancel', $order->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this order? This action cannot be undone and product stock will be returned.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-cancel-order">Cancel Order</button>
                            </form>
                        @elseif ($order->status === 'Delivered')
                            @php
                                $paymentStatus = $order->payment ? $order->payment->status : null;
                                $isRefundRelated = in_array($paymentStatus, ['refunded', 'partially_refunded', 'refund_pending', 'refund_failed']);
                                $hasAnyRefundRecord = $order->payment && $order->payment->refunds->isNotEmpty();
                            @endphp

                            {{-- Only show "Complete Order & Review" if not refund-managed --}}
                            @if (!$hasAnyRefundRecord)
                                <button type="button" class="btn btn-complete-order" data-bs-toggle="modal" data-bs-target="#reviewModal" data-order-id="{{ $order->id }}" {{ $order->hasReview() ? 'disabled' : '' }}>
                                    {{ $order->hasReview() ? 'Reviewed' : 'Complete Order' }}
                                </button>
                            @endif

                            {{-- Refund Request Button for FULL Refund only --}}
                            @php
                                $canInitiateFullRefund = false;
                                if ($order->payment && $order->payment->status === 'succeeded' && !$hasAnyRefundRecord) {
                                    $canInitiateFullRefund = true;
                                }
                            @endphp

                            <a href="{{ route('refund.request', $order->id) }}" class="btn btn-request-refund {{ !$canInitiateFullRefund ? 'disabled' : '' }}" {{ !$canInitiateFullRefund ? 'aria-disabled="true"' : '' }}>
                                Request Full Refund
                            </a>

                            <button type="button" class="btn btn-transaction-detail" data-bs-toggle="modal" data-bs-target="#transactionDetailModal" data-order-id="{{ $order->id }}">Transaction Detail</button>
                        @else
                            {{-- For other statuses (e.g., Partially Refunded, Refund Pending, Refund Failed, Completed) --}}
                            <button type="button" class="btn btn-transaction-detail" data-bs-toggle="modal" data-bs-target="#transactionDetailModal" data-order-id="{{ $order->id }}">Transaction Detail</button>
                        @endif

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

    <x-review-modal/>
    {{-- Assuming components.transaction-detail-modal exists and is correctly defined --}}
    @include('components.transaction-detail-modal')

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Review Modal Logic
        const reviewModalElement = document.getElementById('reviewModal');
        const reviewModal = new bootstrap.Modal(reviewModalElement);
        const reviewForm = document.getElementById('reviewForm');
        const reviewOrderIdInput = document.getElementById('review_order_id');
        const reviewRatingInput = document.getElementById('review_rating_input');
        const starIcons = reviewModalElement.querySelectorAll('#stars i.fa-star');
        const ratingText = document.getElementById('rating-text');
        const commentInput = document.getElementById('comment');
        const submitReviewButton = reviewForm.querySelector('.btn-submit-review');

        let currentRating = 0;

        reviewModalElement.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const orderId = button.dataset.orderId;
            reviewOrderIdInput.value = orderId;

            currentRating = 0;
            reviewRatingInput.value = 0;
            updateStarsDisplay(0);
            commentInput.value = '';
            ratingText.textContent = 'Click on stars to rate';
            submitReviewButton.disabled = false;
            submitReviewButton.textContent = 'Submit Review';
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

            submitReviewButton.disabled = true;
            submitReviewButton.textContent = 'Submitting...';

            const formData = new FormData(this);
            fetch('{{ route('reviews.store') }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                return response.json().then(data => {
                    if (!response.ok) {
                        throw new Error(data.message || 'Server error occurred during review submission.');
                    }
                    return data;
                });
            })
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    reviewModal.hide();
                    if (data.redirect_url) {
                        window.location.href = data.redirect_url;
                    } else {
                        window.location.reload();
                    }
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Operation failed:', error);
                alert('An error occurred: ' + error.message);
            })
            .finally(() => {
                submitReviewButton.disabled = false;
                submitButton.textContent = 'Submit Review';
            });
        });

        // Transaction Detail Modal Logic
        const transactionDetailModalElement = document.getElementById('transactionDetailModal');

        if (transactionDetailModalElement) {
            transactionDetailModalElement.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const orderId = button.dataset.orderId;

                document.getElementById('modalOrderId').textContent = 'Loading...';
                document.getElementById('modalInvoice').textContent = 'Loading...';
                document.getElementById('modalOrderDate').textContent = 'Loading...';
                document.getElementById('modalOrderStatus').textContent = 'Loading...';
                document.getElementById('modalOrderStatus').className = 'badge';
                document.getElementById('modalShippingNamePhone').textContent = 'Loading...';
                document.getElementById('modalShippingAddress').textContent = 'Loading...';
                document.getElementById('modalShippingCityProvince').textContent = 'Loading...';
                document.getElementById('modalShippingCountryPostal').textContent = 'Loading...';
                document.getElementById('modalOrderItems').innerHTML = '<div class="text-center py-4 text-muted">Loading items...</div>';
                document.getElementById('modalTotalAmount').textContent = 'Loading...';


                fetch(`/orders/${orderId}/modal-details`)
                    .then(response => {
                        if (!response.ok) {
                            return response.json().catch(() => {
                                throw new Error(`HTTP error! Status: ${response.status} - Could not parse error message.`);
                            }).then(errorData => {
                                throw new Error(errorData.message || `HTTP error! Status: ${response.status}`);
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        document.getElementById('modalOrderId').textContent = data.order_id;
                        document.getElementById('modalInvoice').textContent = data.invoice;
                        document.getElementById('modalOrderDate').textContent = data.order_date;

                        const statusBadge = document.getElementById('modalOrderStatus');
                        statusBadge.textContent = data.status;
                        statusBadge.className = 'order-status';
                        statusBadge.classList.add(`status-${data.status.toLowerCase().replace(/\s/g, '-')}`);


                        document.getElementById('modalShippingNamePhone').textContent = `${data.shipping_recipient_name} (${data.shipping_phone})`;
                        document.getElementById('modalShippingAddress').textContent = data.shipping_address;
                        document.getElementById('modalShippingCityProvince').textContent = `${data.shipping_city}, ${data.shipping_province}`;
                        document.getElementById('modalShippingCountryPostal').textContent = `${data.shipping_country} ${data.shipping_postal_code}`;

                        const orderItemsContainer = document.getElementById('modalOrderItems');
                        orderItemsContainer.innerHTML = '';
                        if (data.order_items && data.order_items.length > 0) {
                            data.order_items.forEach(item => {
                                const formattedPrice = new Intl.NumberFormat('id-ID').format(item.price);
                                const formattedSubtotal = new Intl.NumberFormat('id-ID').format(item.quantity * item.price);

                                const itemHtml = `
                                    <div class="list-group-item d-flex align-items-center">
                                        <img src="${item.product_image}" alt="${item.product_name}" class="me-3" style="width: 60px; height: 60px; object-fit: cover; border-radius: 4px;">
                                        <div class="flex-grow-1">
                                            <h5 class="mb-1">${item.product_name}</h5>
                                            ${item.variant_size || item.variant_color ? `<small class="text-muted">Variant: ${item.variant_size ? item.variant_size : ''}${item.variant_size && item.variant_color ? ' / ' : ''}${item.variant_color ? item.variant_color : ''}</small><br>` : ''}
                                            <small class="text-muted">${item.quantity} x IDR ${formattedPrice}</small>
                                        </div>
                                        <div>
                                            <strong>IDR ${formattedSubtotal}</strong>
                                        </div>
                                    </div>
                                `;
                                orderItemsContainer.insertAdjacentHTML('beforeend', itemHtml);
                            });
                        } else {
                            orderItemsContainer.innerHTML = '<div class="text-center py-4 text-muted">No items found for this order.</div>';
                        }

                        document.getElementById('modalTotalAmount').textContent = `IDR ${new Intl.NumberFormat('id-ID').format(data.total_amount)}`;

                    })
                    .catch(error => {
                        console.error('Error fetching transaction details:', error);
                        document.getElementById('modalOrderId').textContent = 'Error loading data.';
                        document.getElementById('modalInvoice').textContent = '';
                        document.getElementById('modalOrderDate').textContent = '';
                        document.getElementById('modalOrderStatus').textContent = 'Error';
                        document.getElementById('modalOrderStatus').className = 'badge badge-danger';
                        document.getElementById('modalShippingNamePhone').textContent = 'Error loading data.';
                        document.getElementById('modalShippingAddress').textContent = '';
                        document.getElementById('modalShippingCityProvince').textContent = '';
                        document.getElementById('modalShippingCountryPostal').textContent = '';
                        document.getElementById('modalOrderItems').innerHTML = `<div class="alert alert-danger text-center">Failed to load transaction details: ${error.message}. Please try again.</div>`;
                        document.getElementById('modalTotalAmount').textContent = 'Error';
                    });
            });
        }
    });
</script>
@endpush