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
            display: flex; /* Make it a flex container */
            flex-direction: column; /* Stack children vertically */
            
            position: sticky;
            top: 2rem;
            align-self: flex-start;
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
        .order-items-scroll-container {
            flex-grow: 1; /* Allows this div to take up available space, pushing subsequent items down */
            overflow-y: auto;
            padding-right: 10px;
            margin-bottom: 1rem;
            max-height: 455px;
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
            border-bottom: 2px solid #eee;
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
        .summary-total {
            font-size: 1.5rem;
            font-weight: bold;
            color: #333;
            margin-top: auto; /* Pushes this element to the very bottom of the flex container */
            border-top: 1px solid black;
            padding-top: 1rem;
            position: relative;
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem; /* Reduced to pull it closer to the bottom */
            padding-left: 0.5rem;
            padding-right: 0.5rem;
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

        .fixed-payment-method {
            border: 1px solid black;
            border-radius: 1.5rem;
            padding: 0.75rem 1rem;
            background-color: #FFF;
            color: #495057;
            display: block;
            line-height: 1.5;
        }

        @media (max-width: 991.98px) {
            .card-summary {
                max-width: 100%;
                margin-right: 0;
                margin-top: 2rem;
                position: static;
            }
        }
    </style>
@endpush

@section('content')
    @php
        $userAddresses = [
            ['id' => 1, 'type' => 'Home', 'name' => 'Michael Kurniawan', 'phone' => '085175059853', 'street' => 'Jl. Pakuan No.3, Sumur Batu', 'sub_district' => 'Babakan Madang', 'district' => 'Kabupaten Bogor', 'city' => '', 'province' => 'Jawa Barat', 'postal_code' => '16810', 'is_primary' => true],
            ['id' => 2, 'type' => 'Work', 'name' => 'Michael Kurniawan', 'phone' => '085175059853', 'street' => 'Sentul City, Jl. Pakuan No.3, Sumur Batu', 'sub_district' => 'Babakan Madang', 'district' => 'Bogor Regency', 'city' => '', 'province' => 'West Java', 'postal_code' => '16810', 'is_primary' => false],
        ];

        $defaultAddress = collect($userAddresses)->firstWhere('is_primary');
        if (!$defaultAddress && count($userAddresses) > 0) {
            $defaultAddress = $userAddresses[0];
        } elseif (!$defaultAddress) {
            $defaultAddress = null;
        }
        $address = $defaultAddress;
        $selectedAddressId = $defaultAddress['id'] ?? null;

        $subtotal = $subtotal ?? 0;
        $shippingCost = 0;
        $grandTotal = $subtotal + $shippingCost;
    @endphp

    <div class="container-fluid py-5 px-5">
        <div class="row g-4 h-100 align-items-stretch">
            <div class="col-lg-8">
                <div class="card card-no-border h-100 me-5">
                    <div class="mb-4">
                        <h2 class="section-title d-flex align-items-center">
                            <i class="bi bi-geo-alt-fill text-danger me-2"></i> Shipping Address
                        </h2>
                        <div class="address-box">
                            <div class="d-flex w-100 justify-content-between align-items-start">
                                <div class="d-flex flex-column justify-content-center align-items-center" style="min-width: 80px;">
                                    <p class="fw-semibold fs-5 m-3" id="currentAddressType">{{ $address['type'] ?? '' }}</p>
                                </div>
                                <div class="vr mx-3"></div>
                                <div class="text-start flex-grow-1" id="currentAddressDetails">
                                    <p class="text-muted mb-0" data-address-line="street">{{ $address['street'] ?? '' }}{{ $address['sub_district'] ? ', ' . $address['sub_district'] : '' }}</p>
                                    <p class="text-muted mb-0" data-address-line="district-city">{{ $address['district'] ?? '' }}{{ $address['city'] ? ', ' + $address['city'] : '' }}</p>
                                    <p class="text-muted mb-0" data-address-line="province-postal">{{ $address['province'] ?? '' }} {{ $address['postal_code'] ?? '' }}</p>
                                </div>
                            </div>
                            <button type="button" class="btn-ubah-text-only" data-bs-toggle="modal" data-bs-target="#addressSelectionModal">Change</button>
                        </div>
                    </div>

                    <div class="detail">
                        <h2 class="section-title d-flex align-items-center">
                            <i class="bi bi-person-fill text-primary me-2"></i> Contact Information
                        </h2>
                        <form action="{{ route('order.process') }}" method="POST">
                            @csrf
                            <input type="hidden" name="address_type_input" id="address_type_input" value="{{ $address['type'] ?? '' }}">
                            <input type="hidden" name="street_input" id="street_input" value="{{ $address['street'] ?? '' }}">
                            <input type="hidden" name="sub_district_input" id="sub_district_input" value="{{ $address['sub_district'] ?? '' }}">
                            <input type="hidden" name="district_input" id="district_input" value="{{ $address['district'] ?? '' }}">
                            <input type="hidden" name="city_input" id="city_input" value="{{ $address['city'] ?? '' }}">
                            <input type="hidden" name="province_input" id="province_input" value="{{ $address['province'] ?? '' }}">
                            <input type="hidden" name="postal_code_input" id="postal_code_input" value="{{ $address['postal_code'] ?? '' }}">
                            <input type="hidden" name="user_name_input" id="user_name_input" value="{{ $address['name'] ?? '' }}">
                            <input type="hidden" name="user_phone_input" id="user_phone_input" value="{{ $address['phone'] ?? '' }}">

                            <div class="row g-3 mb-5">
                                <div class="col-md-6">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" placeholder="email@example.com" value="{{ old('email', Auth::user()->email ?? '') }}">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="phone" class="form-label">Phone</label>
                                    <input type="tel" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" placeholder="e.g., 081234567890" value="{{ old('phone', Auth::user()->phone ?? ($address['phone'] ?? '')) }}">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="first_name" class="form-label">First Name</label>
                                    <input type="text" class="form-control @error('first_name') is-invalid @enderror" id="first_name" name="first_name" placeholder="John" value="{{ old('first_name', Auth::user()->first_name ?? ($address['name'] ? explode(' ', $address['name'])[0] : '')) }}">
                                    @error('first_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="last_name" class="form-label">Last Name</label>
                                    <input type="text" class="form-control @error('last_name') is-invalid @enderror" id="last_name" name="last_name" placeholder="Doe" value="{{ old('last_name', Auth::user()->last_name ?? (isset(explode(' ', $address['name'])[1]) ? explode(' ', $address['name'])[1] : '')) }}">
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
                                    <input type="hidden" name="payment_method" value="credit_card">
                                    <p class="fixed-payment-method">Credit Card</p>
                                </div>
                            </div>

                            <input type="hidden" name="total_amount" value="{{ $grandTotal }}">

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
                    <h2 class="order-summary section-title fs-3 text-center">Order Summary</h2>

                    <div class="order-items-scroll-container">
                        <div class="order-items">
                            @forelse ($cartItems as $item)
                            <div class="order-item">
                                <img src="{{ asset('storage/' . $item['product_image']) }}" alt="{{ $item['product_name'] }}" class="img-fluid object-fit-contain" />
                                <div class="order-item-details">
                                    <p class="fw-semibold mb-0 fs-6" title="{{ $item['product_name'] }}">
                                        {{ Str::limit($item['product_name'], 32, '...') }}
                                    </p>
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

                    <div class="summary-total">
                        <span>TOTAL</span>
                        <span>IDR {{ number_format($grandTotal, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

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

        const userAddressesData = {{ Js::from($userAddresses) }};

        window.addEventListener('addressSelected', function(event) {
            const selectedAddressData = event.detail.addressData;

            document.getElementById('currentAddressType').textContent = selectedAddressData.type || '';
            const addressDetailsContainer = document.getElementById('currentAddressDetails');
            addressDetailsContainer.innerHTML = `
                <p class="text-muted mb-0" data-address-line="street">${selectedAddressData.street || ''}${selectedAddressData.sub_district ? ', ' + selectedAddressData.sub_district : ''}</p>
                <p class="text-muted mb-0" data-address-line="district-city">${selectedAddressData.district || ''}${selectedAddressData.city ? ', ' + selectedAddressData.city : ''}</p>
                <p class="text-muted mb-0" data-address-line="province-postal">${selectedAddressData.province || ''} ${selectedAddressData.postal_code || ''}</p>
            `;

            if (formAddressType) formAddressType.value = selectedAddressData.type || '';
            if (formStreet) formStreet.value = selectedAddressData.street || '';
            if (formSubDistrict) formSubDistrict.value = selectedAddressData.sub_district || '';
            if (formDistrict) formDistrict.value = selectedAddressData.district || '';
            if (formCity) formCity.value = selectedAddressData.city || '';
            if (formProvince) formProvince.value = selectedAddressData.province || '';
            if (formPostalCode) formPostalCode.value = selectedAddressData.postal_code || '';
            if (userNameInput) userNameInput.value = selectedAddressData.name || '';
            if (userPhoneInput) userPhoneInput.value = selectedAddressData.phone || '';

            document.getElementById('email').value = document.getElementById('email').value || '';
            document.getElementById('phone').value = selectedAddressData.phone || '';
            document.getElementById('first_name').value = selectedAddressData.name ? selectedAddressData.name.split(' ')[0] : '';
            document.getElementById('last_name').value = selectedAddressData.name ? (selectedAddressData.name.split(' ').length > 1 ? selectedAddressData.name.split(' ')[1] : '') : '';
        });

        const currentSelectedAddressId = {{ Js::from($selectedAddressId) }};
        if (userAddressesData.length > 0 && currentSelectedAddressId !== null) {
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

                document.getElementById('phone').value = defaultAddressFromPHP.phone || '';
                document.getElementById('first_name').value = defaultAddressFromPHP.name ? defaultAddressFromPHP.name.split(' ')[0] : '';
                document.getElementById('last_name').value = defaultAddressFromPHP.name ? (defaultAddressFromPHP.name.split(' ').length > 1 ? defaultAddressFromPHP.name.split(' ')[1] : '') : '';
            }
        }
    });
</script>
@endpush