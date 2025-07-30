@push('styles')
<style>
    /* Updated Button Styles */
    .btn-add-address {
        background-color: #B39C59; /* Background color from image */
        color: white; /* White text for contrast */
        border: 1px solid #AD9D6D; /* Subtle border */
        padding: 8px 15px; /* Adjust padding as needed */
        border-radius: 5px; /* Slightly rounded corners */
        font-size: 0.95rem; /* Adjust font size */
        transition: all 0.3s ease;
        text-decoration: none; /* Ensure no underline from <a> tag */
        display: inline-flex; /* Use flexbox for icon and text alignment */
        align-items: center; /* Vertically center icon and text */
        gap: 5px; /* Space between icon and text */
    }

    .btn-add-address:hover {
        background-color: #AD9D6D; /* Darken on hover */
        color: white;
        border-color: #AD9D6D;
    }

    /* New styles for Edit/Delete action buttons */
    .btn-address-action {
        background-color: transparent;
        color: #AD9D6D; /* Gold color for text */
        border: none;
        padding: 0.25rem 0.5rem; /* Small padding */
        font-size: 0.9rem; /* Slightly smaller font */
        font-weight: 500; /* Medium weight */
        border-radius: 0.25rem; /* Small rounded corners */
        transition: background-color 0.2s ease, color 0.2s ease;
        cursor: pointer;
        display: inline-flex; /* To align with other text/buttons */
        align-items: center;
        gap: 4px; /* Space between icon (if any) and text */
    }

    .btn-address-action:hover {
        background-color: rgba(173, 157, 109, 0.1); /* Light gold background on hover */
        color: #AD9D6D; /* Keep text color same */
        text-decoration: none; /* Ensure no underline on hover */
    }

    /* Remove previous action link styles */
    .address-actions .action-links a {
        /* These styles are now overridden or no longer apply to buttons */
        /* font-size: 1rem; */
        /* opacity: 45%; */
        /* text-decoration: underline; */
    }

    .address-actions .action-links a:hover {
        /* opacity: 100%; */
        /* color: #AD9D6D */
    }

    .address-item {
        border: 1px solid #e0e0e0; /* Light border */
        border-radius: 8px; /* Rounded corners */
        padding: 15px 20px; /* Padding inside the box */
        margin-bottom: 15px; /* Space between address items */
        box-shadow: 0 2px 4px rgba(0,0,0,0.03); /* Subtle shadow */
    }

    /* Styles for default address tag */
    .primary-address-tag { /* Renamed from .primary-address-tag to reflect 'default' */
        color: #B39C59;
        border-radius: 4px;
        font-size: 1.2rem;
        text-transform: uppercase;
    }

    /* Styles for selected address item border */
    .address-item.selected-address {
        border-color: #AD9D6D; /* Changed to direct hex code for consistency */
        box-shadow: 0 0 0 2px rgba(173, 157, 109, 0.3); /* Adjusted rgba based on #AD9D6D */
    }

    .address-label {
        font-size: 1.5rem;
    }
</style>
@endpush

<div class="tab-pane fade" id="addresses" role="tabpanel" aria-labelledby="addresses-tab">
    <div class="addresses-header d-flex justify-content-between align-items-center mb-4">
        <h3 class="personal-info-title mb-0 font-serif-medium">Addresses</h3>
        <button type="button" class="btn btn-add-address font-serif-medium" data-bs-toggle="modal" data-bs-target="#addAddressModal">
            <i class="fas fa-plus"></i> Add new address
        </button>
    </div>
    <hr class="mb-4">

    @forelse ($userAddresses as $userAddress)
        <div class="address-item d-flex justify-content-between align-items-start mb-3" data-address-id="{{ $userAddress['id'] ?? '' }}">
            <div class="address-details d-flex flex-column align-items-start">
                <h6 class="mb-0 font-serif-medium address-label">{{ $userAddress['label'] ?? 'Alamat' }}</h6>
                <p class="address-name-phone fw-bold mb-0 font-serif-medium">{{ $userAddress['recipient_name'] ?? '' }} | {{ $userAddress['phone'] ?? '' }}</p>
                <p class="text-muted mb-0 font-serif-light-italic" data-address-line="address">{{ $userAddress['address'] ?? '' }}</p>
                <p class="text-muted mb-0 font-serif-light-italic" data-address-line="city-province">{{ $userAddress['city'] ?? '' }}{{ ($userAddress['city'] && $userAddress['province']) ? ', ' : '' }}{{ $userAddress['province'] ?? '' }}</p>
                <p class="text-muted mb-0 font-serif-light-italic" data-address-line="country-postal">{{ $userAddress['country'] ?? '' }} {{ $userAddress['postal_code'] ?? '' }}</p>
            </div>
            <div class="address-actions d-flex flex-column align-items-end">
                <div class="action-links font-serif-light-italic">
                    {{-- Changed to button with new class --}}
                    <button type="button" class="btn btn-sm btn-address-action" data-bs-toggle="modal" data-bs-target="#editAddressModal" data-address-id="{{ $userAddress->id }}">
                        Edit
                    </button>
                    |
                    {{-- Changed to button with new class --}}
                    <form action="{{ route('addresses.destroy', $userAddress->id) }}" method="POST" class="d-inline" id="delete-address-form-{{ $userAddress->id }}">
                        @csrf
                        @method('DELETE')
                        <button type="button"
                                class="btn btn-sm btn-address-action trigger-delete-address-notification"
                                data-address-id="{{ $userAddress->id }}"
                                data-address-label="{{ $userAddress->label ?? 'alamat ini' }}">
                            Delete
                        </button>
                    </form>
                </div>
                @if (($userAddress['is_default'] ?? false))
                    <span class="primary-address-tag font-serif-medium">Primary Address</span>
                @endif
            </div>
        </div>
    @empty
        <p class="text-center text-muted font-serif-light-italic">No addresses found. Please add a new address.</p>
    @endforelse
</div>