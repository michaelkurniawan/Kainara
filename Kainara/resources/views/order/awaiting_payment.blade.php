<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menunggu Pembayaran</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background-color: #f8f9fa; font-family: sans-serif; }
        .container { margin-top: 50px; text-align: center; }
        .card { border: none; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .card-header { background-color: #ffc107; color: white; border-radius: 8px 8px 0 0; font-size: 1.5rem; font-weight: bold; }
        .icon { font-size: 5rem; color: #ffc107; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        Pesanan Dibuat - Menunggu Pembayaran
                    </div>
                    <div class="card-body">
                        <i class="bi bi-clock-fill icon"></i>
                        <h2 class="card-title text-warning">Pesanan Anda #{{ $order->id }} telah dibuat.</h2>
                        <p class="card-text">Harap selesaikan pembayaran Anda via **{{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}** dalam waktu yang ditentukan untuk mengkonfirmasi pesanan Anda.</p>
                        <p>Jumlah Total: <strong>IDR {{ number_format($order->grand_total, 0, ',', '.') }}</strong></p>
                        <p class="text-muted">Instruksi lebih lanjut akan dikirim ke email Anda.</p>
                        <a href="{{ route('home') }}" class="btn btn-primary mt-3">Lanjutkan Belanja</a>
                        <a href="{{ route('order.details', $order->id) }}" class="btn btn-secondary mt-3">Lihat Detail Pesanan</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>