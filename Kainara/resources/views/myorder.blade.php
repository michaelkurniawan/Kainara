<!-- resources/views/myorder.blade.php -->
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
        background-color: transparent;
        border: 1px solid #777;
        color: #777;
    }
    .btn-transaction-detail:hover {
        background-color: #eee;
        color: #555;
    }
    .btn-track {
        background-color: #B6B09F;
        color: white;
        border: none;
    }
    .btn-track:hover {
        background-color: #9a9a9a;
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
                        <a href="{{ route('order.details', $order->id) }}" class="btn btn-transaction-detail">Transaction Detail</a>
                        @if (in_array($order->status, ['Awaiting Shipment', 'Shipped', 'In Delivery']))
                            <button class="btn btn-track">Track</button>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-center p-5">
                    <p class="lead">You don't have any orders yet.</p>
                    <p>Start shopping now and support local UMKM!</p>
                    <a href="{{ route('products.index') }}" class="btn btn-primary mt-3" style="background-color: #B6B09F; border-color: #B6B09F;">Start Shopping</a>
                </div>
            @endforelse
        </div>
    </div>
@endsection