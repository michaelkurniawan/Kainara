@extends('layouts.app')

@section('title', 'Keranjang')

@push('styles')
    <style>
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
            margin-left: 20.1rem;
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
            margin-left: 7.6rem;
            flex-shrink: 0;
        }

        .product-quantity-controls-wrapper {
            width: 100px;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-right: 2.8rem;
            flex-shrink: 0;
        }

        .cart-col-total {
            width: 120px;
            text-align: right;
            margin-left: 3rem;
            flex-shrink: 0;
        }
        
        .product-total-price-wrapper {
            width: 120px;
            text-align: right;
            margin-right: 2.8rem;
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
                                    <button type="submit" name="quantity" value="{{ $item['quantity'] - 1 }}" class="btn btn-outline-secondary btn-sm me-2" {{ $item['quantity'] <= 1 ? 'disabled' : '' }}>−</button>
                                    <span class="mx-2">{{ $item['quantity'] }}</span>
                                    <button type="submit" name="quantity" value="{{ $item['quantity'] + 1 }}" class="btn btn-outline-secondary btn-sm ms-2">+</button>
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