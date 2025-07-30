<div class="tab-pane fade show active" id="personal-info" role="tabpanel" aria-labelledby="personal-info-tab">
    <div class="addresses-header d-flex justify-content-between align-items-center mb-4">
        <h3 class="personal-info-title mb-0 font-serif-medium">My Account</h3>
    </div>
    <hr class="mb-4"> 

    <div class="personal-info-section">
        <div class="personal-info-column">
            <h4 class="personal-info-subtitle">
                Private Info
                <button type="button" class="btn btn-sm btn-address-action" data-bs-toggle="modal" data-bs-target="#editPersonalInfoModal">
                    <i class="fas fa-pencil-alt"></i>
                </button>
            </h4>
            <div class="personal-info-item">
                <span class="personal-info-label font-serif-medium">First Name</span>
                <p class="personal-info-value font-serif-medium">{{ $user->first_name }}</p>
            </div>
            <div class="personal-info-item">
                <span class="personal-info-label font-serif-medium">Last Name</span>
                <p class="personal-info-value font-serif-medium">{{ $user->last_name }}</p>
            </div>
            <div class="personal-info-item">
                <span class="personal-info-label font-serif-medium">Date of Birth</span>
                <p class="personal-info-value font-serif-medium">{{ $user->dob ? $user->dob->format('d F Y') : 'N/A' }}</p>
            </div>
        </div>

        <div class="separator"></div>

        {{-- Kolom Kanan: Profile Info --}}
        <div class="personal-info-column">
            <h4 class="personal-info-subtitle font-serif-medium">Profile Info</h4>
            <div class="personal-info-item">
                <span class="personal-info-label font-serif-medium">Email</span>
                <p class="personal-info-value font-serif-medium">{{ $user->email }}</p>
            </div>
            <div class="personal-info-item mt-4">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-logout font-serif-medium">Logout</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* New styles for Personal Information tab */
    .personal-info-section {
        display: flex;
        flex-wrap: wrap; /* Allow wrapping on smaller screens */
        gap: 30px; /* Space between columns */
        justify-content: center; /* Center columns if not full width */
        text-align: left; /* Override parent text-align center */
    }

    .personal-info-column {
        flex: 1; /* Allow columns to grow */
        min-width: 280px; /* Minimum width before wrapping */
    }

    .personal-info-title {
        font-size: 35px; /* My Account title */
        margin-bottom: 8px;
        text-align: left;
        opacity: 45%;
    }

    .personal-info-subtitle {
        font-size: 16px; /* Private Info / Profile Info */
        font-weight: 600;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        opacity: 45%;
    }

    .personal-info-item {
        margin-bottom: 15px;
        margin-left: 20px;
    }

    .personal-info-label {
        font-size: 16px;
        opacity: 45%;
        display: block;
    }

    .personal-info-value {
        font-size: 30px;
        font-style: italic; /* Sesuai gambar */
        color: #333;
        font-weight: normal; /* Untuk menimpa bold dari parent */
    }

    .btn-logout {
        background-color: #f0f0f0;
        color: #ec1f1f;
        border: 1px solid #ccc;
        padding: 8px 15px;
        border-radius: 5px;
        font-size: 0.95rem;
        transition: background-color 0.3s ease;
    }

    .btn-change-password:hover {
        background-color: #c72020;
    }

    .separator {
        border-left: 0.1px #000000 solid;
        opacity: 30%;
    }

    #edit_personal_dob {
        cursor: text; 
    }
</style>
@endpush