<div class="modal fade" id="addNewAddressModal" tabindex="-1" aria-labelledby="addNewAddressModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fs-2" id="addNewAddressModalLabel">Add New Address</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addNewAddressForm" action="{{ route('addresses.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="newAddressType" class="form-label">Address Type (e.g., Home, Work)</label>
                        <input type="text" class="form-control" id="newAddressType" name="type" placeholder="Rumah" required>
                        <div class="invalid-feedback" id="newAddressType-error"></div>
                    </div>
                    <div class="mb-3">
                        <label for="newAddressName" class="form-label">Recipient Name</label>
                        <input type="text" class="form-control" id="newAddressName" name="name" placeholder="Nama Penerima" required>
                        <div class="invalid-feedback" id="newAddressName-error"></div>
                    </div>
                    <div class="mb-3">
                        <label for="newAddressPhone" class="form-label">Phone Number</label>
                        <input type="tel" class="form-control" id="newAddressPhone" name="phone" placeholder="081234567890" required>
                        <div class="invalid-feedback" id="newAddressPhone-error"></div>
                    </div>
                    <div class="mb-3">
                        <label for="newAddressStreet" class="form-label">Street Address</label>
                        <input type="text" class="form-control" id="newAddressStreet" name="street" placeholder="Nama Jalan, Nomor Rumah" required>
                        <div class="invalid-feedback" id="newAddressStreet-error"></div>
                    </div>
                    <div class="mb-3">
                        <label for="newAddressSubDistrict" class="form-label">Sub-District</label>
                        <input type="text" class="form-control" id="newAddressSubDistrict" name="sub_district" placeholder="Kelurahan/Kecamatan" required>
                        <div class="invalid-feedback" id="newAddressSubDistrict-error"></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="newAddressDistrict" class="form-label">District</label>
                            <input type="text" class="form-control" id="newAddressDistrict" name="district" placeholder="Kabupaten" required>
                            <div class="invalid-feedback" id="newAddressDistrict-error"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="newAddressCity" class="form-label">City</label>
                            <input type="text" class="form-control" id="newAddressCity" name="city" placeholder="Kota">
                            <div class="invalid-feedback" id="newAddressCity-error"></div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="newAddressProvince" class="form-label">Province</label>
                            <input type="text" class="form-control" id="newAddressProvince" name="province" placeholder="Provinsi" required>
                            <div class="invalid-feedback" id="newAddressProvince-error"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="newAddressPostalCode" class="form-label">Postal Code</label>
                            <input type="text" class="form-control" id="newAddressPostalCode" name="postal_code" placeholder="Kode Pos" required>
                            <div class="invalid-feedback" id="newAddressPostalCode-error"></div>
                        </div>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" value="1" id="newAddressIsPrimary" name="is_primary">
                        <label class="form-check-label" for="newAddressIsPrimary">
                            Set as Primary Address
                        </label>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-end">
                <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-confirm" id="saveNewAddressBtn">Save Address</button>
            </div>
        </div>
    </div>
</div>