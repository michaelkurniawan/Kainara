<div class="modal fade" id="addAddressModal" tabindex="-1" aria-labelledby="addAddressModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-serif-medium" id="addAddressModalLabel">Add New Address</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('addresses.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
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

                        <div class="col-md-6">
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
                            <div class="form-check form-switch mb-3 mt-4">
                                {{-- Hidden input for unchecked state --}}
                                <input type="hidden" name="is_default" value="0">
                                <input class="form-check-input" type="checkbox" role="switch" id="is_default" name="is_default" value="1" {{ old('is_default') ? 'checked' : '' }}>
                                <label class="form-check-label font-serif-regular" for="is_default">
                                    Set as Default Address
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary font-serif-regular" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary font-serif-medium" style="background-color: #B39C59; border-color: #AD9D6D;">Save Address</button>
                </div>
            </form>
        </div>
    </div>
</div>