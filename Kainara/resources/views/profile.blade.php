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
        /* Assuming --color-brand is defined elsewhere if needed, otherwise remove */
        /* color: var(--color-brand); */
        /* border-bottom-color: var(--color-brand); */
        color: #AD9D6D; /* Example: Use the new gold color for active tab */
        border-bottom-color: #AD9D6D;
        background-color: transparent;
    }

    .profile-tabs .nav-link:hover {
        /* color: var(--color-brand); */
        /* border-bottom-color: #e9ecef; */
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

    /* Order History Styling */
    .order-card {
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        margin-bottom: 20px;
        padding: 15px;
        display: flex;
        align-items: flex-start;
        gap: 15px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
    }

    .order-item-image {
        width: 80px;
        height: 80px;
        background-color: #f0f0f0; /* Placeholder abu-abu */
        border-radius: 4px;
        flex-shrink: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 0.8rem;
        color: #888;
    }

    .order-details {
        flex-grow: 1;
        text-align: left;
    }

    .order-id {
        font-weight: bold;
        color: #333;
        margin-bottom: 5px;
    }

    .order-summary {
        font-size: 0.9rem;
        color: #666;
        margin-bottom: 5px;
    }

    .order-address {
        font-size: 0.85rem;
        color: #888;
    }

    .order-status {
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: bold;
        color: white;
        text-align: center;
        white-space: nowrap; /* Mencegah teks status pecah baris */
    }

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

    /* Styles for the address items based on the image */
    .address-item {
        border: 1px solid #e0e0e0; /* Light border */
        border-radius: 8px; /* Rounded corners */
        padding: 15px 20px; /* Padding inside the box */
        margin-bottom: 15px; /* Space between address items */
        background-color: #fcfbf8; /* Very light background for address cards */
        box-shadow: 0 2px 4px rgba(0,0,0,0.03); /* Subtle shadow */
    }

    /* Styles for default address tag */
    .primary-address-tag { /* Renamed from .primary-address-tag to reflect 'default' */
        background-color: #B39C59; /* Background for default tag */
        color: white;
        padding: 3px 8px;
        border-radius: 4px;
        font-size: 0.8rem;
        font-weight: bold;
        text-transform: uppercase;
    }

    /* Styles for selected address item border */
    .address-item.selected-address {
        border-color: #AD9D6D; /* Changed to direct hex code for consistency */
        box-shadow: 0 0 0 2px rgba(173, 157, 109, 0.3); /* Adjusted rgba based on #AD9D6D */
    }

    /* Styles for Edit/Delete action links */
    .address-actions .action-links a {
        color: #AD9D6D; /* Changed to direct hex code */
        font-size: 0.9rem;
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
        {{-- Gunakan id untuk pratinjau gambar --}}
        <img id="profile_picture_preview" src="{{ asset($user->profile_picture ?? 'images/default-profile.png') }}" alt="Profile Picture" class="profile-picture">

        {{-- Overlay dengan ikon pensil dan input file tersembunyi --}}
        <div class="profile-picture-overlay">
            <label for="profile_picture_input" class="d-flex align-items-center justify-content-center w-100 h-100 cursor-pointer">
                <i class="fas fa-pencil-alt edit-icon-pencil"></i>
                {{-- Input file tersembunyi. Form submission akan ditangani terpisah --}}
                <input type="file" id="profile_picture_input" name="profile_picture" accept="image/*" class="d-none" onchange="previewProfilePicture(event)">
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
            <div class="personal-info-title font-serif-medium tab-title">My Account</div>
            <hr class="mb-4"> {{-- Garis pemisah --}}

            <div class="personal-info-section">
                {{-- Kolom Kiri: Private Info --}}
                <div class="personal-info-column">
                    <h4 class="personal-info-subtitle">Private Info <i class="fas fa-pencil-alt"></i></h4>
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
            <h3>Order History</h3>
            {{-- Mock Order Data (sesuai gambar) --}}
            @php
                $orders = [
                    [
                        'id' => '123456789',
                        'items_count' => 3,
                        'total_price' => 'IDR 2.500.000',
                        'address' => 'Jl. Pakuan No.3, Sumur Batu, Kec. Babakan Madang, Kabupaten Bogor, Jawa Barat 16810',
                        'status' => 'Completed',
                        'image_placeholder' => 'Item 1'
                    ],
                    [
                        'id' => '987654321',
                        'items_count' => 1,
                        'total_price' => 'IDR 750.000',
                        'address' => 'Jl. Pakuan No.3, Sumur Batu, Kec. Babakan Madang, Kabupaten Bogor, Jawa Barat 16810',
                        'status' => 'Waiting for Payment',
                        'image_placeholder' => 'Item 2'
                    ],
                    [
                        'id' => '456789012',
                        'items_count' => 2,
                        'total_price' => 'IDR 1.200.000',
                        'address' => 'Jl. Pakuan No.3, Sumur Batu, Kec. Babakan Madang, Kabupaten Bogor, Jawa Barat 16810',
                        'status' => 'Cancelled',
                        'image_placeholder' => 'Item 3'
                    ],
                ];
            @endphp

            @forelse($orders as $order)
                <div class="order-card">
                    <div class="order-item-image">
                        {{ $order['image_placeholder'] }}
                    </div>
                    <div class="order-details">
                        <div class="order-id">Order ID : {{ $order['id'] }}</div>
                        <div class="order-summary">{{ $order['items_count'] }} Items</div>
                        <div class="order-summary">{{ $order['total_price'] }}</div>
                        <div class="order-address">{{ $order['address'] }}</div>
                    </div>
                    <div class="order-status status-{{ Str::slug($order['status']) }}">
                        {{ $order['status'] }}
                    </div>
                </div>
            @empty
                <p>No orders found.</p>
            @endforelse
        </div>

        {{-- Tab Pane: Addresses --}}
        <div class="tab-pane fade" id="addresses" role="tabpanel" aria-labelledby="addresses-tab">
            <div class="addresses-header d-flex justify-content-between align-items-center mb-4">
                <h3 class="personal-info-title mb-0 font-serif-medium">Addresses</h3>
                {{-- Changed <a> to <button> for modal trigger --}}
                <button type="button" class="btn btn-add-address font-serif-medium" data-bs-toggle="modal" data-bs-target="#addAddressModal">
                    <i class="fas fa-plus"></i> Add new address
                </button>
            </div>
            <hr class="mb-4">

            @forelse ($userAddresses as $userAddress)
                <div class="address-item d-flex justify-content-between align-items-start mb-3 {{ ($userAddress['id'] ?? null) == ($selectedAddressId ?? null) ? 'selected-address' : '' }}" data-address-id="{{ $userAddress['id'] ?? '' }}">
                    <div class="address-details">
                        {{-- Updated to 'label' and 'address' based on new schema --}}
                        <h6 class="mb-0 font-serif-semibold">{{ $userAddress['label'] ?? 'Alamat' }}</h6>
                        <p class="address-name-phone fw-bold mb-0 font-serif-medium">{{ $userAddress['recipient_name'] ?? '' }} | {{ $userAddress['phone'] ?? '' }}</p>
                        <p class="text-muted mb-0 font-serif-light-italic" data-address-line="address">{{ $userAddress['address'] ?? '' }}</p>
                        <p class="text-muted mb-0 font-serif-light-italic" data-address-line="city-province">{{ $userAddress['city'] ?? '' }}{{ ($userAddress['city'] && $userAddress['province']) ? ', ' : '' }}{{ $userAddress['province'] ?? '' }}</p>
                        <p class="text-muted mb-0 font-serif-light-italic" data-address-line="country-postal">{{ $userAddress['country'] ?? '' }} {{ $userAddress['postal_code'] ?? '' }}</p>
                    </div>
                    <div class="address-actions">
                        <div class="action-links font-serif-light-italic">
                            <a href="#" class="text-decoration-underline">Edit</a> | <a href="#" class="text-decoration-underline">Delete</a>
                        </div>
                        {{-- Updated to 'is_default' --}}
                        @if (($userAddress['is_default'] ?? false))
                            <span class="primary-address-tag font-serif-medium">Default Address</span>
                        @endif
                    </div>
                </div>
            @empty
                <p class="text-center text-muted font-serif-light-italic">No addresses found. Please add a new address.</p>
            @endforelse
        </div>

        {{-- Tab Pane: Help & Support --}}
        <div class="tab-pane fade" id="help-support" role="tabpanel" aria-labelledby="help-support-tab">
            <h3>Help & Support</h3>
            <p>For assistance, please contact our support team.</p>
            {{-- Informasi kontak atau FAQ di sini --}}
        </div>
    </div>
</div>

<div class="modal fade" id="addAddressModal" tabindex="-1" aria-labelledby="addAddressModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg"> {{-- Added modal-lg for wider modal --}}
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-serif-medium" id="addAddressModalLabel">Add New Address</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('addresses.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row"> {{-- Start of Bootstrap Row --}}
                        <div class="col-md-6"> {{-- First Column --}}
                            <div class="mb-3">
                                <label for="address_label" class="form-label font-serif-regular">Address Label (e.g., Home, Work)</label>
                                <input type="text" class="form-control" id="address_label" name="label" required>
                            </div>
                            <div class="mb-3">
                                <label for="recipient_name" class="form-label font-serif-regular">Recipient Name</label>
                                <input type="text" class="form-control" id="recipient_name" name="recipient_name" value="{{ old('recipient_name', $user->first_name . ' ' . $user->last_name) }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="phone" class="form-label font-serif-regular">Phone Number</label>
                                <input type="text" class="form-control" id="phone" name="phone" required>
                            </div>
                            <div class="mb-3">
                                <label for="address_full" class="form-label font-serif-regular">Full Address</label>
                                <textarea class="form-control" id="address_full" name="address" rows="3" required>{{ old('address') }}</textarea>
                            </div>
                        </div>

                        <div class="col-md-6"> {{-- Second Column --}}
                            <div class="mb-3">
                                <label for="country" class="form-label font-serif-regular">Country</label>
                                <input type="text" class="form-control" id="country" name="country" value="{{ old('country', 'Indonesia') }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="city" class="form-label font-serif-regular">City</label>
                                <input type="text" class="form-control" id="city" name="city" required>
                            </div>
                            <div class="mb-3">
                                <label for="province" class="form-label font-serif-regular">Province</label>
                                <input type="text" class="form-control" id="province" name="province" required>
                            </div>
                            <div class="mb-3">
                                <label for="postal_code" class="form-label font-serif-regular">Postal Code</label>
                                <input type="text" class="form-control" id="postal_code" name="postal_code" required>
                            </div>
                            {{-- MODIFIED: Changed checkbox to a switch --}}
                            <div class="form-check form-switch mb-3 mt-4">
                                <input class="form-check-input" type="checkbox" role="switch" id="is_default" name="is_default">
                                <label class="form-check-label font-serif-regular" for="is_default">
                                    Set as Default Address
                                </label>
                            </div>
                        </div>
                    </div> {{-- End of Bootstrap Row --}}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary font-serif-regular" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary font-serif-medium" style="background-color: #B39C59; border-color: #AD9D6D;">Save Address</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Fungsi untuk pratinjau gambar profil yang baru dipilih
    function previewProfilePicture(event) {
        const reader = new FileReader();
        reader.onload = function() {
            const output = document.getElementById('profile_picture_preview');
            output.src = reader.result;
        }
        reader.readAsDataURL(event.target.files[0]);

        // Optional: Anda bisa langsung submit form di sini jika ingin otomatis upload
        // document.getElementById('profile_picture_form').submit();
    }
</script>
@endpush