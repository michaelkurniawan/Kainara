@extends('layouts.app')

@section('title', 'Profile')

@push('styles')
<style>
    :root {
        --font-primary: 'Ancizar Serif', serif;
        --font-secondary: 'Ancizar Serif', serif;
    }
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

    .profile-options {
        font-size: 1.2rem; /* Ukuran font untuk tab nav-link */
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
        @include('components.profile.personal-information-tab', ['user' => $user])
        {{-- IMPORTANT: Pass the correct variable name to the partial --}}
        @include('components.profile.order-history-tab', ['userOrders' => $userOrdersHistory])
        @include('components.profile.addresses-tab', ['userAddresses' => $userAddresses])
        @include('components.profile.help-support-tab')
    </div>
</div>

{{-- Modals (already separate components) --}}
@include('components.add-address-modal', ['user' => $user])
@include('components.edit-address-modal')
@include('components.edit-personal-info-modal', ['user' => $user])
{{-- The transaction detail modal is now included here directly for consistency --}}
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

    // --- Transaction Detail Modal Logic for PROFILE page ---
    // This logic runs on profile.blade.php
    document.addEventListener('DOMContentLoaded', function () {
        const transactionDetailModal = document.getElementById('transactionDetailModal');
        if (transactionDetailModal) {
            transactionDetailModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget; // Button that triggered the modal
                // Get the order data directly from the data-order attribute of the button
                // Make sure the data-order attribute is present and correctly JSON encoded on the button
                const orderData = JSON.parse(button.getAttribute('data-order'));

                // Populate Order Information
                document.getElementById('modalOrderId').textContent = orderData.id;
                document.getElementById('modalInvoice').textContent = orderData.invoice;
                document.getElementById('modalOrderDate').textContent = orderData.order_date;

                const modalOrderStatus = document.getElementById('modalOrderStatus');
                modalOrderStatus.textContent = orderData.status;
                // Remove previous status classes and add the new one
                modalOrderStatus.className = 'badge order-status'; // Reset classes
                // Use slugged status for CSS class, ensure it matches your CSS
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
                if (orderData.items && orderData.items.length > 0) {
                    orderData.items.forEach(item => {
                        const itemHtml = `
                            <div class="list-group-item d-flex align-items-center py-2">
                                <img src="${item.image}" alt="${item.product_name}" class="me-3" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                                <div class="flex-grow-1">
                                    <h6 class="mb-0">${item.product_name}</h6>
                                    ${item.variant_size || item.variant_color ? `<small class="text-muted">Variant: ${item.variant_size ? item.variant_size : ''}${item.variant_size && item.variant_color ? ' / ' : ''}${item.variant_color ? item.variant_color : ''}</small><br>` : ''}
                                    <small class="text-muted">${item.quantity} x ${item.price}</small>
                                </div>
                                <div>
                                    <strong>${item.price.replace('IDR ', '')}</strong>
                                </div>
                            </div>
                        `;
                        modalOrderItems.insertAdjacentHTML('beforeend', itemHtml);
                    });
                } else {
                    modalOrderItems.innerHTML = '<div class="text-center py-4 text-muted">No items found for this order.</div>';
                }

                // Populate Total Amount
                document.getElementById('modalTotalAmount').textContent = orderData.total_amount;
            });
        }
    });
</script>
@endpush