@extends('layouts.app')

@section('title', 'Confirm Full Refund')

@push('styles')
<style>
    .refund-container {
        max-width: 600px;
        margin: 50px auto;
        padding: 30px;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        background-color: #fff;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }
    .refund-container h2 {
        font-family: var(--font-primary);
        color: #333;
        margin-bottom: 25px;
        text-align: center;
    }
    .form-label {
        font-weight: bold;
        color: #555;
    }
    .form-control:focus {
        border-color: #B6B09F;
        box-shadow: 0 0 0 0.25rem rgba(182, 176, 159, 0.25);
    }
    .btn-submit-refund {
        background-color: #B6B09F;
        color: white;
        padding: 10px 25px;
        border-radius: 5px;
        transition: background-color 0.3s ease;
        width: 100%;
    }
    .btn-submit-refund:hover {
        background-color: #9c9685;
        color: white;
    }
    .alert-info {
        background-color: #e0f7fa;
        color: #007bb6;
        border-color: #b3e5fc;
    }
    .alert-danger {
        background-color: #fce4ec;
        color: #d32f2f;
        border-color: #ef9a9a;
    }
    .refund-summary-info {
        background-color: #f8f9fa;
        border: 1px dashed #e9ecef;
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 25px;
        text-align: center;
    }
    .refund-summary-info strong {
        font-size: 1.25rem;
        color: #333;
    }
</style>
@endpush

@section('content')
<div class="container py-5">
    <div class="refund-container">
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

        <div class="refund-summary-info">
            <p class="mb-2">You are requesting a **FULL REFUND** for this order.</p>
            <p>Total amount to be refunded: <strong>IDR {{ number_format($amountToRefund, 0, ',', '.') }}</strong></p>
            <p class="text-muted small">This will refund all items and the full payment amount.</p>
        </div>

        <form id="refundRequestForm" method="POST" action="{{ route('refund.request.submit', $order->id) }}">
            @csrf

            <div class="mb-3 mt-4">
                <label for="reason" class="form-label">Reason for Full Refund</label>
                <textarea class="form-control" id="reason" name="reason" rows="4" placeholder="e.g., Order canceled, full return of goods" required>{{ old('reason') }}</textarea>
                <div class="form-text">Please provide a detailed reason for the full refund.</div>
            </div>

            <button type="submit" class="btn btn-submit-refund">Confirm Full Refund</button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const refundForm = document.getElementById('refundRequestForm');
        const submitButton = refundForm.querySelector('.btn-submit-refund');
        const reasonInput = document.getElementById('reason');

        refundForm.addEventListener('submit', function(e) {
            e.preventDefault();

            if (!reasonInput.value.trim()) {
                alert('Please provide a reason for the refund.');
                reasonInput.focus();
                return;
            }

            submitButton.disabled = true;
            submitButton.textContent = 'Processing...';

            const formData = new FormData();
            formData.append('_token', this.querySelector('input[name="_token"]').value);
            formData.append('reason', reasonInput.value);

            // No need to append 'amount' or 'refund_items' as the backend will calculate full refund

            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                return response.json().then(data => {
                    if (!response.ok) {
                        if (response.status === 422 && data.errors) {
                            let errorMessage = 'Validation Error:\n';
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
                });
            })
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