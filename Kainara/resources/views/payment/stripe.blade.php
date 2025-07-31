@extends('layouts.app')

@section('title', 'Complete Your Payment')

@push('styles')
<style>
    :root {
        --font-primary: 'Ancizar Serif', serif;
        --font-secondary: 'Ancizar Serif', serif;
    }
    /* Styling for Stripe Elements form */
    #payment-element {
        margin-bottom: 24px;
        padding: 10px;
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
    }
    #payment-form button {
        background-color: #B6B09F;
        color: white;
        padding: 12px 20px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        width: 100%;
        font-size: 16px;
        transition: background-color 0.3s ease;
    }
    #payment-form button:hover {
        background-color: #9a9a9a;
    }
    #payment-message {
        color: #721c24;
        background: #f8d7da;
        padding: 12px;
        border-radius: 4px;
        margin-bottom: 12px;
        border: 1px solid #f5c6cb;
    }
    .hidden {
        display: none;
    }
</style>
@endpush

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm p-4">
                <h1 class="text-center mb-4">Complete Payment for Order #{{ $order->id }}</h1>
                <p class="text-center mb-4 fs-4">Total: IDR {{ number_format($order->grand_total, 0, ',', '.') }}</p>

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form id="payment-form">
                    <div id="payment-element">
                        </div>
                    <button id="submit-button">Pay Now</button>
                    <div id="payment-message" class="hidden mt-3"></div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const stripe = Stripe('{{ $stripePublicKey }}');
    const clientSecret = '{{ $clientSecret }}';
    const orderId = {{ $order->id }};

    // Initialize Stripe Elements with locale set to 'en' for English
    const elements = stripe.elements({ clientSecret, locale: 'en' });
    const paymentElement = elements.create('payment');
    paymentElement.mount('#payment-element');

    const form = document.getElementById('payment-form');
    const submitButton = document.getElementById('submit-button');
    const paymentMessage = document.getElementById('payment-message');

    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        submitButton.disabled = true;
        paymentMessage.textContent = '';
        paymentMessage.classList.add('hidden');

        try {
            const { error, paymentIntent } = await stripe.confirmPayment({
                elements,
                confirmParams: {
                    return_url: '{{ route('stripe.payment.confirm', $order->id) }}',
                },
                redirect: 'if_required',
            });

            if (error) {
                paymentMessage.textContent = error.message;
                paymentMessage.classList.remove('hidden');
                submitButton.disabled = false;
            } else if (paymentIntent) {
                sendConfirmationToBackend(paymentIntent.id, paymentIntent.status);
            } else {
                paymentMessage.textContent = 'An unexpected error occurred during the payment process.';
                paymentMessage.classList.remove('hidden');
                submitButton.disabled = false;
            }
        } catch (err) {
            console.error('Error during payment confirmation:', err);
            paymentMessage.textContent = 'A network or internal server error occurred. Please try again.';
            paymentMessage.classList.remove('hidden');
            submitButton.disabled = false;
        }
    });

    async function sendConfirmationToBackend(paymentIntentId, paymentIntentStatus) {
        try {
            const response = await fetch('{{ route('stripe.payment.confirm', $order->id) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: JSON.stringify({
                    payment_intent_id: paymentIntentId,
                    payment_intent_status: paymentIntentStatus,
                }),
            });

            const data = await response.json();

            if (data.success) {
                window.location.href = data.redirect_url;
            } else {
                paymentMessage.textContent = data.message || 'Payment confirmation failed in our system.';
                paymentMessage.classList.remove('hidden');
                if (data.redirect_url) {
                    window.location.href = data.redirect_url;
                } else {
                    submitButton.disabled = false;
                }
            }
        } catch (error) {
            console.error('Error sending payment confirmation to backend:', error);
            paymentMessage.textContent = 'Network or server error while confirming payment. Please try again.';
            paymentMessage.classList.remove('hidden');
            submitButton.disabled = false;
        }
    }

    const urlParams = new URLSearchParams(window.location.search);
    const clientSecretFromUrl = urlParams.get('payment_intent_client_secret');

    if (clientSecretFromUrl && clientSecretFromUrl === clientSecret) {
        stripe.retrievePaymentIntent(clientSecretFromUrl).then(({ paymentIntent }) => {
            if (paymentIntent) {
                sendConfirmationToBackend(paymentIntent.id, paymentIntent.status);
            } else {
                paymentMessage.textContent = 'Could not retrieve payment intent after redirect.';
                paymentMessage.classList.remove('hidden');
                submitButton.disabled = false;
            }
        }).catch(err => {
            console.error('Error retrieving payment intent after redirect:', err);
            paymentMessage.textContent = 'Error retrieving payment status after redirect.';
            paymentMessage.classList.remove('hidden');
            submitButton.disabled = false;
        });
    }
</script>
@endpush