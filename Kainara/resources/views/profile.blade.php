@extends('layouts.app')

@section('title', 'Profile')

@push('styles')
<style>
    body {
        background-color: #f8f9fa; /* Warna background netral */
    }

    .profile-header-bg {
        background-image: url('{{ asset('images/BG/cloud bg.png') }}');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        height: 200px; /* Tinggi header background disesuaikan */
        position: relative;
        z-index: 1; /* Pastikan background di bawah gambar profil */
    }

    /* Wrapper untuk gambar profil agar bisa di-center dan diberi negative margin */
    .profile-picture-wrapper {
        display: flex; /* Menggunakan flexbox untuk centering */
        justify-content: center; /* Center horizontal */
        margin-top: -6vw; /* Tarik gambar ke atas (setengah dari tinggi gambar 150px) */
        z-index: 10; /* Pastikan gambar profil di atas background */
        position: relative; /* Diperlukan untuk z-index agar berfungsi */
    }

    .profile-picture-container {
        background-color: white; /* Background putih untuk lingkaran */
        border-radius: 50%;
        padding: 5px; /* Padding untuk border putih */
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        position: relative; /* Untuk absolute positioning overlay */
        overflow: hidden; /* Untuk memotong overlay agar tetap lingkaran */
        cursor: pointer; /* Menunjukkan bahwa area ini bisa diklik */
    }

    .profile-picture {
        width: 12vw; /* Ukuran gambar profil */
        height: 12vw;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #ddd; /* Border tipis di sekitar gambar */
        display: block; /* Menghilangkan spasi ekstra di bawah gambar */
    }

    /* Overlay yang muncul saat hover */
    .profile-picture-overlay {
        position: absolute; /* Penting untuk overlay agar menutupi elemen induknya */
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5); /* Overlay hitam semi-transparan */
        border-radius: 50%; /* Bentuk lingkaran */
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0; /* Awalnya tersembunyi */
        transition: opacity 0.3s ease; /* Transisi halus */
        z-index: 2; /* Pastikan di atas gambar profil */
    }

    .profile-picture-container:hover .profile-picture-overlay {
        opacity: 1; /* Tampilkan saat hover */
    }

    .edit-icon-pencil {
        font-size: 1.8rem; /* Ukuran ikon pensil disesuaikan dari 2.5rem */
        color: white; /* Warna ikon pensil */
    }

    .profile-content-area {
        margin-top: 20px; /* Sesuaikan margin karena gambar sudah ditarik ke atas */
        margin-bottom: 20px;
        padding: 20px;
        text-align: center;
        width: 75vw;
    }

    .user-name {
        font-size: 40px;
        font-weight: bold; /* Ditambahkan font-weight bold */
        margin-bottom: 20px;
        color: #333;
    }

    .profile-tabs .nav-link {
        color: #6c757d;
        font-weight: 500;
        border: none;
        border-bottom: 2px solid transparent;
        padding: 10px 20px;
        transition: all 0.3s ease;
    }

    .profile-tabs .nav-link.active {
        color: #AD9D6D; /* Example: Use the new gold color for active tab */
        border-bottom-color: #AD9D6D;
        background-color: transparent;
    }

    .profile-tabs .nav-link:hover {
        color: #AD9D6D; /* Example: Use the new gold color for hover tab */
        border-bottom-color: #e9ecef;
    }

    .tab-content {
        background-color: #ffffff;
        border-radius: 8px;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.05);
        padding: 30px;
        margin-top: 30px;
        min-height: 50vh;
        overflow-y: auto; /* Enable vertical scrolling */
        max-height: 70vh; /* Adjust as needed to allow scrolling */
    }

    .tab-content::-webkit-scrollbar {
        width: 8px; /* Width of the scrollbar */
    }

    .tab-content::-webkit-scrollbar-track {
        background: #f1f1f1; /* Color of the scrollbar track */
        border-radius: 10px;
    }

    .tab-content::-webkit-scrollbar-thumb {
        background: #ccc; /* Color of the scrollbar thumb */
        border-radius: 10px;
    }

    .tab-content::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8; /* Color of the scrollbar thumb on hover */
    }

    /* New Order Card Styling */
    .card-order {
        display: flex;
        flex-direction: column;
        gap: 20px; /* Space between individual order cards */
    }

    .order-card {
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
        background-color: #fff;
        display: flex;
        flex-direction: column;
        gap: 15px; /* Space between sections within the card */
    }

    .order-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-bottom: 15px;
        border-bottom: 1px dashed #e0e0e0; /* Dashed line separator */
    }

    .order-header .icon {
        font-size: 1.8rem;
        color: #AD9D6D; /* Gold color for the basket icon */
    }

    .order-header .order-info {
        flex-grow: 1; /* Allows info to take available space */
        text-align: left;
        margin-left: 10px; /* Space from icon */
    }

    .order-header .order-info strong {
        font-size: 1.1rem;
        color: #333;
    }

    .order-header .order-info span {
        font-size: 0.9rem;
        color: #666;
    }

    .order-details-summary {
        display: flex;
        align-items: center;
        gap: 15px;
        padding-bottom: 15px;
        border-bottom: 1px dashed #e0e0e0; /* Dashed line separator */
    }

    .order-details-summary img {
        width: 90px; /* Slightly larger image */
        height: 90px;
        object-fit: cover;
        border-radius: 4px;
        flex-shrink: 0;
    }

    .order-details-summary .order-item-info {
        flex-grow: 1;
        text-align: left;
    }

    .order-details-summary .order-item-info h4 {
        font-size: 1.1rem;
        margin-bottom: 5px;
        color: #333;
    }

    .order-details-summary .order-item-info p {
        font-size: 0.9rem;
        color: #666;
        margin-bottom: 3px;
    }

    .order-total {
        text-align: right;
        white-space: nowrap; /* Prevent total from wrapping */
    }

    .order-total strong {
        font-size: 1.3rem;
        color: #AD9D6D; /* Gold color for total price */
    }

    .order-actions {
        display: flex;
        justify-content: flex-end; /* Align buttons to the right */
        gap: 10px;
    }

    .btn-transaction-detail,
    .btn-track {
        padding: 8px 15px;
        border-radius: 5px;
        font-size: 0.9rem;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn-transaction-detail {
        background-color: #AD9D6D; /* Gold color */
        color: white;
        border: 1px solid #AD9D6D;
    }

    .btn-transaction-detail:hover {
        background-color: #B39C59; /* Slightly darker gold on hover */
        border-color: #B39C59;
        color: white;
    }

    .btn-track {
        background-color: transparent;
        color: #AD9D6D; /* Gold text */
        border: 1px solid #AD9D6D; /* Gold border */
    }

    .btn-track:hover {
        background-color: #AD9D6D;
        color: white;
    }

    .order-status {
        padding: 5px 10px;
        border-radius: 5px;
        font-size: 0.9rem;
        font-weight: 500;
        text-transform: capitalize; /* Capitalize first letter of each word */
    }

    /* Existing status styles (ensure these are present) */
    .status-completed { background-color: #9ad5a8; } /* Hijau */

    .profile-options {
        font-size: 1.2rem; /* Ukuran font untuk tab nav-link */
    }

    .status-completed { background-color: #28a745; } /* Hijau */
    .status-waiting { background-color: #007bff; }    /* Biru */
    .status-cancelled { background-color: #dc3545; } /* Merah */

    /* New styles for Personal Information tab */
    .personal-info-section {
        display: flex;
        flex-wrap: wrap; /* Allow wrapping on smaller screens */
        gap: 30px; /* Space between columns */
        justify-content: center; /* Center columns if not full width */
        text-align: left; /* Override parent text-align center */
    }

    .personal-info-column {
        flex: 1; /* Allow columns to grow */
        min-width: 280px; /* Minimum width before wrapping */
    }

    .personal-info-title {
        font-size: 35px; /* My Account title */
        margin-bottom: 8px;
        text-align: left;
        opacity: 45%;
    }

    .personal-info-subtitle {
        font-size: 16px; /* Private Info / Profile Info */
        font-weight: 600;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        opacity: 45%;
    }

    .personal-info-item {
        margin-bottom: 15px;
        margin-left: 20px;
    }

    .personal-info-label {
        font-size: 16px;
        opacity: 45%;
        display: block;
    }

    .personal-info-value {
        font-size: 30px;
        font-style: italic; /* Sesuai gambar */
        color: #333;
        font-weight: normal; /* Untuk menimpa bold dari parent */
    }

    .btn-logout {
        background-color: #f0f0f0;
        color: #ec1f1f;
        border: 1px solid #ccc;
        padding: 8px 15px;
        border-radius: 5px;
        font-size: 0.95rem;
        transition: background-color 0.3s ease;
    }

    .btn-change-password:hover {
        background-color: #c72020;
    }

    .separator {
        border-left: 0.1px #000000 solid;
        opacity: 30%;
    }

    /* Updated Button Styles */
    .btn-add-address {
        background-color: #B39C59; /* Background color from image */
        color: white; /* White text for contrast */
        border: 1px solid #AD9D6D; /* Subtle border */
        padding: 8px 15px; /* Adjust padding as needed */
        border-radius: 5px; /* Slightly rounded corners */
        font-size: 0.95rem; /* Adjust font size */
        transition: all 0.3s ease;
        text-decoration: none; /* Ensure no underline from <a> tag */
        display: inline-flex; /* Use flexbox for icon and text alignment */
        align-items: center; /* Vertically center icon and text */
        gap: 5px; /* Space between icon and text */
    }

    .btn-add-address:hover {
        background-color: #AD9D6D; /* Darken on hover */
        color: white;
        border-color: #AD9D6D;
    }

    /* New styles for Edit/Delete action buttons */
    .btn-address-action {
        background-color: transparent;
        color: #AD9D6D; /* Gold color for text */
        border: none;
        padding: 0.25rem 0.5rem; /* Small padding */
        font-size: 0.9rem; /* Slightly smaller font */
        font-weight: 500; /* Medium weight */
        border-radius: 0.25rem; /* Small rounded corners */
        transition: background-color 0.2s ease, color 0.2s ease;
        cursor: pointer;
        display: inline-flex; /* To align with other text/buttons */
        align-items: center;
        gap: 4px; /* Space between icon (if any) and text */
    }

    .btn-address-action:hover {
        background-color: rgba(173, 157, 109, 0.1); /* Light gold background on hover */
        color: #AD9D6D; /* Keep text color same */
        text-decoration: none; /* Ensure no underline on hover */
    }

    /* Remove previous action link styles */
    .address-actions .action-links a {
        /* These styles are now overridden or no longer apply to buttons */
        /* font-size: 1rem; */
        /* opacity: 45%; */
        /* text-decoration: underline; */
    }

    .address-actions .action-links a:hover {
        /* opacity: 100%; */
        /* color: #AD9D6D */
    }

    .address-item {
        border: 1px solid #e0e0e0; /* Light border */
        border-radius: 8px; /* Rounded corners */
        padding: 15px 20px; /* Padding inside the box */
        margin-bottom: 15px; /* Space between address items */
        box-shadow: 0 2px 4px rgba(0,0,0,0.03); /* Subtle shadow */
    }

    /* Styles for default address tag */
    .primary-address-tag { /* Renamed from .primary-address-tag to reflect 'default' */
        color: #B39C59;
        border-radius: 4px;
        font-size: 1.2rem;
        text-transform: uppercase;
    }

    /* Styles for selected address item border */
    .address-item.selected-address {
        border-color: #AD9D6D; /* Changed to direct hex code for consistency */
        box-shadow: 0 0 0 2px rgba(173, 157, 109, 0.3); /* Adjusted rgba based on #AD9D6D */
    }

    .address-label {
        font-size: 1.5rem;
    }

    /* NEW: Input Field Focus Styles */
    .form-control:focus,
    textarea.form-control:focus { /* Target textareas as well */
        border-color: #AD9D6D; /* Set border color on focus */
        box-shadow: 0 0 0 0.25rem rgba(173, 157, 109, 0.25); /* Custom focus shadow (lighter #AD9D6D) */
        outline: 0; /* Remove default outline */
    }

    /* Style for the "Save Address" button in the modal footer */
    .modal-footer .btn-primary {
        background-color: #B39C59 !important; /* Changed to direct hex code */
        border-color: #AD9D6D !important; /* Changed to direct hex code */
    }

    /* Styles for Bootstrap form-switch active state */
    .form-switch .form-check-input:checked {
        background-color: #B39C59; /* Background color of the switch when active */
        border-color: #AD9D6D; /* Border color of the switch when active */
    }
    .form-switch .form-check-input:focus {
        border-color: #AD9D6D;
        box-shadow: 0 0 0 0.25rem rgba(173, 157, 109, 0.25);
    }

</style>
@endpush

@section('content')
<div class="profile-header-bg">
    {{-- Gambar profil tidak lagi di dalam div ini --}}
</div>

{{-- Wrapper baru untuk gambar profil dan overlay --}}
<div class="profile-picture-wrapper">
    <div class="profile-picture-container">
        {{-- Form for profile picture upload --}}
        <form id="profile_picture_form" action="{{ route('profile.update_picture') }}" method="POST" enctype="multipart/form-data" class="d-none">
            @csrf
            <input type="file" id="profile_picture_input" name="profile_picture" accept="image/*" onchange="submitProfilePictureForm(event)">
        </form>

        {{-- Gunakan id untuk pratinjau gambar --}}
        <img id="profile_picture_preview" src="{{ asset($user->profile_picture ?? 'images/default-profile.png') }}" alt="Profile Picture" class="profile-picture">

        {{-- Overlay dengan ikon pensil dan label yang memicu input file --}}
        <div class="profile-picture-overlay">
            <label for="profile_picture_input" class="d-flex align-items-center justify-content-center w-100 h-100 cursor-pointer">
                <i class="fas fa-pencil-alt edit-icon-pencil"></i>
            </label>
        </div>
    </div>
</div>

<div class="container profile-content-area">
    <h1 class="user-name font-serif-semibold">{{ $user->first_name }} {{ $user->last_name }}</h1>

    <ul class="nav nav-tabs profile-tabs justify-content-center profile-options gap-2 mt-5" id="profileTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active font-serif-light-italic" id="personal-info-tab" data-bs-toggle="tab" data-bs-target="#personal-info" type="button" role="tab" aria-controls="personal-info" aria-selected="true">Personal Information</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link font-serif-light-italic" id="order-history-tab" data-bs-toggle="tab" data-bs-target="#order-history" type="button" role="tab" aria-controls="order-history" aria-selected="false">Order History</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link font-serif-light-italic" id="addresses-tab" data-bs-toggle="tab" data-bs-target="#addresses" type="button" role="tab" aria-controls="addresses" aria-selected="false">Addresses</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link font-serif-light-italic" id="help-support-tab" data-bs-toggle="tab" data-bs-target="#help-support" type="button" role="tab" aria-controls="help-support" aria-selected="false">Help & Support</button>
        </li>
    </ul>

    <div class="tab-content" id="profileTabContent">
        {{-- Tab Pane: Personal Information --}}
        <div class="tab-pane fade show active" id="personal-info" role="tabpanel" aria-labelledby="personal-info-tab">
            <div class="addresses-header d-flex justify-content-between align-items-center mb-4">
                <h3 class="personal-info-title mb-0 font-serif-medium">My Account</h3>
                {{-- Removed the "Edit Profile" button here --}}
            </div>
            <hr class="mb-4"> {{-- Garis pemisah --}}

            <div class="personal-info-section">
                {{-- Kolom Kiri: Private Info --}}
                <div class="personal-info-column">
                    <h4 class="personal-info-subtitle">
                        Private Info
                        {{-- Pencil icon now triggers the modal --}}
                        <button type="button" class="btn btn-sm btn-address-action" data-bs-toggle="modal" data-bs-target="#editPersonalInfoModal">
                            <i class="fas fa-pencil-alt"></i>
                        </button>
                    </h4>
                    <div class="personal-info-item">
                        <span class="personal-info-label font-serif-medium">First Name</span>
                        <p class="personal-info-value font-serif-medium">{{ $user->first_name }}</p>
                    </div>
                    <div class="personal-info-item">
                        <span class="personal-info-label font-serif-medium">Last Name</span>
                        <p class="personal-info-value font-serif-medium">{{ $user->last_name }}</p>
                    </div>
                    <div class="personal-info-item">
                        <span class="personal-info-label font-serif-medium">Date of Birth</span>
                        <p class="personal-info-value font-serif-medium">{{ $user->dob ? $user->dob->format('d F Y') : 'N/A' }}</p>
                    </div>
                </div>

                <div class="separator"></div>

                {{-- Kolom Kanan: Profile Info --}}
                <div class="personal-info-column">
                    <h4 class="personal-info-subtitle font-serif-medium">Profile Info</h4>
                    <div class="personal-info-item">
                        <span class="personal-info-label font-serif-medium">Email</span>
                        <p class="personal-info-value font-serif-medium">{{ $user->email }}</p>
                    </div>
                    <div class="personal-info-item mt-4">
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-logout font-serif-medium">Logout</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tab Pane: Order History --}}
        <div class="tab-pane fade" id="order-history" role="tabpanel" aria-labelledby="order-history-tab">
            <div class="addresses-header d-flex justify-content-between align-items-center mb-4">
                <h3 class="personal-info-title mb-0 font-serif-medium">Order History</h3>
            </div>
            <hr class="mb-4">

            <div class="card-order mt-5">
                @forelse ($userOrders as $order)
                    <div class="order-card">
                        <div class="order-header">
                            <i class="fas fa-shopping-basket icon me-3"></i>
                            <div class="order-info">
                                <strong>Order</strong> | {{ \Carbon\Carbon::parse($order->created_at)->format('d F Y') }}
                                <br>
                                <span>INV/{{ \Carbon\Carbon::parse($order->created_at)->format('Ymd') }}/{{ $order->id }}</span>
                            </div>
                            <span class="order-status status-{{ Str::slug($order->status) }}">{{ $order->status }}</span>
                        </div>

                        <div class="order-details-summary">
                            @php
                                $firstItem = $order->orderItems->first();
                                $remainingItemsCount = $order->orderItems->count() - 1;
                            @endphp

                            @if ($firstItem && $firstItem->product)
                                {{-- Assuming product_image and product_name come from the product relationship --}}
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
                        <button type="button" class="btn btn-transaction-detail"
                                data-bs-toggle="modal"
                                data-bs-target="#transactionDetailModal"
                                data-order="{{ json_encode([
                                    'id' => $order->id,
                                    'invoice' => 'INV/' . \Carbon\Carbon::parse($order->created_at)->format('Ymd') . '/' . $order->id, // Use the calculated invoice
                                    'order_date' => \Carbon\Carbon::parse($order->created_at)->format('d F Y'),
                                    'status' => $order->status,
                                    'total_amount' => 'IDR ' . number_format($order->grand_total, 0, ',', '.'),
                                    'shipping_address' => [
                                        'recipient_name' => $order->shipping_recipient_name ?? 'N/A',
                                        'phone' => $order->shipping_phone ?? 'N/A',
                                        'address' => $order->shipping_address ?? 'N/A',
                                        'city' => $order->shipping_city ?? 'N/A',
                                        'province' => $order->shipping_province ?? 'N/A',
                                        'country' => $order->shipping_country ?? 'N/A',
                                        'postal_code' => $order->shipping_postal_code ?? 'N/A',
                                    ],
                                    'items' => $order->orderItems->map(function($item) {
                                        return [
                                            'product_name' => $item->product->name ?? 'Unknown Product',
                                            'quantity' => $item->quantity,
                                            'price' => 'IDR ' . number_format($item->price, 0, ',', '.'),
                                            'image' => asset('storage/' . ($item->product->image ?? 'images/default-product.png')), // Default image if none
                                        ];
                                    })->toArray(),
                                ]) }}">
                            Transaction Detail
                        </button>
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

        {{-- Tab Pane: Addresses --}}
        <div class="tab-pane fade" id="addresses" role="tabpanel" aria-labelledby="addresses-tab">
            <div class="addresses-header d-flex justify-content-between align-items-center mb-4">
                <h3 class="personal-info-title mb-0 font-serif-medium">Addresses</h3>
                <button type="button" class="btn btn-add-address font-serif-medium" data-bs-toggle="modal" data-bs-target="#addAddressModal">
                    <i class="fas fa-plus"></i> Add new address
                </button>
            </div>
            <hr class="mb-4">

            @forelse ($userAddresses as $userAddress)
                <div class="address-item d-flex justify-content-between align-items-start mb-3" data-address-id="{{ $userAddress['id'] ?? '' }}">
                    <div class="address-details d-flex flex-column align-items-start">
                        <h6 class="mb-0 font-serif-medium address-label">{{ $userAddress['label'] ?? 'Alamat' }}</h6>
                        <p class="address-name-phone fw-bold mb-0 font-serif-medium">{{ $userAddress['recipient_name'] ?? '' }} | {{ $userAddress['phone'] ?? '' }}</p>
                        <p class="text-muted mb-0 font-serif-light-italic" data-address-line="address">{{ $userAddress['address'] ?? '' }}</p>
                        <p class="text-muted mb-0 font-serif-light-italic" data-address-line="city-province">{{ $userAddress['city'] ?? '' }}{{ ($userAddress['city'] && $userAddress['province']) ? ', ' : '' }}{{ $userAddress['province'] ?? '' }}</p>
                        <p class="text-muted mb-0 font-serif-light-italic" data-address-line="country-postal">{{ $userAddress['country'] ?? '' }} {{ $userAddress['postal_code'] ?? '' }}</p>
                    </div>
                    <div class="address-actions d-flex flex-column align-items-end">
                        <div class="action-links font-serif-light-italic">
                            {{-- Changed to button with new class --}}
                            <button type="button" class="btn btn-sm btn-address-action" data-bs-toggle="modal" data-bs-target="#editAddressModal" data-address-id="{{ $userAddress->id }}">
                                Edit
                            </button>
                            |
                            {{-- Changed to button with new class --}}
                            <form action="{{ route('addresses.destroy', $userAddress->id) }}" method="POST" class="d-inline" id="delete-address-form-{{ $userAddress->id }}">
                                @csrf
                                @method('DELETE')
                                <button type="button"
                                        class="btn btn-sm btn-address-action trigger-delete-address-notification"
                                        data-address-id="{{ $userAddress->id }}"
                                        data-address-label="{{ $userAddress->label ?? 'alamat ini' }}">
                                    Delete
                                </button>
                            </form>
                        </div>
                        @if (($userAddress['is_default'] ?? false))
                            <span class="primary-address-tag font-serif-medium">Primary Address</span>
                        @endif
                    </div>
                </div>
            @empty
                <p class="text-center text-muted font-serif-light-italic">No addresses found. Please add a new address.</p>
            @endforelse
        </div>

        {{-- Tab Pane: Help & Support --}}
        <div class="tab-pane fade" id="help-support" role="tabpanel" aria-labelledby="help-support-tab">
            <div class="addresses-header d-flex justify-content-between align-items-center mb-4">
                <h3 class="personal-info-title mb-0 font-serif-medium">Help & Support</h3>
            </div>
            <hr class="mb-4">
            <div class="faq-section text-left">
                <h4>Frequently Asked Questions (FAQ)</h4>
                <div class="accordion" id="faqAccordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingOne">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                                How do I change my personal information (name, date of birth)?
                            </button>
                        </h2>
                        <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                You can change your first name and last name in the "Personal Information" tab on your profile page. Click the pencil icon next to "Private Info" to edit. For date of birth, if it's not directly editable, please contact our support team.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingTwo">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                How do I change my profile picture?
                            </button>
                        </h2>
                        <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Hover over your profile picture at the top of your profile page. A pencil icon will appear. Click the icon to upload a new image.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingThree">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                I forgot my password, how do I reset it?
                            </button>
                        </h2>
                        <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                You can use the "Forgot Password" feature on the login page. Follow the instructions sent to your registered email.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingFour">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                                How do I add a new address?
                            </button>
                        </h2>
                        <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                In the "Addresses" tab, click the "+ Add new address" button. Fill in the address details in the modal that appears and save.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingFive">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                                How do I edit an existing address?
                            </button>
                        </h2>
                        <div id="collapseFive" class="accordion-collapse collapse" aria-labelledby="headingFive" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                In the "Addresses" tab, find the address you want to edit, then click the "Edit" button next to it. A modal will appear with the pre-filled address details; you can modify them and save the changes.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingSix">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSix" aria-expanded="false" aria-controls="collapseSix">
                                How do I delete an address?
                            </button>
                        </h2>
                        <div id="collapseSix" class="accordion-collapse collapse" aria-labelledby="headingSix" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                In the "Addresses" tab, find the address you want to delete, then click the "Delete" button next to it. You will be asked for confirmation before the address is permanently deleted.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingSeven">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSeven" aria-expanded="false" aria-controls="collapseSeven">
                                How do I set a default address?
                            </button>
                        </h2>
                        <div id="collapseSeven" class="accordion-collapse collapse" aria-labelledby="headingSeven" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                When adding or editing an address, there is an option "Set as Default Address". Check this box to make this your primary address. Only one address can be set as default.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingEight">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseEight" aria-expanded="false" aria-controls="collapseEight">
                                How do I view my order history?
                            </button>
                        </h2>
                        <div id="collapseEight" class="accordion-collapse collapse" aria-labelledby="headingEight" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                All your past orders can be viewed in the "Order History" tab. You will see details such as order ID, number of items, total price, shipping address, and order status.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingNine">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseNine" aria-expanded="false" aria-controls="collapseNine">
                                What do the different order statuses mean?
                            </button>
                        </h2>
                        <div id="collapseNine" class="accordion-collapse collapse" aria-labelledby="headingNine" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                <ul>
                                    <li><strong>Completed:</strong> The order has been successfully processed and delivered.</li>
                                    <li><strong>Waiting for Payment:</strong> Your order has been placed but payment has not yet been confirmed.</li>
                                    <li><strong>Cancelled:</strong> The order has been cancelled.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingTen">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTen" aria-expanded="false" aria-controls="collapseTen">
                                I have an issue with my order, what should I do?
                            </button>
                        </h2>
                        <div id="collapseTen" class="accordion-collapse collapse" aria-labelledby="headingTen" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                If you have an issue with an order, please note your order ID and contact our support team using the information in this "Help & Support" tab.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingEleven">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseEleven" aria-expanded="false" aria-controls="collapseEleven">
                                How do I contact the support team?
                            </button>
                        </h2>
                        <div id="collapseEleven" class="accordion-collapse collapse" aria-labelledby="headingEleven" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                You can find our support team's contact information on this "Help & Support" tab. We are ready to assist you with any questions or issues.
                            </div>
                        </div>
                    </div>
                </div>
                <p class="mt-4">If your question is not answered here, feel free to contact us!</p>
            </div>
        </div>
    </div>
</div>

{{-- Memanggil modal dari komponen terpisah --}}
{{-- Pastikan variabel $user dilewatkan jika dibutuhkan di dalam modal --}}
@include('components.add-address-modal', ['user' => $user])

{{-- Memanggil modal edit address dari komponen terpisah --}}
@include('components.edit-address-modal')

{{-- Memanggil modal edit personal information dari komponen terpisah --}}
@include('components.edit-personal-info-modal', ['user' => $user])

{{-- Memanggil modal transaction-detail-modal dari komponen terpisah --}}
@include('components.transaction-detail-modal')
@endsection

@push('scripts')
<script>
    // Function to handle profile picture preview and submission
    function submitProfilePictureForm(event) {
        const reader = new FileReader();
        reader.onload = function() {
            const output = document.getElementById('profile_picture_preview');
            output.src = reader.result;
        }
        reader.readAsDataURL(event.target.files[0]);

        // Submit the form automatically
        document.getElementById('profile_picture_form').submit();
    }

    // JavaScript for populating the Edit Address modal
    document.addEventListener('DOMContentLoaded', function () {
        // Embed user addresses data directly from Blade
        const allUserAddresses = @json($userAddresses->keyBy('id')); // Convert collection to JSON, keyed by ID

        const editAddressModal = document.getElementById('editAddressModal');
        if (editAddressModal) { // Add a check to ensure modal exists
            editAddressModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const addressId = button.getAttribute('data-address-id');

                // Save the current active tab before opening the modal, in case the user closes it
                const activeTab = document.querySelector('.profile-tabs .nav-link.active');
                if (activeTab) {
                    const activeTabId = activeTab.getAttribute('data-bs-target').substring(1);
                    localStorage.setItem('lastActiveProfileTab', activeTabId);
                }

                // Get address data from local JavaScript variable
                const address = allUserAddresses[addressId];

                if (address) {
                    document.getElementById('editAddressForm').action = `/addresses/${address.id}`;
                    document.getElementById('edit_address_label').value = address.label || '';
                    document.getElementById('edit_recipient_name').value = address.recipient_name || '';
                    document.getElementById('edit_phone').value = address.phone || '';
                    document.getElementById('edit_address_full').value = address.address || '';
                    document.getElementById('edit_country').value = address.country || '';
                    document.getElementById('edit_city').value = address.city || '';
                    document.getElementById('edit_province').value = address.province || '';
                    document.getElementById('edit_postal_code').value = address.postal_code || '';
                    document.getElementById('edit_is_default').checked = address.is_default;
                } else {
                    console.error('Address data not found locally for ID:', addressId);
                    alert('Failed to load address data. Please try again.');
                    const modal = bootstrap.Modal.getInstance(editAddressModal);
                    modal.hide();
                }
            });
        }

        // --- Personal Information Edit Modal Logic ---
        const editPersonalInfoModal = document.getElementById('editPersonalInfoModal');
        if (editPersonalInfoModal) {
            editPersonalInfoModal.addEventListener('show.bs.modal', function (event) {
                // Save current active tab
                const activeTab = document.querySelector('.profile-tabs .nav-link.active');
                if (activeTab) {
                    const activeTabId = activeTab.getAttribute('data-bs-target').substring(1);
                    localStorage.setItem('lastActiveProfileTab', activeTabId);
                }

                // Populate the form fields with current user data
                const user = @json($user); // Embed current user data directly

                document.getElementById('edit_personal_first_name').value = user.first_name || '';
                document.getElementById('edit_personal_last_name').value = user.last_name || '';
                document.getElementById('edit_personal_email').value = user.email || '';
                // Format DOB for input type="date"
                if (user.dob) {
                    const dobDate = new Date(user.dob);
                    const year = dobDate.getFullYear();
                    const month = (dobDate.getMonth() + 1).toString().padStart(2, '0');
                    const day = dobDate.getDate().toString().padStart(2, '0');
                    document.getElementById('edit_personal_dob').value = `${year}-${month}-${day}`;
                } else {
                    document.getElementById('edit_personal_dob').value = '';
                }
            });
        }


        // --- GLOBAL TAB LOGIC ---
        const profileTab = document.getElementById('profileTab');
        if (profileTab) {
            const tabButtons = profileTab.querySelectorAll('.nav-link');

            // Function to activate a specific tab
            const activateTab = (tabId) => {
                const targetTabButton = document.getElementById(tabId + '-tab');
                const targetTabContent = document.getElementById(tabId);

                if (targetTabButton && targetTabContent) {
                    // Deactivate all other tabs and their content
                    tabButtons.forEach(link => {
                        link.classList.remove('active');
                        link.setAttribute('aria-selected', 'false');
                    });
                    document.querySelectorAll('.tab-pane').forEach(pane => {
                        pane.classList.remove('show', 'active');
                    });

                    // Activate the target tab button
                    targetTabButton.classList.add('active');
                    targetTabButton.setAttribute('aria-selected', 'true');

                    // Activate the target tab content
                    targetTabContent.classList.add('show', 'active');
                }
            };

            // 1. Handle tab changes via clicks: Save to localStorage
            tabButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const activeTabId = this.getAttribute('data-bs-target').substring(1); // Get "personal-info", "addresses", etc.
                    localStorage.setItem('lastActiveProfileTab', activeTabId);
                });
            });

            // 2. On page load: Try to restore from URL hash or localStorage
            let initialTabId = null;

            // Priority 1: Check URL hash (e.g., #addresses)
            if (window.location.hash) {
                const hashId = window.location.hash.substring(1);
                // Ensure the hash corresponds to a valid tab
                if (document.getElementById(hashId) && document.getElementById(hashId + '-tab')) {
                    initialTabId = hashId;
                }
            }

            // Priority 2: Check localStorage if no valid hash
            if (!initialTabId) {
                const storedTabId = localStorage.getItem('lastActiveProfileTab');
                if (storedTabId && document.getElementById(storedTabId) && document.getElementById(storedTabId + '-tab')) {
                    initialTabId = storedTabId;
                }
            }

            // Priority 3: Default to 'personal-info' if no stored tab or hash
            if (!initialTabId) {
                initialTabId = 'personal-info';
            }

            // Activate the determined tab
            activateTab(initialTabId);
        }
    });

    // --- LOGIC FOR CUSTOM ADDRESS DELETION NOTIFICATION ---
    const deleteAddressButtons = document.querySelectorAll('.trigger-delete-address-notification');

    deleteAddressButtons.forEach(button => {
        button.addEventListener('click', function(event) {
            event.preventDefault(); // Prevent direct form submission

            const addressId = this.dataset.addressId;
            const addressLabel = this.dataset.addressLabel;
            const targetForm = document.getElementById(`delete-address-form-${addressId}`);

            if (typeof window.showNotificationCard === 'function') {
                window.showNotificationCard({
                    type: 'confirmation',
                    title: 'Confirm Address Deletion',
                    message: `Are you sure you want to delete "${addressLabel}"? This action cannot be undone.`,
                    hasActions: true,
                    onConfirm: () => {
                        if (targetForm) {
                            // Before submitting, save the current active tab to localStorage
                            const activeTab = document.querySelector('.profile-tabs .nav-link.active');
                            if (activeTab) {
                                const activeTabId = activeTab.getAttribute('data-bs-target').substring(1);
                                localStorage.setItem('lastActiveProfileTab', activeTabId);
                            }
                            targetForm.submit();
                        }
                    },
                    onCancel: () => {
                        // console.log('Address deletion cancelled.');
                    }
                });
            } else {
                console.warn('window.showNotificationCard function not found. Falling back to native confirm.');
                if (confirm(`Are you sure you want to delete "${addressLabel}"?`)) {
                    if (targetForm) {
                        const activeTab = document.querySelector('.profile-tabs .nav-link.active');
                        if (activeTab) {
                            const activeTabId = activeTab.getAttribute('data-bs-target').substring(1);
                            localStorage.setItem('lastActiveProfileTab', activeTabId);
                        }
                        targetForm.submit();
                    }
                }
            }
        });
    });

    document.addEventListener('DOMContentLoaded', function () {
    const transactionDetailModal = document.getElementById('transactionDetailModal');
    if (transactionDetailModal) {
        transactionDetailModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget; // Button that triggered the modal
            const orderData = JSON.parse(button.getAttribute('data-order'));

            // Populate Order Information
            document.getElementById('modalOrderId').textContent = orderData.id;
            document.getElementById('modalInvoice').textContent = orderData.invoice;
            document.getElementById('modalOrderDate').textContent = orderData.order_date;

            const modalOrderStatus = document.getElementById('modalOrderStatus');
            modalOrderStatus.textContent = orderData.status;
            // Remove previous status classes and add the new one
            modalOrderStatus.className = 'badge order-status'; // Reset classes
            modalOrderStatus.classList.add(`status-${orderData.status.toLowerCase().replace(/\s/g, '-')}`);


            // Populate Shipping Address
            const shippingAddress = orderData.shipping_address;
            document.getElementById('modalShippingNamePhone').innerHTML = `<strong>${shippingAddress.recipient_name}</strong> | ${shippingAddress.phone}`;
            document.getElementById('modalShippingAddress').textContent = shippingAddress.address;
            document.getElementById('modalShippingCityProvince').textContent = `${shippingAddress.city}, ${shippingAddress.province}`;
            document.getElementById('modalShippingCountryPostal').textContent = `${shippingAddress.country} ${shippingAddress.postal_code}`;

            // Populate Order Items
            const modalOrderItems = document.getElementById('modalOrderItems');
            modalOrderItems.innerHTML = ''; // Clear previous items
            orderData.items.forEach(item => {
                const itemHtml = `
                    <div class="list-group-item d-flex align-items-center py-2">
                        <img src="${item.image}" alt="${item.product_name}" class="me-3" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                        <div class="flex-grow-1">
                            <h6 class="mb-0">${item.product_name}</h6>
                            <small class="text-muted">${item.quantity} x ${item.price}</small>
                        </div>
                    </div>
                `;
                modalOrderItems.insertAdjacentHTML('beforeend', itemHtml);
            });

            // Populate Total Amount
            document.getElementById('modalTotalAmount').textContent = orderData.total_amount;
        });
    }
});
</script>
@endpush
