@extends('layouts.app')

@section('title', 'Checkout')

@push('styles')
    <style>
        body {
            background-color: #fff;
            font-family: 'AncizarSerif', serif;
        }
        .container-main {
            max-width: 100vw;
            margin-top: 2rem;
            margin-bottom: 2rem;
        }

        .card-no-border {
            border: none !important;
            box-shadow: 0 0px 0px rgba(0, 0, 0, 0);
            height: 100%;
        }

        .card-summary {
            border: 1px solid black;
            border-radius: 1rem;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        .section-title {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 1.5rem;
            color: #333;
        }
        .address-box {
            background-color: #fff;
            border-radius: 1.5rem;
            margin-bottom: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border: 1px solid black;
            padding: 0.5rem;
        }
        .address-box p {
            margin: 0;
            line-height: 1.5;
        }
        .btn-ubah-text-only {
            background-color: transparent;
            border: none;
            padding: 0.5rem 1rem;
            text-decoration: underline;
            color: #333;
            font-size: 0.9rem;
            cursor: pointer;
        }
        .btn-ubah-text-only:hover {
            color: #555;
        }
        .form-label {
            font-weight: 500;
            color: #555;
            padding-left: 1rem;
        }
        .form-control, .form-select {
            border-radius: 1.5rem;
            padding: 0.75rem 1rem;
            border: 1px solid black;
        }
        .form-control::placeholder {
            color: rgba(128, 128, 128, 0.7);
            opacity: 1;
        }
        .form-control:focus, .form-select:focus {
            border-color: #80bdff;
            box-shadow: 0 0 0 0.25rem rgba(0, 123, 255, 0.25);
        }
        .bank-logo {
            height: 70px;
            object-fit: contain;
            border: 1px solid black;
            border-radius: 1.5rem;
            padding: 1rem;
        }
        .bank-logo-container {
            display: flex;
            align-items: center;
            margin-top: 1rem;
            flex-wrap: wrap;
            gap: 1rem;
            justify-content: space-between;
        }
        .order-items-scroll-container {
            flex-grow: 1;
            overflow-y: auto;
            padding-right: 10px;
            margin-bottom: 1rem;
        }
        .order-items-scroll-container::-webkit-scrollbar {
            width: 8px;
        }
        .order-items-scroll-container::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        .order-items-scroll-container::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 10px;
        }
        .order-items-scroll-container::-webkit-scrollbar-thumb:hover {
            background: #555;
            box-shadow: 0 0 0 0.25rem rgba(0, 0, 0, 0.25);
        }
        .order-item {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #eee;
        }
        .order-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        .order-item img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 0.25rem;
            margin-right: 1rem;
            flex-shrink: 0;
        }
        .order-item-details {
            flex-grow: 1;
        }
        .order-item-price {
            font-weight: bold;
            color: #333;
            flex-shrink: 0;
            text-align: right;
        }
        .summary-details {
            position: relative;
            padding-top: 1rem;
        }
        .summary-details::before {
            content: '';
            position: absolute;
            top: 0.5rem;
            left: 0.5rem;
            right: 0.5rem;
            height: 1px;
            background-color: black;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
            padding-left: 0.5rem;
            padding-right: 0.5rem;
        }
        .summary-total {
            font-size: 1.5rem;
            font-weight: bold;
            color: #333;
            margin-top: 1rem;
            border-top: none;
            padding-top: 1rem;
            position: relative;
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
            padding-left: 0.5rem;
            padding-right: 0.5rem;

        }
        .summary-total::before {
            content: '';
            position: absolute;
            top: 0.5rem;
            left: 0.5rem;
            right: 0.5rem;
            height: 1px;
            background-color: black;
        }
        .btn-checkout {
            width: 100%;
            padding: 1rem;
            background-color: #B6B09F;
            color: #fff;
            border: none;
            border-radius: 0.375rem;
            font-size: 1.125rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-bottom: 0 !important;
        }
        .btn-checkout:hover {
            background-color: #9a9a9a;
        }

        @media (max-width: 991.98px) {
            .card-summary {
                max-width: 100%;
                margin-right: 0;
                margin-top: 2rem;
            }
        }
    </style>
