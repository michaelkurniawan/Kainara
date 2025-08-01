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
                <label for="refund_image" class="form-label">Upload Image <span class="text-danger">*</span></label>
                <input type="file" class="form-control" id="refund_image" name="refund_image" accept="image/*" required>
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
    // Global function to display Toast notifications
    function showToast(type, title, message) {
        const toastContainer = document.querySelector('.toast-container');
        if (!toastContainer) {
            console.error('Toast container not found. Cannot show toast.');
            return;
        }
        const toastElement = document.createElement('div');
        toastElement.className = `toast align-items-center text-white bg-${type} border-0`;
        toastElement.setAttribute('role', 'alert');
        toastElement.setAttribute('aria-live', 'assertive');
        toastElement.setAttribute('aria-atomic', 'true');
        toastElement.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    <strong>${title}</strong><br>${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        `;
        toastContainer.appendChild(toastElement);
        const toast = new bootstrap.Toast(toastElement);
        toast.show();
    }

    document.addEventListener('DOMContentLoaded', function() {
        const refundForm = document.getElementById('refundForm');
        const submitButton = refundForm.querySelector('.btn-submit-refund');
        const reasonInput = document.getElementById('reason');
        const refundImageInput = document.getElementById('refund_image');
        const imagePreviewContainer = document.getElementById('imagePreview');

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

        // Add a click event listener to the submit button
        submitButton.addEventListener('click', function(e) {
            e.preventDefault(); // Prevent the form from submitting immediately

            // Client-side validation
            if (!reasonInput.value.trim()) {
                showToast('warning', 'Validation Failed', 'Please provide a reason for the refund.');
                reasonInput.focus();
                return;
            }
            if (refundImageInput.files.length === 0) {
                showToast('warning', 'Validation Failed', 'Please upload an image as proof for the refund.');
                return;
            }

            // Show the confirmation dialog
            // NOTE: This assumes `window.showNotificationCard` is defined globally as in your example.
            if (typeof window.showNotificationCard === 'function') {
                window.showNotificationCard({
                    type: 'confirmation',
                    title: 'Confirm Full Refund',
                    message: 'Are you sure you want to submit this full refund request?',
                    hasActions: true,
                    onConfirm: () => {
                        // If confirmed, proceed with the refund submission
                        submitRefundRequest();
                    },
                    onCancel: () => {
                        console.log('Refund request cancelled by user.');
                    }
                });
            } else {
                // Fallback to a simple confirm dialog if the custom one is not available
                if (confirm('Are you sure you want to submit this full refund request?')) {
                    submitRefundRequest();
                }
            }

            if ($action === 'buy_now') {

            return redirect()->route('profile.index', ['#order-history'])->with('notification', [
                'type' => 'success',
                'title' => 'Fully Request Refund',
                'message' => 'Your refund request has been submitted and is awaiting admin review.',
                'hasActions' => false
            ]);
        });

        // Function to handle the actual refund submission via Fetch API
        function submitRefundRequest() {
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
            .then(response => {
                if (response.status === 422) {
                    return response.json().then(data => {
                        let errorMessage = '';
                        for (const key in data.errors) {
                            errorMessage += `${data.errors[key].join(', ')}\n`;
                        }
                        return Promise.reject(new Error(errorMessage));
                    });
                }
                if (!response.ok) {
                    return response.json().then(data => Promise.reject(new Error(data.message)));
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    showToast('success', 'Refund Submitted', data.message);
                    if (data.redirect_url) {
                        setTimeout(() => {
                            window.location.href = data.redirect_url;
                        }, 2000);
                    }
                } else {
                    showToast('error', 'Request Failed', data.message);
                }
            })
            .catch(error => {
                console.error('Refund request failed:', error);
                showToast('error', 'Request Failed', error.message);
            })
            .finally(() => {
                submitButton.disabled = false;
                submitButton.textContent = 'Confirm Full Refund';
            });
        }
    });
</script>
@endpush