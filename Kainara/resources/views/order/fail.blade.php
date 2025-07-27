<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Gagal</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background-color: #f8f9fa; font-family: sans-serif; }
        .container { margin-top: 50px; text-align: center; }
        .card { border: none; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .card-header { background-color: #dc3545; color: white; border-radius: 8px 8px 0 0; font-size: 1.5rem; font-weight: bold; }
        .icon { font-size: 5rem; color: #dc3545; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        Pembayaran Gagal!
                    </div>
                    <div class="card-body">
                        <i class="bi bi-x-circle-fill icon"></i>
                        <h2 class="card-title text-danger">Pembayaran Anda untuk pesanan #{{ $order->id }} gagal.</h2>
                        <p class="card-text">Ada masalah saat memproses pembayaran Anda. Silakan coba lagi atau pilih metode pembayaran lain.</p>
                        <a href="{{ route('checkout.index') }}" class="btn btn-primary mt-3">Coba Lagi</a>
                        <a href="{{ route('order.details', $order->id) }}" class="btn btn-secondary mt-3">Lihat Detail Pesanan</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>