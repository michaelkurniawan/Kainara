@push('styles')
<style>
    /* New styles for the compact order history card based on the image */
    .order-card-compact {
        background-color: #fff;
        border: 1px solid #ddd;
        padding: 15px 20px; /* Reduced padding */
        margin-bottom: 15px; /* Reduced margin */
        box-shadow: 0 2px 5px rgba(0,0,0,0.05); /* Lighter shadow */
        display: flex; /* Menggunakan flexbox untuk tata letak horizontal */
        align-items: center; /* Align items vertically in the center */
        justify-content: space-between; /* Space out content and status */
        min-height: 100px; /* Ensure a minimum height for visual consistency */
        position: relative; /* Needed for absolute positioning of M badge if used */
        overflow: hidden; /* Prevent content overflow */
        gap: 15px; /* Spasi antar item flex (gambar, konten, status) */
    }

    /* Gaya untuk gambar produk pertama */
    .order-card-compact .product-image-thumbnail {
        width: 80px; /* Ukuran gambar produk */
        height: 80px;
        object-fit: cover;
        border-radius: 4px; /* Sedikit rounded corner */
        flex-shrink: 0; /* Pastikan gambar tidak mengecil */
    }

    .order-card-content {
        flex-grow: 1; /* Allows content to take available space */
        display: flex;
        flex-direction: column;
        text-align: left; /* Ensure text inside is left-aligned */
    }

    .order-card-content .order-id-line {
        font-size: 1.1rem;
        font-weight: bold;
        color: #333;
        margin-bottom: 5px; /* Space below ID line */
    }

    .order-card-content .order-summary-line {
        font-size: 0.9rem;
        color: #666;
        margin-bottom: 3px;
    }

    .order-card-content .order-address-line {
        font-size: 0.85rem;
        color: #888;
        line-height: 1.4;
    }

    /* Status badge for compact card */
    .order-status-compact {
        padding: 8px 12px;
        border-radius: 5px;
        font-weight: bold;
        font-size: 0.85rem;
        text-transform: capitalize;
        white-space: nowrap; /* Prevent text from wrapping */
        flex-shrink: 0; /* Prevent shrinking */
        margin-left: 15px; /* Space from content */
    }

    /* Status colors (re-using existing ones) */
    .status-completed { background-color: #d4edda; color: #155724; } /* Green */
    .status-canceled { background-color: #f8d7da; color: #721c24; } /* Red */
    .status-returned { background-color: #fff3cd; color: #856404; } /* Yellow */
    .status-refunded { background-color: #d1ecf1; color: #0c5460; } /* Light Blue */

    /* Generic empty state card styling (if it exists outside the order card) */
    .empty-order-card {
        background-color: #fff;
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 40px 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        text-align: left; /* Ensure text is left-aligned */
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

    /* Responsive adjustments for compact card */
    @media (max-width: 768px) {
        .order-card-compact {
            flex-direction: column;
            align-items: flex-start;
            padding: 15px;
        }
        .order-card-compact .product-image-thumbnail {
            margin-bottom: 10px; /* Spasi bawah gambar pada mobile */
        }
        .order-card-content {
            width: 100%;
            margin-bottom: 10px; /* Space between content and status on small screens */
        }
        .order-status-compact {
            margin-left: 0; /* Remove side margin */
            width: 100%; /* Full width badge on small screens */
            text-align: center; /* Center status text */
        }
    }
</style>
@endpush

<div class="tab-pane fade" id="order-history" role="tabpanel" aria-labelledby="order-history-tab">
    <div class="addresses-header d-flex justify-content-between align-items-center mb-4">
        <h3 class="personal-info-title mb-0 font-serif-medium">Order History</h3>
    </div>
    <hr class="mb-4">

    {{-- Container for all order cards, adjusted margin top --}}
    <div class="card-order-container mt-2">
        {{-- Loop through $userOrdersHistory --}}
        @forelse ($userOrdersHistory as $order)
            @php
                $firstItem = $order->orderItems->first();
                $productImage = $firstItem ? asset('storage/' . ($firstItem->product->image ?? 'images/default-product.png')) : 'https://placehold.co/80x80/cccccc/333333?text=No+Image';

                // Construct full address safely
                $fullAddressParts = [];
                // Add the main address line (street, sub-district, district)
                if (!empty($order->shipping_address)) {
                    $fullAddressParts[] = $order->shipping_address;
                }
                // Add city, province, country, and postal code
                if (!empty($order->shipping_city)) {
                    $fullAddressParts[] = $order->shipping_city;
                }
                if (!empty($order->shipping_province)) {
                    $fullAddressParts[] = $order->shipping_province;
                }
                if (!empty($order->shipping_country)) {
                    $fullAddressParts[] = $order->shipping_country;
                }
                if (!empty($order->shipping_postal_code)) {
                    $fullAddressParts[] = $order->shipping_postal_code;
                }
                $fullAddress = implode(', ', array_filter($fullAddressParts));
            @endphp

            <div class="order-card-compact"
                data-bs-toggle="modal"
                data-bs-target="#transactionDetailModal"
                style="cursor: pointer;" {{-- Add cursor pointer to indicate clickability --}}
                data-order="{{ json_encode([
                    'id' => $order->id,
                    'invoice' => 'INV/' . \Carbon\Carbon::parse($order->created_at)->format('Ymd') . '/' . $order->id,
                    'order_date' => \Carbon\Carbon::parse($order->created_at)->format('d F Y'),
                    'status' => $order->status,
                    'total_amount' => 'IDR ' . number_format($order->grand_total, 0, ',', '.'), // Format for display
                    'subtotal' => 'IDR ' . number_format($order->subtotal, 0, ',', '.'),
                    'shipping_cost' => 'IDR ' . number_format($order->shipping_cost, 0, ',', '.'),
                    'shipping_address' => [
                        'recipient_name' => $order->shipping_recipient_name ?? 'N/A',
                        'phone' => $order->shipping_phone ?? 'N/A',
                        'address' => $order->shipping_address ?? 'N/A', // This should already contain the street/sub-district/district
                        'city' => $order->shipping_city ?? 'N/A',
                        'province' => $order->shipping_province ?? 'N/A',
                        'country' => $order->shipping_country ?? 'N/A',
                        'postal_code' => $order->shipping_postal_code ?? 'N/A',
                    ],
                    'items' => $order->orderItems->map(function($item) {
                        return [
                            'product_name' => $item->product->name ?? 'Unknown Product',
                            'quantity' => $item->quantity,
                            'price' => 'IDR ' . number_format($item->price, 0, ',', '.'), // Format for display
                            'image' => asset('storage/' . ($item->product->image ?? 'images/default-product.png')),
                            'variant_size' => $item->variant_size, // Include variants if available
                            'variant_color' => $item->variant_color, // Include variants if available
                        ];
                    })->toArray(),
                ]) }}">

                <img src="{{ $productImage }}" alt="Product Image" class="product-image-thumbnail">

                <div class="order-card-content">
                    <div class="order-id-line">
                        Order ID : {{ \Carbon\Carbon::parse($order->created_at)->format('Ymd') }}/{{ $order->id }}
                    </div>
                    <div class="order-summary-line">
                        {{ $order->orderItems->count() }} items
                    </div>
                    <div class="order-summary-line">
                        IDR {{ number_format($order->grand_total, 0, ',', '.') }}
                    </div>
                    <div class="order-address-line">
                        {{-- Display the constructed full address here --}}
                        {{ $fullAddress }}
                    </div>
                </div>

                <span class="order-status-compact status-{{ Str::slug($order->status) }}">
                    {{ $order->status }}
                </span>
            </div>
        @empty
            <div class="empty-order-card">
                <p class="lead">You don't have any past orders (completed, canceled, returned, or refunded) yet.</p>
                <p>Once you complete an order, it will appear here!</p>
                <a href="{{ route('products.index') }}" class="btn btn-primary mt-3" style="background-color: #B6B09F; border-color: #B6B09F;">Start Shopping</a>
            </div>
        @endforelse
    </div>
</div>