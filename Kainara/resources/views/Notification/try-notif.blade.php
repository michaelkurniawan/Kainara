{{--
    resources/views/notification/try-notif.blade.php

    Ini adalah halaman web mandiri untuk mencoba notifikasi.
    Dapat diakses melalui route: Route::get('/trynotif', function () { return view('notification.try-notif'); });
--}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Try Pop-Up Notification</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        /* CSS kustom untuk styling halaman dan tombol pemicu */
        body {
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            gap: 20px;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #f0f2f5;
        }
        .btn-primary-custom {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .btn-primary-custom:hover {
            background-color: #0056b3;
        }
        /* Styling untuk container tombol */
        .container-center {
            text-align: center;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            margin-top: 50px; /* Jarak dari atas */
        }
    </style>
</head>
<body>

    <div id="app">
        <div class="container container-center">
            <h1>Demo Notifikasi dengan Laravel & Bootstrap</h1>
            <p class="mb-4">Klik tombol di bawah untuk mencoba berbagai jenis notifikasi.</p>

            <div class="d-grid gap-3 col-md-8 mx-auto">
                <button class="btn btn-danger btn-primary-custom" onclick="showError()">
                    Tampilkan Notifikasi Error
                </button>
                <button class="btn btn-primary btn-primary-custom" onclick="showConfirmation()">
                    Tampilkan Konfirmasi
                </button>
                <button class="btn btn-info btn-primary-custom" onclick="showInfo()">
                    Tampilkan Informasi
                </button>
                <button class="btn btn-success btn-primary-custom" onclick="showSuccess()">
                    Tampilkan Sukses
                </button>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS (Popper.js diperlukan) -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTFyMfwGpOfJTxuYkHwFhkqofYjQERJvLikKkOqNQnwaJtybu5Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlco9j7w8xid/EzcKdyfLwXoPy+1/Hp/r+vE/P/gE4y/E+Q" crossorigin="anonymous"></script>

    {{-- MEMANGGIL NOTIFIKASI KOMPONEN DI SINI --}}
    @include('notification.notif-components')

    {{-- Script untuk memicu notifikasi --}}
    <script>
        // Fungsi untuk menampilkan notifikasi error
        function showError() {
            window.showNotificationCard({
                type: 'error',
                title: 'Oops! Terjadi Kesalahan',
                message: 'Maaf, data yang Anda masukkan tidak valid. Silakan coba lagi.',
                hasActions: false, // Tidak ada tombol YES/NO
                onCancel: () => {
                    console.log('Notifikasi error ditutup.');
                    // alert('Anda menutup notifikasi error!'); // Gunakan alert untuk debugging, tapi hindari di produksi
                }
            });
        }

        // Fungsi untuk menampilkan notifikasi konfirmasi
        function showConfirmation() {
            window.showNotificationCard({
                type: 'confirmation',
                title: 'Konfirmasi Perubahan Profil?',
                message: 'Apakah Anda yakin ingin menyimpan perubahan pada profil Anda?',
                hasActions: true, // Ada tombol YES/NO
                onConfirm: () => {
                    console.log('Perubahan profil dikonfirmasi!');
                    alert('Perubahan profil berhasil disimpan!');
                    // Di sini Anda bisa menambahkan logika AJAX untuk menyimpan data ke server
                },
                onCancel: () => {
                    console.log('Perubahan profil dibatalkan.');
                    alert('Perubahan profil dibatalkan!');
                }
            });
        }

        // Fungsi untuk menampilkan notifikasi informasi
        function showInfo() {
            window.showNotificationCard({
                type: 'info',
                title: 'Pemberitahuan Informasi Penting',
                message: 'Ini adalah pesan informasi terkait aktivitas terbaru Anda di sistem kami.',
                hasActions: false,
                onCancel: () => {
                    console.log('Notifikasi informasi ditutup.');
                }
            });
        }

        // Fungsi untuk menampilkan notifikasi sukses
        function showSuccess() {
            window.showNotificationCard({
                type: 'success',
                title: 'Sukses!',
                message: 'Operasi Anda berhasil diselesaikan dengan sempurna.',
                hasActions: false,
                onCancel: () => {
                    console.log('Notifikasi sukses ditutup.');
                }
            });
        }
    </script>
    {{-- @stack('scripts') dihapus karena tidak ada extend layout --}}
</body>
</html>