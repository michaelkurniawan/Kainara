<div class="modal fade" id="editAddressModal" tabindex="-1" aria-labelledby="editAddressModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fs-2" id="editAddressModalLabel">Edit Address</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editAddressForm" method="POST"> {{-- Action will be set by JS --}}
                    @csrf
                    @method('PUT') {{-- For update operations --}}
                    <input type="hidden" id="editAddressId" name="id">

                    <div class="mb-3">
                        <label for="editAddressType" class="form-label">Address Type (e.g., Home, Work)</label>
                        <input type="text" class="form-control" id="editAddressType" name="type" placeholder="Rumah" required>
                        <div class="invalid-feedback" id="editAddressType-error"></div>
                    </div>
                    <div class="mb-3">
                        <label for="editAddressName" class="form-label">Recipient Name</label>
                        <input type="text" class="form-control" id="editAddressName" name="name" placeholder="Nama Penerima" required>
                        <div class="invalid-feedback" id="editAddressName-error"></div>
                    </div>
                    <div class="mb-3">
                        <label for="editAddressPhone" class="form-label">Phone Number</label>
                        <input type="tel" class="form-control" id="editAddressPhone" name="phone" placeholder="081234567890" required>
                        <div class="invalid-feedback" id="editAddressPhone-error"></div>
                    </div>
                    <div class="mb-3">
                        <label for="editAddressStreet" class="form-label">Street Address</label>
                        <input type="text" class="form-control" id="editAddressStreet" name="street" placeholder="Nama Jalan, Nomor Rumah" required>
                        <div class="invalid-feedback" id="editAddressStreet-error"></div>
                    </div>
                    <div class="mb-3">
                        <label for="editAddressSubDistrict" class="form-label">Sub-District</label>
                        <input type="text" class="form-control" id="editAddressSubDistrict" name="sub_district" placeholder="Kelurahan/Kecamatan" required>
                        <div class="invalid-feedback" id="editAddressSubDistrict-error"></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="editAddressDistrict" class="form-label">District</label>
                            <input type="text" class="form-control" id="editAddressDistrict" name="district" placeholder="Kabupaten" required>
                            <div class="invalid-feedback" id="editAddressDistrict-error"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="editAddressCity" class="form-label">City</label>
                            <input type="text" class="form-control" id="editAddressCity" name="city" placeholder="Kota">
                            <div class="invalid-feedback" id="editAddressCity-error"></div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="editAddressProvince" class="form-label">Province</label>
                            <input type="text" class="form-control" id="editAddressProvince" name="province" placeholder="Provinsi" required>
                            <div class="invalid-feedback" id="editAddressProvince-error"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="editAddressPostalCode" class="form-label">Postal Code</label>
                            <input type="text" class="form-control" id="editAddressPostalCode" name="postal_code" placeholder="Kode Pos" required>
                            <div class="invalid-feedback" id="editAddressPostalCode-error"></div>
                        </div>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" value="1" id="editAddressIsPrimary" name="is_primary">
                        <label class="form-check-label" for="editAddressIsPrimary">
                            Set as Primary Address
                        </label>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-end">
                <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-confirm" id="updateAddressBtn">Save Changes</button>
            </div>
        </div>
    </div>
</div>