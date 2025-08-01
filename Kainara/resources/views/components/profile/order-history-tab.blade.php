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
    .status-refund-pending { background-color: #ffeeba; color: #856404; } /* Yellow-ish for pending refund */
    .status-refund-failed { background-color: #f8d7da; color: #721c24; } /* Red for failed refund */
    .status-rejected { background-color: #6c757d; color: #fff; } /* Grey for rejected refund */
    .status-approved { background-color: #007bff; color: #fff; } /* Blue for approved refund */


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

                // LOGIC DIBERIKAN PERBAIKAN DI SINI
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
                else {
                    $displayStatus = $order->status;
                    $statusClass = Str::slug($order->status);
                }
            @endphp

            <div class="order-card-compact"
                data-bs-toggle="modal"
                data-bs-target="#transactionDetailModal"
                style="cursor: pointer;"
                data-order-id="{{ $order->id }}" {{-- Pass order ID for JavaScript to fetch details --}}
                >

                <img src="{{ $productImage }}" alt="Product Image" class="product-image-thumbnail">

                <div class="order-card-content">
                    <div class="order-id-line">
                        Order ID : INV/{{ \Carbon\Carbon::parse($order->created_at)->format('Ymd') }}/{{ $order->id }}
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

                <span class="order-status-compact status-{{ $statusClass }}">
                    {{ $displayStatus }}
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

{{-- This part should be outside the tab-pane, usually at the end of profile.blade.php --}}
{{-- @include('components.transaction-detail-modal') --}}
{{-- Make sure your transaction-detail-modal is setup to receive order ID and fetch data via AJAX --}}

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const transactionDetailModalElement = document.getElementById('transactionDetailModal');

        if (transactionDetailModalElement) {
            transactionDetailModalElement.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget; // Button that triggered the modal
                const orderId = button.dataset.orderId; // Extract info from data-order-id attribute

                // Reset modal content to loading state
                document.getElementById('modalOrderId').textContent = 'Loading...';
                document.getElementById('modalInvoice').textContent = 'Loading...';
                document.getElementById('modalOrderDate').textContent = 'Loading...';
                document.getElementById('modalOrderStatus').textContent = 'Loading...';
                document.getElementById('modalOrderStatus').className = 'badge'; // Reset class
                document.getElementById('modalShippingNamePhone').innerHTML = 'Loading...'; // Use innerHTML for strong tag
                document.getElementById('modalShippingAddress').textContent = 'Loading...';
                document.getElementById('modalShippingCityProvince').textContent = 'Loading...';
                document.getElementById('modalShippingCountryPostal').textContent = 'Loading...';
                document.getElementById('modalOrderItems').innerHTML = '<div class="text-center py-4 text-muted">Loading items...</div>';
                document.getElementById('modalTotalAmount').textContent = 'Loading...';

                const paymentDetailsContainer = document.getElementById('modalPaymentDetails');
                if (paymentDetailsContainer) {
                    paymentDetailsContainer.innerHTML = '<div class="text-center py-4 text-muted">Loading payment details...</div>';
                }

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
                        const displayedStatus = (data.payment_details && data.payment_details.status) ? String(data.payment_details.status) : String(data.status);
                        statusBadge.textContent = displayedStatus;
                        statusBadge.className = 'badge';
                        statusBadge.classList.add(`status-${displayedStatus.toLowerCase().replace(/\s/g, '-')}`);


                        document.getElementById('modalShippingNamePhone').innerHTML = `<strong>${data.shipping_recipient_name}</strong> | ${data.shipping_phone} `;
                        document.getElementById('modalShippingAddress').textContent = data.shipping_address;
                        document.getElementById('modalShippingCityProvince').textContent = `${data.shipping_city}, ${data.shipping_province}`;
                        document.getElementById('modalShippingCountryPostal').textContent = `${data.shipping_country} ${data.shipping_postal_code}`;

                        const orderItemsContainer = document.getElementById('modalOrderItems');
                        orderItemsContainer.innerHTML = '';
                        if (data.order_items && data.order_items.length > 0) {
                            data.order_items.forEach(item => {
                                const formattedPrice = new Intl.NumberFormat('id-ID').format(item.price);
                                const itemHtml = `
                                    <div class="list-group-item d-flex align-items-center">
                                        <img src="${item.product_image}" alt="${item.product_name}" class="me-3" style="width: 60px; height: 60px; object-fit: cover; border-radius: 4px;">
                                        <div class="flex-grow-1">
                                            <h5 class="mb-1">${item.product_name}</h5>
                                            ${item.variant_size || item.variant_color ? `<small class="text-muted">Variant: ${item.variant_size ? item.variant_size : ''}${item.variant_size && item.variant_color ? ' / ' : ''}${item.variant_color ? item.variant_color : ''}</small><br>` : ''}
                                            <small class="text-muted">${item.quantity} x IDR ${formattedPrice}</small>
                                        </div>
                                        <div>
                                            <strong>IDR ${new Intl.NumberFormat('id-ID').format(item.quantity * item.price)}</strong>
                                        </div>
                                    </div>
                                `;
                                orderItemsContainer.insertAdjacentHTML('beforeend', itemHtml);
                            });
                        } else {
                            orderItemsContainer.innerHTML = '<div class="text-center py-4 text-muted">No items found for this order.</div>';
                        }

                        document.getElementById('modalTotalAmount').textContent = `IDR ${new Intl.NumberFormat('id-ID').format(data.total_amount)}`;

                        // Display payment and refund details if available
                        if (paymentDetailsContainer) {
                            if (data.payment_details) {
                                let paymentHtml = `
                                    <h5 class="mt-4">Payment Details</h5>
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item"><strong>Payment Status:</strong> <span class="badge status-${String(data.payment_details.status).toLowerCase().replace(/\s/g, '-')}" style="text-transform: capitalize;">${data.payment_details.status}</span></li>
                                        <li class="list-group-item"><strong>Amount Paid:</strong> IDR ${new Intl.NumberFormat('id-ID').format(data.payment_details.amount_paid)}</li>
                                        <li class="list-group-item"><strong>Payment Method:</strong> ${data.payment_details.payment_method_type}</li>
                                `;
                                if (data.payment_details.card_details) {
                                    paymentHtml += `
                                        <li class="list-group-item"><strong>Card Details:</strong> ${data.payment_details.card_details.brand} **** ${data.payment_details.card_details.last4} (Exp: ${data.payment_details.card_details.exp_month}/${data.payment_details.card_details.exp_year})</li>
                                    `;
                                }
                                paymentHtml += `</ul>`;

                                // Add Refund Details section
                                if (data.payment_details.refunds && data.payment_details.refunds.length > 0) {
                                    paymentHtml += `<h6 class="mt-3">Refunds:</h6><ul class="list-group list-group-flush">`;
                                    data.payment_details.refunds.forEach(refund => {
                                        paymentHtml += `
                                            <li class="list-group-item">
                                                <strong>Refund ID:</strong> ${refund.stripe_refund_id || 'N/A'} <br>
                                                <strong>Amount:</strong> IDR ${new Intl.NumberFormat('id-ID').format(refund.refunded_amount)} <br>
                                                <strong>Status:</strong> <span class="badge status-${String(refund.status).toLowerCase().replace(/\s/g, '-')}" style="text-transform: capitalize;">${refund.status}</span> <br>
                                                <strong>Reason:</strong> ${refund.reason || 'No reason provided'} <br>
                                                ${refund.refund_image ? `<strong>Proof Image:</strong> <a href="${'{{ Storage::url("") }}'.replace('""', '')}${String(refund.refund_image).replace('public/', '')}" target="_blank">View Image</a><br>` : ''}
                                                <strong>Refund Date:</strong> ${refund.refunded_at || 'N/A'} <br>
                                                <strong>Admin Notes:</strong> ${refund.admin_notes || 'N/A'}
                                            </li>
                                        `;
                                    });
                                    paymentHtml += `</ul>`;
                                }
                                paymentDetailsContainer.innerHTML = paymentHtml;
                            } else {
                                paymentDetailsContainer.innerHTML = '<p class="text-muted mt-4">No payment details available for this order.</p>';
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching transaction details:', error);
                        document.getElementById('modalOrderId').textContent = 'Error loading data.';
                        document.getElementById('modalInvoice').textContent = '';
                        document.getElementById('modalOrderDate').textContent = '';
                        document.getElementById('modalOrderStatus').textContent = 'Error';
                        document.getElementById('modalOrderStatus').className = 'badge badge-danger';
                        document.getElementById('modalShippingNamePhone').innerHTML = 'Error loading data.';
                        document.getElementById('modalShippingAddress').textContent = '';
                        document.getElementById('modalShippingCityProvince').textContent = '';
                        document.getElementById('modalShippingCountryPostal').textContent = '';
                        document.getElementById('modalOrderItems').innerHTML = `<div class="alert alert-danger text-center">Failed to load transaction details: ${error.message}. Please try again.</div>`;
                        document.getElementById('modalTotalAmount').textContent = 'Error';
                        if (paymentDetailsContainer) {
                            paymentDetailsContainer.innerHTML = `<div class="alert alert-danger text-center">Failed to load payment details: ${error.message}.</div>`;
                        }
                    });
            });
        }
    });
</script>
@endpush