@endpush

@section('content')
    @php
        $userAddresses = [
            [
                'id' => 1,
                'type' => 'Home',
                'name' => 'Michael Kurniawan',
                'phone' => '085175059853',
                'street' => 'Jl. Pakuan No.3, Sumur Batu',
                'sub_district' => 'Babakan Madang',
                'district' => 'Kabupaten Bogor',
                'city' => '',
                'province' => 'Jawa Barat',
                'postal_code' => '16810',
                'is_primary' => true,
            ],
            [
                'id' => 2,
                'type' => 'Work',
                'name' => 'Michael Kurniawan',
                'phone' => '085175059853',
                'street' => 'Sentul City, Jl. Pakuan No.3, Sumur Batu',
                'sub_district' => 'Babakan Madang',
                'district' => 'Bogor Regency',
                'city' => '',
                'province' => 'West Java',
                'postal_code' => '16810',
                'is_primary' => false,
            ],
        ];

        $defaultAddress = collect($userAddresses)->firstWhere('is_primary');
        if (!$defaultAddress && count($userAddresses) > 0) {
            $defaultAddress = $userAddresses[0];
        } elseif (!$defaultAddress) {
            $defaultAddress = null; 
        }
        $address = $defaultAddress;
        $selectedAddressId = $defaultAddress['id'] ?? null;
    @endphp

    <div class="container-fluid py-5 px-5">
        <div class="row g-4 h-100 align-items-stretch">
            <div class="col-lg-8">
                <div class="card card-no-border h-100 me-5">
                    <div class="mb-4">
                        <h2 class="section-title d-flex align-items-center">
                            <i class="bi bi-geo-alt-fill text-danger me-2"></i> Address
                        </h2>
                        <div class="address-box">
                            <div class="d-flex w-100 justify-content-between align-items-start">
                                <div class="d-flex flex-column justify-content-center align-items-center" style="min-width: 80px;">
                                    <p class="fw-semibold fs-5 m-3" id="currentAddressType">{{ $address['type'] ?? '' }}</p>
                                </div>
                                <div class="vr mx-3"></div>
                                <div class="text-start flex-grow-1" id="currentAddressDetails">
                                    <p class="text-muted mb-0" data-address-line="street">{{ $address['street'] ?? '' }}{{ $address['sub_district'] ? ', ' . $address['sub_district'] : '' }}</p>
                                    <p class="text-muted mb-0" data-address-line="district-city">{{ $address['district'] ?? '' }}{{ $address['city'] ? ', ' . $address['city'] : '' }}</p>
                                    <p class="text-muted mb-0" data-address-line="province-postal">{{ $address['province'] ?? '' }} {{ $address['postal_code'] ?? '' }}</p>
                                </div>
                            </div>
                            <button type="button" class="btn-ubah-text-only" data-bs-toggle="modal" data-bs-target="#addressSelectionModal">Ubah</button>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h2 class="section-title d-flex align-items-center">
                            <i class="bi bi-person-fill text-primary me-2"></i> Contact Information
                        </h2>
                        <form action="{{ route('checkout.add') }}" method="POST">
                            @csrf
                            <div class="row g-3 mb-5">
                                <div class="col-md-6">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" placeholder="email@example.com" value="{{ old('email') }}">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="phone" class="form-label">Phone</label>
                                    <input type="tel" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" placeholder="e.g., 081234567890" value="{{ old('phone') }}">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="first_name" class="form-label">First Name</label>
                                    <input type="text" class="form-control @error('first_name') is-invalid @enderror" id="first_name" name="first_name" placeholder="John" value="{{ old('first_name') }}">
                                    @error('first_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="last_name" class="form-label">Last Name</label>
                                    <input type="text" class="form-control @error('last_name') is-invalid @enderror" id="last_name" name="last_name" placeholder="Doe" value="{{ old('last_name') }}">
                                    @error('last_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            


                            <div class="mt-5">
                                <h2 class="section-title d-flex align-items-center">
                                    <i class="bi bi-credit-card-fill text-success me-2"></i> Payment Method
                                </h2>
                                <div class="mb-3">
                                    <select class="form-select @error('payment_method') is-invalid @enderror" id="paymentMethod" name="payment_method">
                                        <option value="transfer_bank" {{ old('payment_method') == 'transfer_bank' ? 'selected' : '' }}>Transfer Bank</option>
                                        <option value="credit_card" {{ old('payment_method') == 'credit_card' ? 'selected' : '' }}>Credit Card</option>
                                        <option value="e_wallet" {{ old('payment_method') == 'e_wallet' ? 'selected' : '' }}>E-Wallet</option>
                                    </select>
                                    @error('payment_method')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <input type="hidden" name="total_amount" value="{{ $subtotal ?? 0 }}">
                            @foreach ($cartItems as $index => $item)
                                <input type="hidden" name="cart_items[{{ $index }}][product_id]" value="{{ $item['product_id'] }}">
                                <input type="hidden" name="cart_items[{{ $index }}][product_variant_id]" value="{{ $item['product_variant_id'] ?? '' }}">
                                <input type="hidden" name="cart_items[{{ $index }}][quantity]" value="{{ $item['quantity'] }}">
                                <input type="hidden" name="cart_items[{{ $index }}][price]" value="{{ $item['price'] }}">
                                <input type="hidden" name="cart_items[{{ $index }}][product_name]" value="{{ $item['product_name'] }}">
                                <input type="hidden" name="cart_items[{{ $index }}][product_image]" value="{{ $item['product_image'] }}">
                                <input type="hidden" name="cart_items[{{ $index }}][variant_size]" value="{{ $item['variant_size'] ?? '' }}">
                            @endforeach

                            <button type="submit" class="btn btn-checkout mt-3">CHECK OUT</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card-summary p-4 h-100">
                    <h2 class="section-title">Order Summary</h2>

                    <div class="order-items-scroll-container">
                        <div class="order-items">
                            @forelse ($cartItems as $item)
                            <div class="order-item">
                                <img src="{{ asset($item['product_image']) }}" alt="{{ $item['product_name'] }}" class="img-fluid">
                                <div class="order-item-details">
                                    <p class="fw-semibold mb-0 fs-6">{{ $item['product_name'] }}</p>
                                    <p class="text-muted mb-0">IDR {{ number_format($item['price'], 0, ',', '.') }}</p>
                                    @if ($item['variant_size'])
                                        <p class="text-muted mb-0">Size: {{ $item['variant_size'] }}</p>
                                    @endif
                                    <p class="text-muted mb-0">x{{ $item['quantity'] }}</p>
                                </div>
                                <p class="order-item-price fs-6">IDR {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</p>
                            </div>
                            @empty
                            <p class="text-muted text-center">Your cart is empty.</p>
                            @endforelse
                        </div>
                    </div>

                    <div class="summary-details">
                        <div class="summary-row">
                            <span>SUBTOTAL</span>
                            <span class="fw-semibold">IDR {{ number_format($subtotal ?? 0, 0, ',', '.') }}</span>
                        </div>
                        <div class="summary-row">
                            <span>SHIPPING</span>
                            @php $shippingCost = 0; @endphp
                            <span class="fw-semibold">{{ $shippingCost == 0 ? 'FREE' : 'IDR ' . number_format($shippingCost, 0, ',', '.') }}</span>
                        </div>
                        <div class="summary-total">
                            <span>TOTAL</span>
                            <span>IDR {{ number_format(($subtotal ?? 0) + $shippingCost, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Call the new Blade Component --}}
    @include('components.popupalamat', [
        'userAddresses' => $userAddresses, 
        'selectedAddressId' => $selectedAddressId,
    ])
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const formAddressType = document.getElementById('address_type_input');
        const formStreet = document.getElementById('street_input');
        const formSubDistrict = document.getElementById('sub_district_input');
        const formDistrict = document.getElementById('district_input');
        const formCity = document.getElementById('city_input');
        const formProvince = document.getElementById('province_input');
        const formPostalCode = document.getElementById('postal_code_input');
        const userNameInput = document.getElementById('user_name_input');
        const userPhoneInput = document.getElementById('user_phone_input');
        
        // userAddressesData is now populated from the @json($userAddresses) in the parent Blade file
        const userAddressesData = @json($userAddresses);


        // Listen for the custom event dispatched by the address selection modal component
        window.addEventListener('addressSelected', function(event) {
            const selectedAddressData = event.detail.addressData;
            // No need to use addressId here, as we have selectedAddressData directly.
            
            // Update displayed address details on the main checkout page
            document.getElementById('currentAddressType').textContent = selectedAddressData.type || '';
            const addressDetailsContainer = document.getElementById('currentAddressDetails');
            addressDetailsContainer.innerHTML = `
                <p class="text-muted mb-0" data-address-line="street">${selectedAddressData.street || ''}${selectedAddressData.sub_district ? ', ' + selectedAddressData.sub_district : ''}</p>
                <p class="text-muted mb-0" data-address-line="district-city">${selectedAddressData.district || ''}${selectedAddressData.city ? ', ' + selectedAddressData.city : ''}</p>
                <p class="text-muted mb-0" data-address-line="province-postal">${selectedAddressData.province || ''} ${selectedAddressData.postal_code || ''}</p>
            `;

            // Fill in the actual form input fields
            if (formAddressType) formAddressType.value = selectedAddressData.type || '';
            if (formStreet) formStreet.value = selectedAddressData.street || '';
            if (formSubDistrict) formSubDistrict.value = selectedAddressData.sub_district || '';
            if (formDistrict) formDistrict.value = selectedAddressData.district || '';
            if (formCity) formCity.value = selectedAddressData.city || '';
            if (formProvince) formProvince.value = selectedAddressData.province || '';
            if (formPostalCode) formPostalCode.value = selectedAddressData.postal_code || '';
            if (userNameInput) userNameInput.value = selectedAddressData.name || '';
            if (userPhoneInput) userPhoneInput.value = selectedAddressData.phone || '';
        });

        // Initial fill of the main form fields when the page loads, using the defaultAddress from PHP
        // This ensures the form fields are populated from the start.
        const currentSelectedAddressId = {{ Js::from($selectedAddressId) }}; // Get the initial selected ID from PHP
        if (userAddressesData.length > 0) {
            const defaultAddressFromPHP = userAddressesData.find(addr => addr.id === currentSelectedAddressId);
            if (defaultAddressFromPHP) {
                if (formAddressType) formAddressType.value = defaultAddressFromPHP.type || '';
                if (formStreet) formStreet.value = defaultAddressFromPHP.street || '';
                if (formSubDistrict) formSubDistrict.value = defaultAddressFromPHP.sub_district || '';
                if (formDistrict) formDistrict.value = defaultAddressFromPHP.district || '';
                if (formCity) formCity.value = defaultAddressFromPHP.city || '';
                if (formProvince) formProvince.value = defaultAddressFromPHP.province || '';
                if (formPostalCode) formPostalCode.value = defaultAddressFromPHP.postal_code || '';
                if (userNameInput) userNameInput.value = defaultAddressFromPHP.name || '';
                if (userPhoneInput) userPhoneInput.value = defaultAddressFromPHP.phone || '';
            }
        }
    });
</script>
@endpush