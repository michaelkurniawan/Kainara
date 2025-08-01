@extends('layouts.app')

@section('title', 'Confirm Full Refund')

@push('styles')
<style>
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
        <h2>Confirm Full Refund for Order #{{ $order->id }}</h2>

        @if (session('error'))
            <div class="alert alert-danger" role="alert">
                {{ session('error') }}
            </div>
        @endif
        @if (session('info'))
            <div class="alert alert-info" role="alert">
                {{ session('info') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <p class="alert-info-custom">
            Total Paid: <strong>IDR {{ number_format($payment->amount_paid, 0, ',', '.') }}</strong>
            <br>
            Amount available for refund: <strong>IDR {{ number_format($availableForRefund, 0, ',', '.') }}</strong>
        </p>

        {{-- IMPORTANT: Ensure enctype="multipart/form-data" for file uploads --}}
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
                alert('Please provide a reason for the refund.');
                reasonInput.focus();
                return;
            }

            submitButton.disabled = true;
            submitButton.textContent = 'Processing Refund...';

            const formData = new FormData(this);

            fetch(this.action, {
                method: 'POST',
                body: formData, // FormData automatically sets Content-Type for file uploads
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest' // Important for Laravel to receive JSON response
                }
            })
            .then(response => response.json().then(data => {
                if (!response.ok) {
                    if (response.status === 422 && data.errors) {
                        let errorMessage = 'Validation Failed:\n';
                        for (const key in data.errors) {
                            if (Array.isArray(data.errors[key])) {
                                errorMessage += `- ${data.errors[key].join(', ')}\n`;
                            } else {
                                errorMessage += `- ${data.errors[key]}\n`;
                            }
                        }
                        throw new Error(errorMessage);
                    } else {
                        throw new Error(data.message || `Server error occurred during refund request (Status: ${response.status}).`);
                    }
                }
                return data;
            }))
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    if (data.redirect_url) {
                        window.location.href = data.redirect_url;
                    } else {
                        window.location.reload();
                    }
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Refund request failed:', error);
                alert('An error occurred: ' + error.message);
            })
            .finally(() => {
                submitButton.disabled = false;
                submitButton.textContent = 'Confirm Full Refund';
            });
        });
    });
</script>
@endpush