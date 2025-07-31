<div class="modal fade" id="editAddressModal" tabindex="-1" aria-labelledby="editAddressModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header-eam">
                <h5 class="modal-title-eam font-serif-medium" id="editAddressModalLabel">Edit Address</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editAddressForm" method="POST">
                @csrf
                @method('PUT')
                {{-- Add this hidden input to indicate origin --}}
                <input type="hidden" name="from_checkout" id="edit_from_checkout" value="0">
                <div class="modal-body-eam">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_address_label" class="form-label-eam font-serif-regular">Address Label (e.g., Home, Work)</label>
                                <input type="text" class="form-control-eam" id="edit_address_label" name="label" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit_recipient_name" class="form-label-eam font-serif-regular">Recipient Name</label>
                                <input type="text" class="form-control-eam" id="edit_recipient_name" name="recipient_name" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit_phone" class="form-label-eam font-serif-regular">Phone Number</label>
                                <input type="text" class="form-control-eam" id="edit_phone" name="phone" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit_address_full" class="form-label-eam font-serif-regular">Full Address</label>
                                <textarea class="form-control-eam" id="edit_address_full" name="address" rows="3" required></textarea>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_country" class="form-label-eam font-serif-regular">Country</label>
                                <input type="text" class="form-control-eam" id="edit_country" name="country" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit_city" class="form-label-eam font-serif-regular">City</label>
                                <input type="text" class="form-control-eam" id="edit_city" name="city" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit_province" class="form-label-eam font-serif-regular">Province</label>
                                <input type="text" class="form-control-eam" id="edit_province" name="province" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit_postal_code" class="form-label-eam font-serif-regular">Postal Code</label>
                                <input type="text" class="form-control-eam" id="edit_postal_code" name="postal_code" required>
                            </div>
                            <div class="form-check form-switch mb-3 mt-4">
                                <input type="hidden" name="is_default" value="0">
                                <input class="form-check-input" type="checkbox" role="switch" id="edit_is_default" name="is_default" value="1">
                                <label class="form-check-label font-serif-regular" for="edit_is_default">
                                    Set as Default Address
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer-eam d-flex justify-content-end align-items-center border-top-0 py-3 px-3 bg-white">
                    <button type="button" class="btn btn-outline-secondary font-serif-regular px-4 py-2 me-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary font-serif-medium px-4 py-2 btn-custom-gold me-3">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    /* Ensure these styles are consistent or linked globally */
    .modal-header-eam {
        padding: 1.5rem;
        background-color: #fff;
        border-bottom: 1px solid #dee2e6;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-title-eam {
        font-size: 1.5rem;
        color: #333;
    }

    .modal-body-eam {
        padding: 2rem;
        line-height: 1.6;
        background-color: #fff;
    }

    .modal-footer-eam {
        padding: 1rem 1.5rem;
        border-top: none;
        background-color: #fff;
        display: flex;
        justify-content: flex-end;
        gap: 0.5rem;
    }

    .btn-custom-gold {
        background-color: #B39C59;
        border-color: #AD9D6D;
        transition: all 0.2s ease-in-out;
        border-radius: 0 !important; /* Make Save Changes button square */
    }

    .btn-custom-gold:hover {
        background-color: #c9b071;
        border-color: #B39C59;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .btn-outline-secondary {
        border-color: #ced4da;
        color: #6c757d;
        transition: all 0.2s ease-in-out;
        border-radius: 0 !important; /* Make Cancel button square */
    }

    .btn-outline-secondary:hover {
        background-color: #e9ecef;
        border-color: #adb5bd;
        color: #495057;
    }
    .form-control-eam {
        display: block;
        width: 100%;
        padding: 0.5rem 1rem;
        font-size: 1rem;
        font-weight: 400;
        line-height: 1.5;
        color: #495057;
        background-color: #fff;
        background-clip: padding-box;
        border: 2px solid #ced4da;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }

    .form-control-eam:focus {
        color: #495057;
        background-color: #fff;
        border-color: #B39C59; /* Highlight with your custom gold color on focus */
        outline: 0;
        box-shadow: 0 0 0 0.25rem rgba(179, 156, 89, 0.25); /* Subtle glow matching the gold */
    }

    textarea.form-control-eam {
        min-height: calc(1.5em + 1.5rem + 2px); /* Adjust as needed for better default height */
        resize: vertical; /* Allow vertical resizing only */
    }
</style>