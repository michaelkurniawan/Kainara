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
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            border-radius: 0;
        }

        #addressSelectionModal .modal-header {
            background-color: #FFFFFF;
            border-bottom: 1px solid #ddd;
            padding: 0.75rem 2rem;
            border-radius: 0;
            flex-shrink: 0;
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
            max-height: 37vh;
            overflow-y: auto;
            border-radius: 0;
            flex-grow: 1;
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
            display: flex;
            justify-content: space-between;
            align-items: center;
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
            flex-grow: 1;
        }

        /* --- START: Kunci perubahan di sini --- */
        #addressSelectionModal .modal-body .address-item .address-actions {
            display: flex;
            flex-direction: column; /* Keep column for primary address tag below actions */
            gap: 0.5rem;
            font-size: 0.9rem;
            align-items: flex-end; /* Align items to the right */
            white-space: nowrap;
        }

        #addressSelectionModal .modal-body .address-item .address-actions .action-links {
            display: flex; /* Already set, but good to confirm */
            align-items: center; /* Vertically align items like text and buttons */
            gap: 0.75rem;
            /* No need for flex-direction: row; as it's default for flex containers */
        }

        /* Style for the separator if you choose to keep it */
        #addressSelectionModal .modal-body .address-item .address-actions .action-links > span {
            color: #ccc; /* Or a darker shade if you prefer */
            margin: 0 -0.25rem; /* Adjust to bring elements closer if needed */
        }
        /* --- END: Kunci perubahan di sini --- */

        .btn-address-action-modal {
            background-color: transparent;
            color: #AD9D6D;
            border: none;
            padding: 0.25rem 0.5rem;
            font-size: 0.9rem;
            font-weight: 500;
            border-radius: 0.25rem;
            transition: background-color 0.2s ease, color 0.2s ease;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .btn-address-action-modal:hover {
            background-color: rgba(173, 157, 109, 0.1);
            color: #AD9D6D;
            text-decoration: none;
        }

        .primary-address-tag {
            background-color: transparent;
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
            flex-shrink: 0;
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
                                {{-- Changed to button with new class and data attributes for edit modal --}}
                                <button type="button" class="btn btn-sm btn-address-action-modal edit-address-from-checkout" data-address-id="{{ $userAddress->id }}">
                                    Edit
                                </button>
                                <span>|</span> {{-- Moved the | separator into a span for better control --}}
                                {{-- Changed to button with new class and added data attributes for JS confirmation --}}
                                {{-- This form is for the actual DELETE request --}}
                                <form action="{{ route('addresses.destroy', $userAddress->id) }}" method="POST" class="d-inline" id="delete-address-form-{{ $userAddress->id }}-checkout">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button"
                                            class="btn btn-sm btn-address-action-modal trigger-delete-address-notification-checkout"
                                            data-address-id="{{ $userAddress->id }}"
                                            data-address-label="{{ $userAddress->label ?? 'alamat ini' }}">
                                        Delete
                                    </button>
                                </form>
                            </div>
                            @if ($userAddress->is_default)
                                <span class="primary-address-tag">Primary Address</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-center text-muted">No addresses found. Please add a new address.</p>
                @endforelse

                <button type="button" class="add-new-address-btn" id="addNewAddressFromCheckout">
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
        const addAddressModal = document.getElementById('addAddressModal'); // Get the add address modal element
        const editAddressModal = document.getElementById('editAddressModal'); // Get the edit address modal element

        // Use the prop directly for userAddressesData, ensuring it's always an array
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
                // Remove existing click listeners to prevent duplicates
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

            // Re-attach listeners for Edit/Delete buttons each time modal is shown
            document.querySelectorAll('#addressSelectionModal .edit-address-from-checkout').forEach(button => {
                button.removeEventListener('click', handleEditButtonClick);
                button.addEventListener('click', handleEditButtonClick);
            });

            document.querySelectorAll('#addressSelectionModal .trigger-delete-address-notification-checkout').forEach(button => {
                button.removeEventListener('click', handleDeleteButtonClick);
                button.addEventListener('click', handleDeleteButtonClick);
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

        const addNewAddressBtn = document.getElementById('addNewAddressFromCheckout');
        if (addNewAddressBtn) {
            addNewAddressBtn.addEventListener('click', function() {
                const addressSelectionModalInstance = bootstrap.Modal.getInstance(addressSelectionModal);
                if (addressSelectionModalInstance) {
                    // Hide the first modal completely before opening the second
                    addressSelectionModalInstance.hide();
                }

                // Set the hidden input to indicate the request is from checkout
                const addFromCheckoutInput = document.getElementById('add_from_checkout');
                if (addFromCheckoutInput) {
                    addFromCheckoutInput.value = '1';
                }

                // Show the add address modal
                const addAddressModalInstance = new bootstrap.Modal(addAddressModal);
                addAddressModalInstance.show();
            });
        }

        // --- Functions for Edit and Delete buttons within the modal ---

        function handleDeleteButtonClick(event) {
            event.preventDefault(); // Prevent default button behavior (e.g., form submission)

            const addressId = this.dataset.addressId;
            const addressLabel = this.dataset.addressLabel;
            const targetForm = document.getElementById(`delete-address-form-${addressId}-checkout`);

            // Get instance of the address selection modal
            const addressSelectionModalInstance = bootstrap.Modal.getInstance(addressSelectionModal);

            // Hide the address selection modal immediately BEFORE showing the confirmation
            if (addressSelectionModalInstance) {
                addressSelectionModalInstance.hide();
            }

            if (typeof window.showNotificationCard === 'function') {
                window.showNotificationCard({
                    type: 'confirmation',
                    title: 'Confirm Address Deletion',
                    message: `Are you sure you want to delete "${addressLabel}"? This action cannot be undone.`,
                    hasActions: true,
                    onConfirm: () => {
                        // THIS IS THE KEY PART: Submit the form here
                        if (targetForm) {
                            // Add hidden input to form to indicate origin from checkout
                            const hiddenInput = document.createElement('input');
                            hiddenInput.type = 'hidden';
                            hiddenInput.name = 'from_checkout';
                            hiddenInput.value = '1';
                            targetForm.appendChild(hiddenInput);

                            targetForm.submit(); // <--- Submit the form!
                        }
                    },
                    onCancel: () => {
                        // If canceled, re-open the address selection modal
                        if (addressSelectionModalInstance) {
                            addressSelectionModalInstance.show();
                        }
                    }
                });
            } else {
                // Fallback for native confirm (less ideal, but functional)
                console.warn('window.showNotificationCard function not found. Falling back to native confirm.');
                if (confirm(`Are you sure you want to delete "${addressLabel}"?`)) {
                    if (targetForm) {
                        // Add hidden input to form
                        const hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = 'from_checkout';
                        hiddenInput.value = '1';
                        targetForm.appendChild(hiddenInput);
                        targetForm.submit(); // <--- Submit the form!
                    }
                } else {
                    // If canceled, re-open the address selection modal
                    if (addressSelectionModalInstance) {
                        addressSelectionModalInstance.show();
                    }
                }
            }
        }

        function handleEditButtonClick(event) {
            event.preventDefault(); // Prevent default button behavior

            const addressId = this.dataset.addressId;
            const addressSelectionModalInstance = bootstrap.Modal.getInstance(addressSelectionModal);
            if (addressSelectionModalInstance) {
                addressSelectionModalInstance.hide(); // Hide the selection modal
            }

            if (editAddressModal) {
                const editAddressModalInstance = new bootstrap.Modal(editAddressModal);

                // Find the address data in the pre-loaded array
                const address = userAddressesData.find(addr => addr.id == addressId);
                if (address) {
                    const editAddressForm = document.getElementById('editAddressForm');
                    editAddressForm.action = `/addresses/${address.id}`;
                    document.getElementById('edit_address_label').value = address.label || '';
                    document.getElementById('edit_recipient_name').value = address.recipient_name || '';
                    document.getElementById('edit_phone').value = address.phone || '';
                    document.getElementById('edit_address_full').value = address.address || '';
                    document.getElementById('edit_country').value = address.country || '';
                    document.getElementById('edit_city').value = address.city || '';
                    document.getElementById('edit_province').value = address.province || '';
                    document.getElementById('edit_postal_code').value = address.postal_code || '';
                    document.getElementById('edit_is_default').checked = address.is_default;

                    // Set the hidden input to indicate the request is from checkout
                    const editFromCheckoutInput = document.getElementById('edit_from_checkout');
                    if (editFromCheckoutInput) {
                        editFromCheckoutInput.value = '1';
                    }

                } else {
                    console.error('Address data not found for ID:', addressId);
                    alert('Failed to load address data for editing. Please try again.');
                    // If data not found, don't show the modal, or hide it if it somehow appeared
                    editAddressModalInstance.hide();
                    return; // Exit the function
                }

                editAddressModalInstance.show(); // Show the edit address modal
            }
        }

        // Listener for when 'addAddressModal' closes after adding a new address
        // This will re-open the address selection modal
        if (addAddressModal) {
            addAddressModal.addEventListener('hidden.bs.modal', function () {
                // Reset the hidden input value when the modal is closed
                const addFromCheckoutInput = document.getElementById('add_from_checkout');
                if (addFromCheckoutInput) {
                    addFromCheckoutInput.value = '0';
                }
                const addressSelectionModalInstance = new bootstrap.Modal(addressSelectionModal);
                addressSelectionModalInstance.show();
            });
        }

        // Listener for when 'editAddressModal' closes after editing an address
        // This will re-open the address selection modal
        if (editAddressModal) {
            editAddressModal.addEventListener('hidden.bs.modal', function () {
                // Reset the hidden input value when the modal is closed
                const editFromCheckoutInput = document.getElementById('edit_from_checkout');
                if (editFromCheckoutInput) {
                    editFromCheckoutInput.value = '0';
                }
                const addressSelectionModalInstance = new bootstrap.Modal(addressSelectionModal);
                addressSelectionModalInstance.show();
            });
        }
    });
</script>
@endpush