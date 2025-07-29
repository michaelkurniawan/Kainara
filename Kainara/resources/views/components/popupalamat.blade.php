@props(['userAddresses', 'selectedAddressId'])

@push('styles')
    <style>
        /* CSS ini spesifik untuk modal, dipindahkan ke sini */
        .modal-backdrop {
            background-color: #2c2c2c;
            opacity: 0.9 !important;
        }

        #addressSelectionModal .modal-content {
            background-color: #FFFFFF;
            border: 1px solid #333;
            overflow: hidden; /* Penting untuk memotong konten yang melebihi */
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            border-radius: 0;
            /* Tambahkan max-height untuk konten modal keseluruhan jika diperlukan,
               tapi biasanya cukup di modal-body agar header/footer tetap terlihat */
        }

        #addressSelectionModal .modal-header {
            background-color: #FFFFFF;
            border-bottom: 1px solid #ddd;
            padding: 0.75rem 2rem;
            border-radius: 0;
            flex-shrink: 0; /* Pastikan header tidak menyusut */
        }
        #addressSelectionModal .modal-header h5 {
            font-family: 'AncizarSerif', serif;
            font-weight: bold;
            font-size: 2.5rem;
            color: #333;
        }
        #addressSelectionModal .modal-header .btn-close {
            filter: invert(0%);
        }


        #addressSelectionModal .modal-body {
            padding: 1.5rem 2rem;
            color: #333;
            max-height: 37vh; /* KUNCI: Atur tinggi maksimal modal body */
            overflow-y: auto; /* KUNCI: Aktifkan scrollbar vertikal */
            border-radius: 0;
            flex-grow: 1; /* Biarkan modal-body mengisi ruang yang tersedia */
        }
        #addressSelectionModal .modal-body::-webkit-scrollbar {
            width: 8px;
        }
        #addressSelectionModal .modal-body::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        #addressSelectionModal .modal-body::-webkit-scrollbar-thumb {
            background: #888;
        }
        #addressSelectionModal .modal-body::-webkit-scrollbar-thumb:hover {
            background: #555;
        }


        #addressSelectionModal .modal-body .address-item {
            border: 1px solid #ccc;
            padding: 1.25rem 1.5rem;
            margin-bottom: 1rem;
            background-color: #FFFFFF;
            cursor: pointer;
            transition: border-color 0.2s, box-shadow 0.2s, background-color 0.2s;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            border-radius: 0;
            display: flex; /* Tambahkan ini jika belum ada untuk memastikan item flex */
            justify-content: space-between;
            align-items: center; /* Pusatkan item di dalam address-item */
        }
        #addressSelectionModal .modal-body .address-item:hover {
            background-color: #f8f8f8;
        }
        #addressSelectionModal .modal-body .address-item.selected-address {
            border-color: #B6B09F;
            background-color: #EAE4D5;
            box-shadow: 0 0 0 0.15rem rgba(182, 176, 159, 0.5);
        }
        #addressSelectionModal .modal-body .address-item h6 {
            font-size: 1.1rem;
            font-weight: bold;
            margin-bottom: 0;
            color: #333;
        }
        #addressSelectionModal .modal-body .address-item .address-name-phone {
            font-size: 0.85rem;
            color: #555;
            margin-bottom: 0.5rem;
        }
        #addressSelectionModal .modal-body .address-item p {
            font-size: 0.85rem;
            line-height: 1.4;
            color: #555;
        }
        #addressSelectionModal .modal-body .address-item .address-details {
            flex-grow: 1; /* Biarkan detail alamat mengisi ruang */
        }
        #addressSelectionModal .modal-body .address-item .address-actions {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            font-size: 0.9rem;
            align-items: flex-end;
            white-space: nowrap;
        }
        #addressSelectionModal .modal-body .address-item .address-actions .action-links {
            display: flex;
            gap: 0.75rem;
        }
        #addressSelectionModal .modal-body .address-item .address-actions a {
            color: #666;
            text-decoration: underline;
            padding: 0;
            border-radius: 0;
            transition: color 0.2s;
        }
        #addressSelectionModal .modal-body .address-item .address-actions a:hover {
            color: #333;
            background-color: transparent;
        }
        .primary-address-tag {
            background-color: transparent; /* Tetap transparan */
            color: #000;
            border-radius: 0.75rem;
            font-size: 0.8rem;
            font-weight: bold;
        }

        .add-new-address-btn {
            width: 30%;
            padding: 0.7rem;
            border: 1px dashed #999;
            background-color: #FFFFFF;
            color: #666;
            margin-top: 1rem;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            cursor: pointer;
            transition: background-color 0.2s, border-color 0.2s;
            border-radius: 0;
        }
        .add-new-address-btn:hover {
            background-color: #e0e0e0;
            border-color: #666;
        }

        .modal-footer {
            border-top: 1px solid #ddd;
            padding: 0.75rem 2rem;
            display: flex;
            justify-content: flex-end;
            border-radius: 0;
            background-color: #FFFFFF;
            gap: 1rem;
            flex-shrink: 0; /* Pastikan footer tidak menyusut */
        }
        .modal-footer .btn-confirm {
            background-color: #B6B09F;
            color: #fff;
            padding: 0.5rem 1.5rem;
            font-size: 1rem;
            font-weight: normal;
            transition: background-color 0.3s ease;
            border: none;
            border-radius: 0;
        }
        .modal-footer .btn-confirm:hover {
            background-color: #A09A87;
        }
        .modal-footer .btn-cancel {
            background-color: transparent;
            border: 1px solid #ccc;
            color: #333;
            padding: 0.5rem 1.5rem;
            font-size: 1rem;
            font-weight: normal;
            transition: background-color 0.3s ease, border-color 0.3s ease, color 0.3s ease;
            border-radius: 0;
        }
        .modal-footer .btn-cancel:hover {
            background-color: #f0f0f0;
            border-color: #999;
            color: #000;
        }
    </style>
