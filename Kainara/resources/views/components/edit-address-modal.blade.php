{{-- resources/views/components/edit-address-modal.blade.php --}}

<div class="modal fade" id="editAddressModal" tabindex="-1" aria-labelledby="editAddressModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-serif-medium" id="editAddressModalLabel">Edit Address</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editAddressForm" method="POST">
                @csrf
                @method('PUT') {{-- Penting untuk metode PUT --}}
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_address_label" class="form-label font-serif-regular">Address Label (e.g., Home, Work)</label>
                                <input type="text" class="form-control" id="edit_address_label" name="label" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit_recipient_name" class="form-label font-serif-regular">Recipient Name</label>
                                <input type="text" class="form-control" id="edit_recipient_name" name="recipient_name" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit_phone" class="form-label font-serif-regular">Phone Number</label>
                                <input type="text" class="form-control" id="edit_phone" name="phone" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit_address_full" class="form-label font-serif-regular">Full Address</label>
                                <textarea class="form-control" id="edit_address_full" name="address" rows="3" required></textarea>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_country" class="form-label font-serif-regular">Country</label>
                                <input type="text" class="form-control" id="edit_country" name="country" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit_city" class="form-label font-serif-regular">City</label>
                                <input type="text" class="form-control" id="edit_city" name="city" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit_province" class="form-label font-serif-regular">Province</label>
                                <input type="text" class="form-control" id="edit_province" name="province" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit_postal_code" class="form-label font-serif-regular">Postal Code</label>
                                <input type="text" class="form-control" id="edit_postal_code" name="postal_code" required>
                            </div>
                            <div class="form-check form-switch mb-3 mt-4">
                                {{-- Hidden input for unchecked state --}}
                                <input type="hidden" name="is_default" value="0">
                                <input class="form-check-input" type="checkbox" role="switch" id="edit_is_default" name="is_default" value="1">
                                <label class="form-check-label font-serif-regular" for="edit_is_default">
                                    Set as Default Address
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary font-serif-regular" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary font-serif-medium" style="background-color: #B39C59; border-color: #AD9D6D;">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>