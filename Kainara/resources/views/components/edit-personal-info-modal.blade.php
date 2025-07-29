{{-- resources/views/components/edit-personal-info-modal.blade.php --}}

<div class="modal fade" id="editPersonalInfoModal" tabindex="-1" aria-labelledby="editPersonalInfoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-serif-medium" id="editPersonalInfoModalLabel">Edit Personal Information</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editPersonalInfoForm" method="POST" action="{{ route('profile.update_personal_info') }}">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_personal_first_name" class="form-label font-serif-regular">First Name</label>
                                <input type="text" class="form-control" id="edit_personal_first_name" name="first_name" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit_personal_last_name" class="form-label font-serif-regular">Last Name</label>
                                <input type="text" class="form-control" id="edit_personal_last_name" name="last_name" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit_personal_dob" class="form-label font-serif-regular">Date of Birth</label>
                                <input type="date" class="form-control" id="edit_personal_dob" name="dob">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_personal_email" class="form-label font-serif-regular">Email</label>
                                <input type="email" class="form-control" id="edit_personal_email" name="email" required>
                            </div>
                            {{-- Add more fields here if needed, e.g., phone number --}}
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