<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Stripe untuk Pesanan #{{ $order->id }}</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Stripe.js v3 -->
    <script src="https://js.stripe.com/v3/"></script>
    <!-- Penting untuk AJAX dan keamanan CSRF Laravel -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body {
            font-family: sans-serif;
            background-color: #f8f9fa;
        }
        .container {
            margin-top: 50px;
        }
        .card {
            border: none;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .card-header {
            background-color: #007bff;
            color: white;
            border-radius: 8px 8px 0 0;
            font-size: 1.25rem;
            font-weight: bold;
            padding: 1.25rem;
        }
        .card-body {
            padding: 2rem;
        }
        /* Styling untuk Stripe Elements */
        .StripeElement {
            box-sizing: border-box;
            height: 40px;
            padding: 10px 12px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            background-color: white;
            box-shadow: 0 1px 3px 0 #e6ebf1;
            -webkit-transition: box-shadow 150ms ease;
            transition: box-shadow 150ms ease;
        }
        .StripeElement--focus {
            box-shadow: 0 1px 3px 0 #cfd7df;
        }
        .StripeElement--invalid {
            border-color: #fa755a;
        }
        .StripeElement--webkit-autofill {
            background-color: #fefde5 !important;
        }
        #card-errors {
            color: #fa755a;
            margin-top: 10px;
        }
        .btn-primary {
            background-color: #28a745;
            border-color: #28a745;
        }
        .btn-primary:hover {
            background-color: #218838;
            border-color: #1e7e34;
        }
        .loading-overlay {
            display: flex;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            justify-content: center;
            align-items: center;
            z-index: 1000;
            visibility: hidden;
            opacity: 0;
            transition: visibility 0s, opacity 0.3s linear;
        }
        .loading-overlay.active {
            visibility: visible;
            opacity: 1;
        }
        .spinner-border {
            width: 3rem;
            height: 3rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        Selesaikan Pembayaran Anda untuk Pesanan #{{ $order->id }}
                    </div>
                    <div class="card-body">
                        <h5 class="mb-4">Jumlah Total: <strong>IDR {{ number_format($order->grand_total, 0, ',', '.') }}</strong></h5>

                        <form id="payment-form">
                            @csrf <!-- Token CSRF untuk keamanan form -->
                            <div class="form-group">
                                <label for="card-element">Kartu Kredit atau Debit</label>
                                <div id="card-element" class="StripeElement">
                                    <!-- Stripe Elements akan dimasukkan di sini. -->
                                </div>
                                <!-- Tempat untuk menampilkan error validasi kartu -->
                                <div id="card-errors" role="alert"></div>
                            </div>
                            <button type="submit" id="submit-button" class="btn btn-primary btn-block mt-4">Bayar Sekarang</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div id="loading-overlay" class="loading-overlay">
        <div class="spinner-border text-primary" role="status">
            <span class="sr-only">Memuat...</span>
        </div>
    </div>

    <script>
        // Inisialisasi Stripe dengan Public Key Anda
        const stripe = Stripe('{{ $stripePublicKey }}'); // $stripePublicKey datang dari controller
        const elements = stripe.elements();

        // Buat instance Stripe Elements 'card'
        const cardElement = elements.create('card', {
            style: {
                base: {
                    iconColor: '#666EE8',
                    color: '#313259',
                    fontWeight: '300',
                    fontFamily: 'Helvetica Neue, Helvetica, Arial, sans-serif',
                    fontSize: '16px',
                    '::placeholder': {
                        color: '#aab7c4',
                    },
                },
                invalid: {
                    iconColor: '#FFC7EE',
                    color: '#FFC7EE',
                },
            },
        });

        // Mount Card Element ke elemen DOM
        cardElement.mount('#card-element');

        const form = document.getElementById('payment-form');
        const cardErrors = document.getElementById('card-errors');
        const submitButton = document.getElementById('submit-button');
        const loadingOverlay = document.getElementById('loading-overlay');

        // Tangani error real-time saat pengguna memasukkan detail kartu
        cardElement.on('change', function(event) {
            if (event.error) {
                cardErrors.textContent = event.error.message;
            } else {
                cardErrors.textContent = '';
            }
        });

        // Tangani saat form pembayaran disubmit
        form.addEventListener('submit', async function(event) {
            event.preventDefault(); // Mencegah submit form standar

            submitButton.disabled = true; // Nonaktifkan tombol untuk mencegah double click
            loadingOverlay.classList.add('active'); // Tampilkan loading overlay

            // Konfirmasi PaymentIntent di sisi klien
            // Client secret datang dari controller
            const { paymentIntent, error } = await stripe.confirmCardPayment('{{ $clientSecret }}', {
                payment_method: {
                    card: cardElement, // Gunakan elemen kartu yang sudah di-mount
                    billing_details: {
                        // Informasi billing bisa diambil dari form checkout atau data user
                        name: "{{ $order->customer_first_name ?? 'Guest' }} {{ $order->customer_last_name ?? '' }}",
                        email: "{{ $order->customer_email ?? 'guest@example.com' }}",
                    },
                }
            });

            if (error) {
                // Tampilkan error jika konfirmasi gagal (misal: kartu ditolak)
                cardErrors.textContent = error.message;
                submitButton.disabled = false; // Aktifkan kembali tombol
                loadingOverlay.classList.remove('active'); // Sembunyikan loading overlay
            } else {
                // Jika PaymentIntent berhasil dikonfirmasi di sisi klien (misal: status 'succeeded' atau 'requires_action')
                // Kirim PaymentIntent ID dan status ke backend Laravel untuk verifikasi dan update database
                const response = await fetch('{{ route('checkout.stripe.confirm', $order->id) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        payment_intent_id: paymentIntent.id,
                        payment_intent_status: paymentIntent.status,
                    })
                });

                const result = await response.json(); // Ambil respons dari backend

                if (result.success) {
                    // Pembayaran berhasil, arahkan ke halaman sukses
                    alert(result.message); // Ganti alert() dengan modal/notifikasi kustom di produksi
                    window.location.href = result.redirect_url;
                } else {
                    // Pembayaran gagal atau memerlukan tindakan lebih lanjut (tapi backend tidak menangani redirect otomatis)
                    alert('Pembayaran gagal: ' + result.message);
                    submitButton.disabled = false;
                    loadingOverlay.classList.remove('active');

                    // Jika backend memberikan URL redirect, ikuti
                    if (result.redirect_url) {
                        window.location.href = result.redirect_url;
                    }
                }
            }
        });
    </script>
</body>
</html>