@extends('layouts.app')

@section('title', 'Keranjang')

@push('styles')
    <style>
        :root {
            --font-primary: 'Ancizar Serif', serif;
            --font-secondary: 'Ancizar Serif', serif;
        }
        .btn-gold {
            background-color: #EAE4D5;
            color: black;
            border: 1px solid #000;
        }
        .btn-gold:hover {
            background-color: #B6B09F;
            color: black;
            border: 1px solid #000;
        }

        .img-shoppingcart-title {
            width: 100%;
            margin-bottom: 2rem;
        }

        .product-card {
            border: 1px solid #ddd;
            background-color: #fff;
        }

        .btn-outline-secondary {
            width: 25px;
            height: 25px;
            padding: 0;
        }

        .btn-link {
            font-size: 1.25rem;
        }


        h1.display-5 {
            font-size: 4.5rem;
            font-weight: bold;
        }

        @media (max-width: 992px) {
            .row.flex-lg-row {
                flex-direction: column !important;
            }
        }

        .me-3 {
            margin-right: 1rem !important;
        }

        .cart-header-row {
            display: flex;
            align-items: center;
            margin-bottom: 0.5rem;
            font-weight: bold;
            color: rgb(136, 139, 142);
        }

        .cart-col-image-placeholder {
            width: 80px;
            flex-shrink: 0;
            margin-right: 1rem;
        }
        
        .product-image-container {
            width: 80px;
            flex-shrink: 0;
            margin-right: 1rem;
        }

        .product-image-container img {
            width: 80px;
            height: 80px;
            object-fit: cover;
        }

        .cart-col-price {
            width: 100px;
            text-align: right;
            margin-left: 19.5rem;
            flex-shrink: 0;
        }
        
        .product-unit-price-wrapper {
            width: 100px;
            text-align: right;
            margin-right: 5.5rem;
            flex-shrink: 0;
        }
        
        .cart-col-quantity {
            width: 100px;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-left: 6.9rem;
            flex-shrink: 0;
        }


        .product-quantity-controls-wrapper { 
            width: 100px; /* Overall width for the controls group */
            flex-shrink: 0; 
            display: flex;
            justify-content: center;
            align-items: center;
            margin-right: 2.8rem;
            gap: 0.5rem; /* Space between buttons and input/span */
        }

        .quantity-display-span {
            display: inline-block;
            width: 50px; /* Fixed width for alignment */
            text-align: center;
            padding: 0.375rem 0.25rem;
            cursor: pointer;
            border-bottom: 1px solid transparent; /* Default transparent underline */
            transition: border-color 0.2s;
            line-height: 1.5; /* Ensure text baseline alignment with buttons */
            box-sizing: border-box; /* Include padding and border in width/height */
        }

        .quantity-input-active {
            width: 50px;
            text-align: center;
            padding: 0; /* Remove padding entirely for minimal box */
            border: none; /* No borders */
            background-color: transparent; /* No background */
            box-shadow: none; /* No shadows */
            border-radius: 0; /* No border radius */
            border-bottom: 2px solid #000; /* Only a bottom border (underline) */
            outline: none; /* Remove default outline on focus */
            height: 1.5em; /* Set height based on font-size to avoid extra space */
            font-size: inherit; /* Inherit font size from parent span/text */
            font-family: inherit; /* Inherit font family */
            color: inherit; /* Inherit text color */
            box-sizing: border-box; /* Crucial for sizing */

            /* Remove default appearance for number inputs (spinners etc.) */
            -webkit-appearance: none; /* For Chrome, Safari, Edge */
            -moz-appearance: textfield; /* For Firefox */
            appearance: none; /* Standard */
        }

        .quantity-input-active::-webkit-inner-spin-button,
        .quantity-input-active::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        .quantity-input-active {
            -moz-appearance: textfield; /* Remove Firefox spinner */
        }

        .cart-col-total {
            width: 120px;
            text-align: right;
            margin-left: 3.6rem;
            flex-shrink: 0;
        }
        
        .product-total-price-wrapper {
            width: 120px;
            text-align: right;
            margin-right: 3.5rem;
            flex-shrink: 0;
        }

        
        .trash-icon-container {
            width: 24px;
            flex-shrink: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-right: 1rem;
        }

        .product-card-body {
            display: flex;
            align-items: center;
            padding: 1rem;
        }

        .trash-icon {
            cursor: pointer;
            font-size: 1.2rem;
        }

        .alert-info {
            background-color: #EAE4D5; 
            color: rgb(136, 139, 142);
            border-color: #EAE4D5; 
        }

        .alert-link {
            color: rgb(136, 139, 142);
        }

        .btn-outline-dark:hover {
            background-color: transparent !important; 
            color: #000 !important; 
            border-color: #000 !important;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-5 px-5">
        <x-bangga title="Shopping Cart" subtitle="Bangga Pakai Karya UMKM" />

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

        <div class="row mt-5 ">
            <div class="col-md-8">
                <div class="mb-2 ">
                    <div class="cart-header-row">
                        <div class="cart-col-name">Product name</div>
                        <div class="cart-col-price">Price</div>
                        <div class="cart-col-quantity">Quantity</div>
                        <div class="cart-col-total">Total price</div>
                        <div class="cart-col-trash-placeholder"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row d-flex flex-lg-row">
            <div class="col-lg-8">
                @forelse ($cartItems as $item)
                    <div class="card product-card mb-3">
                        <div class="card-body product-card-body">
                            <div class="product-image-container">
                                <img src="{{ asset('storage/' . $item['product_image']) }}" alt="{{ $item['product_name'] }}" class="img-fluid object-fit-contain" />
                            </div>

                            <div class="flex-grow-1">
                                @php
                                    $productName = $item['product_name'];
                                    $maxLength = 45; 
                                    if (strlen($productName) > $maxLength) {
                                        $productName = substr($productName, 0, $maxLength) . '...';
                                    }
                                @endphp
                                <h6 class="mb-1">{{ $productName }}</h6>
                                @php
                                    $variantSizeDisplay = '';
                                    $variantColorDisplay = '';

                                    if (!empty($item['variant_size']) && $item['variant_size'] !== 'N/A') {
                                        $variantSizeDisplay = $item['variant_size'];
                                    }
                                    if (!empty($item['variant_color']) && $item['variant_color'] !== 'N/A') {
                                        $variantColorDisplay = $item['variant_color'];
                                    }
                                @endphp

                                @if ($variantSizeDisplay || $variantColorDisplay)
                                    <small class="text-muted">
                                        @if ($variantSizeDisplay)
                                            Size: {{ $variantSizeDisplay }}
                                        @endif
                                    </small>
                                @endif
                            </div>

                            <div class="product-unit-price-wrapper">
                                <p class="mb-1">IDR {{ number_format($item['price'], 0, ',', '.') }}</p>
                            </div>

                            <div class="product-quantity-controls-wrapper">
                                <form action="{{ route('cart.update') }}" method="POST" class="d-flex align-items-center">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $item['product_id'] }}">
                                    <input type="hidden" name="product_variant_id" value="{{ $item['product_variant_id'] ?? '' }}">
                                    <input type="hidden" name="quantity" class="hidden-quantity-input" value="{{ $item['quantity'] }}">
                                    
                                    <button type="button" class="btn btn-outline-secondary btn-sm me-2 quantity-minus" data-product-id="{{ $item['product_id'] }}" data-variant-id="{{ $item['product_variant_id'] ?? '' }}" {{ $item['quantity'] <= 1 ? 'disabled' : '' }}>−</button>
                                    
                                    <span class="quantity-display-span" 
                                          data-quantity="{{ $item['quantity'] }}" 
                                          data-min="1" 
                                          data-max="{{ $item['max_stock_available'] }}"
                                          data-product-id="{{ $item['product_id'] }}"
                                          data-variant-id="{{ $item['product_variant_id'] ?? '' }}">
                                        {{ $item['quantity'] }}
                                    </span>
                                    
                                    <button type="button" class="btn btn-outline-secondary btn-sm ms-2 quantity-plus" data-product-id="{{ $item['product_id'] }}" data-variant-id="{{ $item['product_variant_id'] ?? '' }}" {{ $item['quantity'] >= $item['max_stock_available'] ? 'disabled' : '' }}>+</button>
                                </form>
                            </div>

                            <div class="product-total-price-wrapper">
                                <p class="mb-0">IDR {{ number_format($item['total_item_price'], 0, ',', '.') }}</p>
                            </div>

                            <div class="trash-icon-container">
                                <form action="{{ route('cart.remove') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $item['product_id'] }}">
                                    <input type="hidden" name="product_variant_id" value="{{ $item['product_variant_id'] ?? '' }}">
                                    <button type="submit" class="btn btn-link p-0 text-dark trash-icon">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="alert alert-info text-center" role="alert">
                        Keranjang belanja Anda kosong. <a href="{{ route('products.index') }}" class="alert-link">Lanjutkan belanja</a>.
                    </div>
                @endforelse
            </div>

            <div class="col-lg-4">
                <div class="subtotal-box">
                    <div class="d-flex justify-content-between align-items-baseline mb-1">
                        <h5 class="fw-bold mb-0">Subtotal</h5>
                        <h5 class="fw-bold mb-0">IDR {{ number_format($subtotal, 0, ',', '.') }}</h5>
                    </div>
                    <p class="text-muted small mb-1">Sudah termasuk pajak</p>
                    <hr>
                    <a href="{{ route('checkout.show') }}" class="btn btn-gold w-100 mt-2">Checkout</a>
                    <a href="{{ route('products.index') }}" class="btn btn-outline-dark w-100 mt-2">Lanjutkan Belanja</a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const quantityDisplaySpans = document.querySelectorAll('.quantity-display-span');
        const quantityPlusButtons = document.querySelectorAll('.quantity-plus');
        const quantityMinusButtons = document.querySelectorAll('.quantity-minus');

        // Function to update the quantity and submit the form
        function updateAndSubmitQuantity(targetElement, newQuantity) {
            const form = targetElement.closest('form');
            const hiddenQuantityInput = form.querySelector('.hidden-quantity-input');
            const displaySpan = form.querySelector('.quantity-display-span'); // Reference to the actual span

            const minQuantity = parseInt(displaySpan.dataset.min);
            const maxQuantity = parseInt(displaySpan.dataset.max);

            let clampedQuantity = newQuantity;

            // Client-side validation: enforce min and max
            if (isNaN(newQuantity) || newQuantity < minQuantity) {
                clampedQuantity = minQuantity;
                alert('Quantity cannot be less than ' + minQuantity + '.');
            } else if (newQuantity > maxQuantity) {
                clampedQuantity = maxQuantity;
                alert('Only ' + maxQuantity + ' of this item are available in stock.');
            }
            
            // Update the span's text and data attribute for consistent state
            displaySpan.textContent = clampedQuantity;
            displaySpan.dataset.quantity = clampedQuantity;
            
            // Update the hidden input's value for form submission
            hiddenQuantityInput.value = clampedQuantity;

            // Submit the form
            form.submit();
        }

        // Event listeners for span click to enable editing
        quantityDisplaySpans.forEach(span => {
            span.addEventListener('click', function() {
                // If already in edit mode, do nothing
                if (this.classList.contains('active-edit-mode')) {
                    return;
                }
                
                // Add class to mark as active edit mode (for CSS underline)
                this.classList.add('active-edit-mode');

                // Create an input element
                const input = document.createElement('input');
                input.type = 'number';
                input.className = 'quantity-input-active'; // Apply minimalist style
                input.value = this.dataset.quantity; // Get current quantity
                input.min = this.dataset.min; // Get min from data attribute
                input.max = this.dataset.max; // Get max from data attribute
                
                // Set data attributes from span to input
                input.dataset.productId = this.dataset.productId;
                input.dataset.variantId = this.dataset.variantId;

                // Insert the input before the span and hide the span
                this.parentNode.insertBefore(input, this);
                this.style.display = 'none'; // Hide the span

                input.focus();
                input.select();

                // Store a reference to the original span for later replacement
                input.originalSpan = this; // Store original span
                
                // Event listener for when the input loses focus (user clicks outside or tabs out)
                input.addEventListener('blur', function() {
                    const originalSpan = this.originalSpan;
                    const newValue = parseInt(this.value);

                    // Remove active-edit-mode class from original span when input loses focus
                    originalSpan.classList.remove('active-edit-mode');

                    // Validate and update value on blur
                    // Pass the originalSpan as the target element because it holds the data attributes
                    updateAndSubmitQuantity(originalSpan, newValue);

                    // Revert back to span after submission (or immediately if no submission needed)
                    // The page will reload on submit, so immediate replacement is mainly for non-submitting blur.
                    // For a smooth feel without reload, we would update the span's text content here.
                    originalSpan.textContent = newValue; // Update span with new value
                    this.parentNode.replaceChild(originalSpan, this); // Replace input with original span
                });

                // Event listener for 'Enter' key press
                input.addEventListener('keydown', function(event) {
                    if (event.key === 'Enter') {
                        event.preventDefault(); // Prevent form default submission on Enter
                        this.blur(); // Trigger blur to run validation and update
                    }
                });
            });
        });

        // Event listeners for '+' buttons
        quantityPlusButtons.forEach(button => {
            button.addEventListener('click', function() {
                const form = this.closest('form');
                const displaySpan = form.querySelector('.quantity-display-span'); // Always get the span

                let currentQuantity = parseInt(displaySpan.dataset.quantity);
                const maxQuantity = parseInt(displaySpan.dataset.max);

                if (currentQuantity < maxQuantity) {
                    updateAndSubmitQuantity(displaySpan, currentQuantity + 1);
                } else {
                    alert('Only ' + maxQuantity + ' of this item are available in stock.');
                }
            });
        });

        // Event listeners for '-' buttons
        quantityMinusButtons.forEach(button => {
            button.addEventListener('click', function() {
                const form = this.closest('form');
                const displaySpan = form.querySelector('.quantity-display-span'); // Always get the span

                let currentQuantity = parseInt(displaySpan.dataset.quantity);
                const minQuantity = parseInt(displaySpan.dataset.min);

                if (currentQuantity > minQuantity) {
                    updateAndSubmitQuantity(displaySpan, currentQuantity - 1);
                } else {
                    alert('Quantity cannot be less than ' + minQuantity + '.');
                }
            });
        });
    });
</script>
@endpush