@extends('layouts.app')

@section('title', 'Profile')

@push('styles')
<style>
    :root {
        --font-primary: 'Ancizar Serif', serif;
        --font-secondary: 'Ancizar Serif', serif;
    }

    .profile-header-bg {
        background-image: url('{{ asset('images/BG/cloud bg.png') }}');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        height: 200px;
        position: relative;
        z-index: 1;
    }

    .profile-picture-wrapper {
        display: flex;
        justify-content: center;
        margin-top: -6vw;
        z-index: 10;
        position: relative;
    }

    .profile-picture-container {
        background-color: white;
        border-radius: 50%;
        padding: 5px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        position: relative;
        overflow: hidden;
        cursor: pointer;
    }

    .profile-picture {
        width: 12vw;
        height: 12vw;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #ddd;
        display: block;
    }

    .profile-picture-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s ease;
        z-index: 2;
    }

    .profile-picture-container:hover .profile-picture-overlay {
        opacity: 1;
    }

    .edit-icon-pencil {
        font-size: 1.8rem;
        color: white;
    }

    .profile-content-area {
        margin-top: 20px;
        margin-bottom: 20px;
        padding: 20px;
        text-align: center;
        width: 75vw;
    }

    .user-name {
        font-size: 40px;
        font-weight: bold;
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
        color: #AD9D6D;
        border-bottom-color: #AD9D6D;
        background-color: transparent;
    }

    .profile-tabs .nav-link:hover {
        color: #AD9D6D;
        border-bottom-color: #e9ecef;
    }

    .tab-content {
        background-color: #ffffff;
        border-radius: 8px;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.05);
        padding: 30px;
        margin-top: 30px;
        min-height: 50vh;
        overflow-y: auto;
        max-height: 70vh;
    }

    .tab-content::-webkit-scrollbar {
        width: 8px;
    }

    .tab-content::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    .tab-content::-webkit-scrollbar-thumb {
        background: #ccc;
        border-radius: 10px;
    }

    .tab-content::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }

    .profile-options {
        font-size: 1.2rem;
    }
</style>
@endpush

@section('content')
<div class="profile-header-bg">
</div>

<div class="profile-picture-wrapper">
    <div class="profile-picture-container">
        <form id="profile_picture_form" action="{{ route('profile.update_picture') }}" method="POST" enctype="multipart/form-data" class="d-none">
            @csrf
            <input type="file" id="profile_picture_input" name="profile_picture" accept="image/*" onchange="submitProfilePictureForm(event)">
        </form>

        <img id="profile_picture_preview" src="{{ asset($user->profile_picture ?? 'images/default-profile.png') }}" alt="Profile Picture" class="profile-picture">

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
        @include('components.profile.order-history-tab', ['userOrders' => $userOrdersHistory])
        @include('components.profile.addresses-tab', ['userAddresses' => $userAddresses])
        @include('components.profile.help-support-tab')
    </div>
</div>

@include('components.add-address-modal', ['user' => $user])
@include('components.edit-address-modal')
@include('components.edit-personal-info-modal', ['user' => $user])
@include('components.transaction-detail-modal')
@endsection