@endpush

<div class="modal fade" id="addressSelectionModal" tabindex="-1" aria-labelledby="addressSelectionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fs-2" id="addressSelectionModalLabel">Addresses</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @forelse ($userAddresses as $userAddress)
                    <div class="address-item d-flex justify-content-between align-items-center mb-3 {{ ($userAddress->id == $selectedAddressId) ? 'selected-address' : '' }}" data-address-id="{{ $userAddress->id }}">
                        <div class="address-details">
                            <h6 class="mb-0">{{ $userAddress->label ?? 'Alamat' }}</h6>
                            <p class="address-name-phone fw-bold mb-0">{{ $userAddress->recipient_name ?? '' }} | {{ $userAddress->phone ?? '' }}</p>
                            <p class="text-muted mb-0" data-address-line="address">{{ $userAddress->address ?? '' }}{{ $userAddress->sub_district ? ', ' . $userAddress->sub_district : '' }}</p>
                            <p class="text-muted mb-0" data-address-line="city-province">{{ $userAddress->city ?? '' }}{{ ($userAddress->city && $userAddress->province) ? ', ' : '' }}{{ $userAddress->province ?? '' }}</p>
                            <p class="text-muted mb-0" data-address-line="country-postal">{{ $userAddress->country ?? '' }} {{ $userAddress->postal_code ?? '' }}</p>
                        </div>
                        <div class="address-actions">
                            <div class="action-links">
                                <a href="#" class="text-decoration-underline" data-bs-toggle="modal" data-bs-target="#editAddressModal" data-address-id="{{ $userAddress->id }}">Edit</a> |
                                <a href="#" class="text-decoration-underline trigger-delete-address-notification" data-address-id="{{ $userAddress->id }}" data-address-label="{{ $userAddress->label ?? 'alamat ini' }}">Delete</a>
                            </div>
                            @if ($userAddress->is_default)
                                <span class="primary-address-tag">Primary Address</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-center text-muted">No addresses found. Please add a new address.</p>
                @endforelse

                <button type="button" class="add-new-address-btn" data-bs-toggle="modal" data-bs-target="#addAddressModal">
                    <i class="bi bi-plus-circle"></i> Add New Address
                </button>
            </div>
            <div class="modal-footer justify-content-end">
                <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-confirm" id="confirmAddressSelection">Confirm</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const addressSelectionModal = document.getElementById('addressSelectionModal');
        const confirmAddressSelectionBtn = document.getElementById('confirmAddressSelection');
        const userAddressesData = @json($userAddresses->toArray());

        let currentSelectedAddressId = {{ Js::from($selectedAddressId) }};

        function handleAddressItemClick(event) {
            const clickedItem = event.currentTarget;

            document.querySelectorAll('#addressSelectionModal .address-item').forEach(item => {
                item.classList.remove('selected-address');
            });

            clickedItem.classList.add('selected-address');
            currentSelectedAddressId = clickedItem.dataset.addressId;
            console.log('Selected Address ID in modal:', currentSelectedAddressId);
        }

        addressSelectionModal.addEventListener('show.bs.modal', function () {
            document.querySelectorAll('#addressSelectionModal .address-item').forEach(item => {
                item.removeEventListener('click', handleAddressItemClick);
                item.addEventListener('click', handleAddressItemClick);

                if (item.dataset.addressId == currentSelectedAddressId) {
                    item.classList.add('selected-address');
                } else {
                    item.classList.remove('selected-address');
                }
            });

            const selectedInModal = document.querySelector('#addressSelectionModal .address-item.selected-address');
            if (!selectedInModal && userAddressesData.length > 0) {
                const defaultSelect = userAddressesData.find(addr => addr.is_default) || userAddressesData[0];
                if (defaultSelect) {
                    document.querySelector(`.address-item[data-address-id="${defaultSelect.id}"]`)?.classList.add('selected-address');
                    currentSelectedAddressId = defaultSelect.id;
                }
            }

            document.querySelectorAll('#addressSelectionModal .address-actions .action-links a').forEach(link => {
                if (link.classList.contains('trigger-delete-address-notification')) {
                    link.removeEventListener('click', handleDeleteLinkClick);
                    link.addEventListener('click', handleDeleteLinkClick);
                }
                // Pastikan link edit juga menghentikan default behavior jika perlu
                if (link.getAttribute('data-bs-toggle') === 'modal' && link.getAttribute('data-bs-target') === '#editAddressModal') {
                    link.removeEventListener('click', handleEditLinkClick); // Hapus listener lama jika ada
                    link.addEventListener('click', handleEditLinkClick); // Tambahkan listener baru
                }
            });
        });


        if (confirmAddressSelectionBtn) {
            confirmAddressSelectionBtn.addEventListener('click', function() {
                if (currentSelectedAddressId) {
                    const selectedAddressData = userAddressesData.find(addr => addr.id == currentSelectedAddressId);

                    if (selectedAddressData) {
                        const event = new CustomEvent('addressSelected', {
                            detail: {
                                addressId: currentSelectedAddressId,
                                addressData: selectedAddressData
                            }
                        });
                        window.dispatchEvent(event);

                        const bsModal = bootstrap.Modal.getInstance(addressSelectionModal);
                        if (bsModal) {
                            bsModal.hide();
                        }
                    }
                } else {
                    alert('Please select an address or add a new one.');
                }
            });
        }

        const addNewAddressBtn = document.querySelector('#addressSelectionModal .add-new-address-btn');
        if (addNewAddressBtn) {
            addNewAddressBtn.addEventListener('click', function() {
                const addressSelectionModalInstance = bootstrap.Modal.getInstance(addressSelectionModal);
                if (addressSelectionModalInstance) {
                    addressSelectionModalInstance.hide();
                }

                const addAddressModal = new bootstrap.Modal(document.getElementById('addAddressModal'));
                addAddressModal.show();
            });
        }

        function handleDeleteLinkClick(event) {
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
        }

        // New function to handle edit link click to hide addressSelectionModal first
        function handleEditLinkClick(event) {
            event.preventDefault(); // Prevent default link behavior (opening modal directly)

            const addressSelectionModalInstance = bootstrap.Modal.getInstance(addressSelectionModal);
            if (addressSelectionModalInstance) {
                addressSelectionModalInstance.hide(); // Hide this modal first
            }

            // Get the target modal ID from data-bs-target attribute
            const targetModalId = this.getAttribute('data-bs-target');
            const targetAddressId = this.getAttribute('data-address-id');

            // Find the target modal element and manually show it
            const editAddressModalElement = document.querySelector(targetModalId);
            if (editAddressModalElement) {
                const editAddressModal = new bootstrap.Modal(editAddressModalElement);
                // Dispatch event to populate the edit modal BEFORE showing it
                const editEvent = new CustomEvent('editAddressModalShow', {
                    detail: { addressId: targetAddressId }
                });
                window.dispatchEvent(editEvent); // Assuming edit-address-modal.blade.php listens to this
                editAddressModal.show();
            }
        }

        // You might need to add a listener in your edit-address-modal.blade.php
        // similar to what you have for 'addressSelected' in checkout.blade.php
        // For example, in edit-address-modal.blade.php's script section:
        /*
        document.addEventListener('DOMContentLoaded', function() {
            const editAddressModal = document.getElementById('editAddressModal');
            editAddressModal.addEventListener('editAddressModalShow', function(event) {
                const addressId = event.detail.addressId;
                // Fetch or use pre-loaded data to populate the form fields in this modal
                // Example: const address = allUserAddresses.find(addr => addr.id == addressId);
                // Then set form.value for each field
            });
        });
        */
    });
</script>
@endpush