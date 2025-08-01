@extends('layouts.app')

@section('title', 'Confirm Full Refund')

@push('styles')
<style>
    :root {
        --font-primary: 'Ancizar Serif', serif;
        --font-secondary: 'Ancizar Serif', serif;
    }
    .form-container {
        max-width: 600px;
        margin: 50px auto;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        background-color: #fff;
    }
    .form-container h2 {
        color: #333;
        margin-bottom: 25px;
        font-weight: bold;
    }
    .form-label {
        font-weight: 600;
        color: #555;
    }
    .btn-submit-refund {
        background-color: #B6B09F;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        font-size: 1rem;
        cursor: pointer;
    }
    .btn-submit-refund:hover {
        background-color: #9c9685;
    }
    .alert-info-custom {
        background-color: #e6f7ff;
        border-color: #91d5ff;
        color: #000;
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 20px;
        font-weight: 500;
    }
    .image-preview-container {
        margin-top: 15px;
        border: 1px dashed #ccc;
        padding: 10px;
        border-radius: 5px;
        text-align: center;
        min-height: 100px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #f9f9f9;
    }
    .image-preview-container img {
        max-width: 100%;
        max-height: 150px;
        display: block;
        margin: 0 auto;
        border-radius: 4px;
    }
    .image-preview-container p {
        color: #777;
        margin: 0;
    }
</style>
@endpush

@section('content')
<div class="container py-5 px-5">
    <div class="form-container">
        <h2 class="fs-4">Confirm Full Refund for Order #{{ $order->id }}</h2>

        <p class="alert-info-custom">
            Total Paid: <strong>IDR {{ number_format($payment->amount_paid, 0, ',', '.') }}</strong>
            <br>
            Amount available for refund: <strong>IDR {{ number_format($availableForRefund, 0, ',', '.') }}</strong>
        </p>

        <form id="refundForm" method="POST" action="{{ route('refund.request.submit', $order->id) }}" enctype="multipart/form-data">
            @csrf

            <div class="mb-3 mt-4">
                <label for="reason" class="form-label">Reason for Full Refund</label>
                <textarea class="form-control" id="reason" name="reason" rows="4" placeholder="e.g., Order canceled, full return of goods due to defect" required>{{ old('reason') }}</textarea>
                <div class="form-text">Please provide a detailed reason for the full refund.</div>
            </div>

            <div class="mb-3">
                <label for="refund_image" class="form-label">Upload Image (Optional)</label>
                <input type="file" class="form-control" id="refund_image" name="refund_image" accept="image/*">
                <small class="form-text text-muted">Upload an image as proof (e.g., product damage). Max 2MB.</small>
                <div class="image-preview-container" id="imagePreview">
                    <p>No Image Selected</p>
                </div>
            </div>

            <button type="submit" class="btn btn-submit-refund">Confirm Full Refund</button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const refundForm = document.getElementById('refundForm');
        const submitButton = refundForm.querySelector('.btn-submit-refund');
        const reasonInput = document.getElementById('reason');
        const refundImageInput = document.getElementById('refund_image');
        const imagePreviewContainer = document.getElementById('imagePreview');

        // Function to display image preview
        refundImageInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreviewContainer.innerHTML = `<img src="${e.target.result}" alt="Image Preview">`;
                };
                reader.readAsDataURL(file);
            } else {
                imagePreviewContainer.innerHTML = `<p>No Image Selected</p>`;
            }
        });

        refundForm.addEventListener('submit', function(e) {
            e.preventDefault();

            if (!reasonInput.value.trim()) {
                window.showNotificationCard({
                    type: 'error',
                    title: 'Validation Failed',
                    message: 'Please provide a reason for the refund request.',
                    hasActions: false
                });
                reasonInput.focus();
                return;
            }

            // Show a confirmation dialog before submitting
            window.showNotificationCard({
                type: 'confirmation',
                title: 'Confirm Full Refund',
                message: 'Are you sure you want to submit this full refund request? This action cannot be undone.',
                hasActions: true,
                onConfirm: () => {
                    // User confirmed, proceed with submission
                    submitButton.disabled = true;
                    submitButton.textContent = 'Processing Refund...';

                    const formData = new FormData(refundForm);

                    fetch(refundForm.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(async response => {
                        const data = await response.json();
                        if (!response.ok) {
                            let errorMessage;
                            if (response.status === 422 && data.errors) {
                                const errorMessages = Object.values(data.errors).flat().join(' ');
                                errorMessage = `Validation failed: ${errorMessages}`;
                            } else {
                                errorMessage = data.message || `Server error occurred during refund request (Status: ${response.status}).`;
                            }
                            throw new Error(errorMessage);
                        }
                        return data;
                    })
                    .then(data => {
                        window.showNotificationCard({
                            type: 'success',
                            title: 'Request Submitted',
                            message: data.message,
                            hasActions: false, // For simple success, no need for action buttons
                            onConfirm: () => {
                                if (data.redirect_url) {
                                    window.location.href = data.redirect_url;
                                } else {
                                    window.location.reload();
                                }
                            }
                        });
                    })
                    .catch(error => {
                        console.error('Refund request failed:', error);
                        window.showNotificationCard({
                            type: 'error',
                            title: 'Request Failed',
                            message: 'An error occurred: ' + error.message,
                            hasActions: false
                        });
                    })
                    .finally(() => {
                        submitButton.disabled = false;
                        submitButton.textContent = 'Confirm Full Refund';
                    });
                },
                onCancel: () => {
                    console.log('Refund request cancelled.');
                }
            });
        });
    });
</script>
@endpush