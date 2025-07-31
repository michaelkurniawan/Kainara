<div class="modal fade" id="editPersonalInfoModal" tabindex="-1" aria-labelledby="editPersonalInfoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content shadow-lg">
            <div class="modal-header-epi border-bottom-0 p-4">
                <h3 class="modal-title-epi font-serif-bold fs-4 text-dark" id="editPersonalInfoModalLabel">Edit Personal Information</h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editPersonalInfoForm" method="POST" action="{{ route('profile.update_personal_info') }}">
                @csrf
                @method('PUT')
                <div class="modal-body-epi py-3 px-4 bg-white">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_personal_first_name" class="form-label font-serif-regular text-dark mb-1">First Name</label>
                                <input type="text" class="form-control form-control-lg border-light focus-ring" id="edit_personal_first_name" name="first_name" required placeholder="Enter your first name">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_personal_last_name" class="form-label font-serif-regular text-dark mb-1">Last Name</label>
                                <input type="text" class="form-control form-control-lg border-light focus-ring" id="edit_personal_last_name" name="last_name" required placeholder="Enter your last name">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_personal_dob" class="form-label font-serif-regular text-dark mb-1">Date of Birth</label>
                                <input type="date" class="form-control form-control-lg border-light focus-ring" id="edit_personal_dob" name="dob">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer-epi d-flex justify-content-end align-items-center border-top-0 py-3 px-3 bg-white">
                    <button type="button" class="btn btn-outline-secondary font-serif-regular px-4 py-2 me-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary font-serif-medium px-4 py-2 btn-custom-gold me-2">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .modal-content-epi {
        border: none;
        border-radius: 0 !important;
    }

    .modal-header-epi,
    .modal-body-epi,
    .modal-footer-epi {
        background-color: #fff !important;
        border-radius: 0 !important;
    }

    .text-dark {
        color: #212529 !important;
    }

    .form-control-lg {
        padding: 0.75rem 1rem;
    }

    .border-light {
        border-color: #dee2e6 !important;
    }

    .focus-ring:focus {
        box-shadow: 0 0 0 0.25rem rgba(179, 156, 89, 0.25);
        border-color: #B39C59;
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

    .modal-header-epi {
        border-bottom: 1px solid #dee2e6 !important;
        padding-bottom: 1rem !important;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
</style>