@push('scripts')
<script>
    function submitProfilePictureForm(event) {
        const reader = new FileReader();
        reader.onload = function() {
            const output = document.getElementById('profile_picture_preview');
            output.src = reader.result;
        }
        reader.readAsDataURL(event.target.files[0]);

        document.getElementById('profile_picture_form').submit();
    }

    document.addEventListener('DOMContentLoaded', function () {
        const allUserAddresses = @json($userAddresses->keyBy('id'));

        const editAddressModal = document.getElementById('editAddressModal');
        if (editAddressModal) {
            editAddressModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const addressId = button.getAttribute('data-address-id');

                const activeTab = document.querySelector('.profile-tabs .nav-link.active');
                if (activeTab) {
                    const activeTabId = activeTab.getAttribute('data-bs-target').substring(1);
                    localStorage.setItem('lastActiveProfileTab', activeTabId);
                }

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

        const editPersonalInfoModal = document.getElementById('editPersonalInfoModal');
        if (editPersonalInfoModal) {
            editPersonalInfoModal.addEventListener('show.bs.modal', function (event) {
                const activeTab = document.querySelector('.profile-tabs .nav-link.active');
                if (activeTab) {
                    const activeTabId = activeTab.getAttribute('data-bs-target').substring(1);
                    localStorage.setItem('lastActiveProfileTab', activeTabId);
                }

                const user = @json($user);

                document.getElementById('edit_personal_first_name').value = user.first_name || '';
                document.getElementById('edit_personal_last_name').value = user.last_name || '';
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

        const profileTab = document.getElementById('profileTab');
        if (profileTab) {
            const tabButtons = profileTab.querySelectorAll('.nav-link');

            const activateTab = (tabId) => {
                const targetTabButton = document.getElementById(tabId + '-tab');
                const targetTabContent = document.getElementById(tabId);

                if (targetTabButton && targetTabContent) {
                    tabButtons.forEach(link => {
                        link.classList.remove('active');
                        link.setAttribute('aria-selected', 'false');
                    });
                    document.querySelectorAll('.tab-pane').forEach(pane => {
                        pane.classList.remove('show', 'active');
                    });

                    targetTabButton.classList.add('active');
                    targetTabButton.setAttribute('aria-selected', 'true');

                    targetTabContent.classList.add('show', 'active');
                }
            };

            tabButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const activeTabId = this.getAttribute('data-bs-target').substring(1);
                    localStorage.setItem('lastActiveProfileTab', activeTabId);
                });
            });

            let initialTabId = null;

            if (window.location.hash) {
                const hashId = window.location.hash.substring(1);
                if (document.getElementById(hashId) && document.getElementById(hashId + '-tab')) {
                    initialTabId = hashId;
                }
            }

            if (!initialTabId) {
                const storedTabId = localStorage.getItem('lastActiveProfileTab');
                if (storedTabId && document.getElementById(storedTabId) && document.getElementById(storedTabId + '-tab')) {
                    initialTabId = storedTabId;
                }
            }

            if (!initialTabId) {
                initialTabId = 'personal-info';
            }

            activateTab(initialTabId);
        }
    });

    const deleteAddressButtons = document.querySelectorAll('.trigger-delete-address-notification');

    deleteAddressButtons.forEach(button => {
        button.addEventListener('click', function(event) {
            event.preventDefault();

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
                            const activeTab = document.querySelector('.profile-tabs .nav-link.active');
                            if (activeTab) {
                                const activeTabId = activeTab.getAttribute('data-bs-target').substring(1);
                                localStorage.setItem('lastActiveProfileTab', activeTabId);
                            }
                            targetForm.submit();
                        }
                    },
                    onCancel: () => {
                    }
                });
            } else {
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
                const button = event.relatedTarget;
                const orderData = JSON.parse(button.getAttribute('data-order'));

                document.getElementById('modalOrderId').textContent = orderData.id;
                document.getElementById('modalInvoice').textContent = orderData.invoice;
                document.getElementById('modalOrderDate').textContent = orderData.order_date;

                const modalOrderStatus = document.getElementById('modalOrderStatus');
                modalOrderStatus.textContent = orderData.status;
                modalOrderStatus.className = 'badge order-status';
                modalOrderStatus.classList.add(`status-${orderData.status.toLowerCase().replace(/\s/g, '-')}`);


                const shippingAddress = orderData.shipping_address;
                document.getElementById('modalShippingNamePhone').innerHTML = `<strong>${shippingAddress.recipient_name}</strong> | ${shippingAddress.phone}`;
                document.getElementById('modalShippingAddress').textContent = shippingAddress.address;
                document.getElementById('modalShippingCityProvince').textContent = `${shippingAddress.city}, ${shippingAddress.province}`;
                document.getElementById('modalShippingCountryPostal').textContent = `${shippingAddress.country} ${shippingAddress.postal_code}`;

                const modalOrderItems = document.getElementById('modalOrderItems');
                modalOrderItems.innerHTML = '';
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

                document.getElementById('modalTotalAmount').textContent = orderData.total_amount;
            });
        }
    });
</script>
@endpush