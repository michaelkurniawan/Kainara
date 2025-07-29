<!-- resources/views/payment/stripe.blade.php -->
@extends('layouts.app')

@section('title', 'Selesaikan Pembayaran Anda')

@push('styles')
<style>
    /* Styling untuk form Stripe Elements */
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
        color: #721c24; /* Warna merah gelap untuk teks error */
        background: #f8d7da; /* Latar belakang merah muda */
        padding: 12px;
        border-radius: 4px;
        margin-bottom: 12px;
        border: 1px solid #f5c6cb; /* Border merah */
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
                <h1 class="text-center mb-4">Selesaikan Pembayaran untuk Pesanan #{{ $order->id }}</h1>
                <p class="text-center mb-4 fs-4">Total: IDR {{ number_format($order->grand_total, 0, ',', '.') }}</p>
                {{-- $order->grand_total akan menggunakan accessor dari model Order --}}

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form id="payment-form">
                    <div id="payment-element">
                        <!-- Stripe Elements akan disisipkan di sini -->
                    </div>
                    <button id="submit-button">Bayar Sekarang</button>
                    <div id="payment-message" class="hidden mt-3"></div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const stripe = Stripe('{{ $stripePublicKey }}'); // Kunci publik Stripe Anda dari backend
    const clientSecret = '{{ $clientSecret }}'; // Client secret dari backend
    const orderId = {{ $order->id }}; // ID Order dari backend

    // Inisialisasi Stripe Elements
    // Ini adalah UI form pembayaran kartu yang disediakan oleh Stripe
    const elements = stripe.elements({ clientSecret });
    const paymentElement = elements.create('payment');
    paymentElement.mount('#payment-element'); // Pasang elemen ke div di halaman HTML

    const form = document.getElementById('payment-form');
    const submitButton = document.getElementById('submit-button');
    const paymentMessage = document.getElementById('payment-message');

    form.addEventListener('submit', async (e) => {
        e.preventDefault(); // Mencegah submit form standar

        submitButton.disabled = true; // Nonaktifkan tombol untuk mencegah double click
        paymentMessage.textContent = ''; // Hapus pesan sebelumnya
        paymentMessage.classList.add('hidden'); // Sembunyikan pesan

        try {
            // Konfirmasi pembayaran dengan Stripe
            const { error, paymentIntent } = await stripe.confirmPayment({
                elements, // Element pembayaran yang sudah kita mount
                confirmParams: {
                    // return_url: URL di mana Stripe akan mengalihkan pengguna setelah pembayaran selesai.
                    // Ini penting untuk alur non-webhook.
                    return_url: '{{ route('stripe.payment.confirm', $order->id) }}',
                },
                // redirect: 'if_required' akan menangani 3D Secure atau redirect lainnya secara otomatis
                // Jika tidak ada redirect, PaymentIntent akan tersedia langsung di respons.
                redirect: 'if_required',
            });

            if (error) {
                // Tampilkan pesan error kepada pengguna
                paymentMessage.textContent = error.message;
                paymentMessage.classList.remove('hidden');
                submitButton.disabled = false; // Aktifkan kembali tombol
            } else if (paymentIntent) {
                // Jika PaymentIntent tersedia (baik sukses, requires_action, atau lainnya)
                // Kirim status ke backend untuk diperbarui di database
                sendConfirmationToBackend(paymentIntent.id, paymentIntent.status);
            } else {
                // Kasus lain yang tidak terduga
                paymentMessage.textContent = 'Terjadi kesalahan tidak terduga dalam proses pembayaran.';
                paymentMessage.classList.remove('hidden');
                submitButton.disabled = false;
            }
        } catch (err) {
            console.error('Error during payment confirmation:', err);
            paymentMessage.textContent = 'Terjadi kesalahan jaringan atau internal server. Mohon coba lagi.';
            paymentMessage.classList.remove('hidden');
            submitButton.disabled = false;
        }
    });

    /**
     * Fungsi untuk mengirim status pembayaran ke backend Anda.
     * Ini penting karena `redirect: 'if_required'` mungkin tidak selalu redirect browser
     * atau untuk menangani kasus ketika pengguna kembali ke halaman ini setelah redirect Stripe.
     */
    async function sendConfirmationToBackend(paymentIntentId, paymentIntentStatus) {
        try {
            const response = await fetch('{{ route('stripe.payment.confirm', $order->id) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}', // Laravel CSRF token
                },
                body: JSON.stringify({
                    payment_intent_id: paymentIntentId,
                    payment_intent_status: paymentIntentStatus,
                }),
            });

            const data = await response.json();

            if (data.success) {
                // Jika backend melaporkan sukses, arahkan ke halaman sukses
                window.location.href = data.redirect_url;
            } else {
                // Jika backend melaporkan gagal atau memerlukan tindakan
                paymentMessage.textContent = data.message || 'Konfirmasi pembayaran gagal di sistem kami.';
                paymentMessage.classList.remove('hidden');
                // Arahkan ke URL yang diberikan oleh backend (misalnya halaman gagal)
                if (data.redirect_url) {
                    window.location.href = data.redirect_url;
                } else {
                    submitButton.disabled = false; // Aktifkan kembali tombol jika tidak ada redirect
                }
            }
        } catch (error) {
            console.error('Error sending payment confirmation to backend:', error);
            paymentMessage.textContent = 'Kesalahan jaringan atau server saat mengonfirmasi pembayaran. Mohon coba lagi.';
            paymentMessage.classList.remove('hidden');
            submitButton.disabled = false;
        }
    }

    /**
     * Tangani skenario di mana pengguna dialihkan kembali ke halaman ini setelah 3D Secure
     * atau otentikasi lainnya (saat `return_url` dipicu).
     * Stripe akan menambahkan `payment_intent_client_secret` ke URL.
     */
    const urlParams = new URLSearchParams(window.location.search);
    const clientSecretFromUrl = urlParams.get('payment_intent_client_secret');

    // Jika clientSecretFromUrl ada dan cocok dengan clientSecret kita, artinya pengguna kembali dari redirect Stripe
    if (clientSecretFromUrl && clientSecretFromUrl === clientSecret) {
        // Ambil PaymentIntent dari Stripe untuk memeriksa statusnya setelah redirect
        stripe.retrievePaymentIntent(clientSecretFromUrl).then(({ paymentIntent }) => {
            if (paymentIntent) {
                sendConfirmationToBackend(paymentIntent.id, paymentIntent.status);
            } else {
                paymentMessage.textContent = 'Tidak dapat mengambil intent pembayaran setelah pengalihan.';
                paymentMessage.classList.remove('hidden');
                submitButton.disabled = false;
            }
        }).catch(err => {
            console.error('Error retrieving payment intent after redirect:', err);
            paymentMessage.textContent = 'Kesalahan saat mengambil status pembayaran setelah pengalihan.';
            paymentMessage.classList.remove('hidden');
            submitButton.disabled = false;
        });
    }
</script>
@endpush