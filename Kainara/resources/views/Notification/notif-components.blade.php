<!-- ini untuk template penggunaan notification -->
<!-- kalau di page lain butuh notifikasi, bisa pakai dari sini ya -->

{{--
    Cara pakai:
    1. Pastikan sudah memuat Bootstrap CSS/JS di page kalian.
    2. Include file ini di layout utama page kalian (misalnya, resources/views/layouts/app.blade.php)
       sebelum tag </body> penutup, atau di view lain yang memerlukannya:
       @include('notification.notif-components')
    3. Panggil notifikasi dari JavaScript di mana pun di page kalian:
       window.showNotificationCard({
           type: 'success', // 'info', 'error', 'confirmation'
           title: 'Operasi Berhasil!',
           message: 'Data Anda telah disimpan dengan sukses.',
           hasActions: false, // true untuk tombol YES/NO
           onConfirm: () => { console.log('Confirmed!'); },
           onCancel: () => { console.log('Cancelled!'); }
       });
--}}

<style>
    /* Overlay untuk menutupi seluruh layar */
    .notification-card-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.4);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 1050;
        visibility: hidden;
        opacity: 0;
        transition: visibility 0s, opacity 0.3s ease;
    }

    /* Tampilkan overlay saat ada kelas 'show' */
    .notification-card-overlay.show {
        visibility: visible;
        opacity: 1;
    }

    /* Styling dasar card notifikasi (modal-content Bootstrap) */
    .notification-card {
        font-family: 'Ancizar Serif', serif; /* Terapkan font ke seluruh card */
        background: white;
        border-radius: 0.75rem;
        transform: translateY(20px);
        transition: transform 0.3s ease;
        max-width: 400px;
        width: 90%;
        /* Hapus padding dari sini, akan diatur oleh .notification-main-layout */
    }

    /* Efek slide-up saat overlay muncul */
    .notification-card-overlay.show .notification-card {
        transform: translateY(0);
    }

    /* --- LAYOUT 2 KOLOM UTAMA NOTIFIKASI --- */
    /* Wrapper utama untuk layout 2 kolom: Ikon Kiri, Konten Kanan */
    .notification-main-layout {
        padding: 1rem 1rem 0 1rem; /* Padding keseluruhan untuk konten di dalam card, atas-bawah dan samping */
        gap: 15px; /* Jarak antara kolom ikon dan kolom konten kanan */
        /* d-flex align-items-start sudah ada di HTML */
    }

    /* Kolom Kiri: Hanya Ikon Notifikasi */
    .notification-icon-column {
        flex-shrink: 0; /* Pastikan kolom ikon tidak menyusut */
        padding-top: 0.25rem; /* Sedikit padding atas agar ikon sejajar visual dengan teks judul */
    }

    /* Styling ikon notifikasi */
    .notification-modal-icon {
        font-size: 1.5rem; /* Ukuran ikon sesuai permintaan */
        line-height: 1; /* Dipertahankan jika Anda merasa perlu */
        /* flex-shrink: 0 sudah diatur di .notification-icon-column */
        background: #EAE4D5;
        padding: 0.5rem 0.3rem 0.3rem 0.3rem;
        border-radius: 0.5rem;
    }

    /* Kolom Kanan: Wrapper untuk Judul, Pesan, dan Tombol */
    .notification-content-column {
        flex-grow: 1; /* Memungkinkan kolom ini mengambil sisa ruang */
    }

    /* Bagian Atas Kanan: Judul & Tombol Close */
    .notification-header-right {
        /* d-flex justify-content-between align-items-start sudah ada di HTML */
        padding-bottom: 0.5rem; /* Jarak di bawah judul sebelum pesan */
        border-bottom: none; /* Pastikan tidak ada border di sini */
    }

    /* Styling teks di header modal (wrapper judul) */
    .notification-modal-text {
        font-family: 'Ancizar Serif', serif;
        flex-grow: 1; /* Ambil sisa ruang */
        padding-right: 10px; /* Jarak antara judul dan tombol close */
    }

    /* Styling judul notifikasi */
    .notification-modal-title {
        font-family: 'Ancizar Serif', serif;
        font-size: 1.25rem;
        font-weight: bold;
        margin-bottom: 0; /* Hapus margin-bottom default h5 */
        color: #333;
    }

    /* Penyesuaian body modal untuk pesan */
    .notification-modal-body {
        font-family: 'Ancizar Serif', serif;
        padding: 0; /* Hapus padding yang diwarisi dari modal-body Bootstrap */
        text-align: left;
        padding-bottom: 1rem; /* Jarak di bawah pesan sebelum footer/tombol */
    }

    /* Styling pesan notifikasi */
    .notification-modal-message {
        font-family: 'Ancizar Serif', serif;
        font-size: 0.9375rem; /* Sekitar 15px */
        color: #666;
        line-height: 1.4;
        margin-bottom: 0; /* Hapus margin-bottom default p */
    }

    /* Penyesuaian footer modal untuk tombol aksi */
    .notification-modal-footer {
        padding: 0; /* Hapus padding default Bootstrap */
        justify-content: flex-end; /* Tombol di kanan */
        border-top: none; /* Hapus border atas bawaan Bootstrap */
        /* Padding tambahan untuk footer di bagian bawah card */
        padding-bottom: 1rem; /* Sesuaikan agar sesuai dengan padding .notification-main-layout */
        padding-top: 0.5rem;
        /* d-flex sudah ada di HTML */
    }

    /* Styling tombol NO */
    .btn-custom-no {
        font-family: 'Ancizar Serif', serif;
        background-color: #fff;
        color: #333;
        border: 1px solid #000;
        padding: 0.5rem 0.5rem;
        border-radius: 0.5rem;
        font-size: 1rem;
        min-width: 100px;
        transition: background-color 0.2s, border-color 0.2s;
        margin: 0rem 0.5rem 0rem 0rem; /* Jarak kanan antar tombol */
    }
    .btn-custom-no:hover {
        background-color: #eee;
        color: #333;
        border-color: #000;
    }

    /* Styling tombol YES */
    .btn-custom-yes {
        font-family: 'Ancizar Serif', serif;
        background-color: #000;
        color: #fff;
        border: 1px solid #000;
        padding: 0.5rem 0.5rem;
        border-radius: 0.5rem;
        font-size: 1rem;
        min-width: 100px;
        transition: background-color 0.2s, border-color 0.2s;
    }
    .btn-custom-yes:hover {
        background-color: #333;
        color: #fff;
        border-color: #333;
    }
</style>

{{-- Struktur HTML notifikasi --}}
{{-- Kontainer overlay. Defaultnya tersembunyi dengan visibility/opacity --}}
<div class="notification-card-overlay" id="globalNotificationOverlay">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 400px; margin: 0;">
        <div class="modal-content notification-card">
            {{-- Wrapper utama untuk layout 2 kolom: Ikon Kiri, Konten Kanan --}}
            <div class="notification-main-layout d-flex align-items-start">

                {{-- Kolom Kiri: Hanya Ikon Notifikasi --}}
                <div class="notification-icon-column">
                    <div class="notification-modal-icon" id="globalNotificationIcon"></div>
                </div>

                {{-- Kolom Kanan: Seluruh Konten Notifikasi (Judul, Close Btn, Pesan, Tombol Aksi) --}}
                <div class="notification-content-column flex-grow-1">

                    {{-- Bagian Atas Kanan: Judul dan Tombol Close --}}
                    <div class="notification-header-right d-flex justify-content-between align-items-start">
                        {{-- Area teks untuk judul --}}
                        <div class="notification-modal-text">
                            <h5 class="modal-title notification-modal-title" id="globalNotificationTitle"></h5>
                        </div>
                        {{-- Tombol close standar Bootstrap --}}
                        <button type="button" class="btn-close" aria-label="Close"></button>
                    </div>

                    {{-- Bagian Tengah Kanan: Body Notifikasi (Pesan Detail) --}}
                    {{-- Kelas modal-body tetap, namun padding diatur ulang di CSS --}}
                    <div class="modal-body notification-modal-body">
                        <p class="notification-modal-message" id="globalNotificationMessage"></p>
                    </div>

                    {{-- Bagian Bawah Kanan: Footer Notifikasi (Tombol Aksi YES/NO) --}}
                    {{-- Kelas modal-footer tetap, namun padding diatur ulang di CSS --}}
                    <div class="modal-footer notification-modal-footer" id="globalNotificationActions">
                        <button type="button" class="btn btn-custom-no" id="globalNotificationCancelBtn">NO</button>
                        <button type="button" class="btn btn-custom-yes" id="globalNotificationConfirmBtn">YES</button>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

{{-- Skrip JavaScript untuk mengelola notifikasi --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const overlay = document.getElementById('globalNotificationOverlay');
        const closeBtn = overlay.querySelector('.btn-close');
        const iconElement = document.getElementById('globalNotificationIcon');
        const titleElement = document.getElementById('globalNotificationTitle');
        const messageElement = document.getElementById('globalNotificationMessage');
        const actionsDiv = document.getElementById('globalNotificationActions');
        const confirmBtn = document.getElementById('globalNotificationConfirmBtn');
        const cancelBtn = document.getElementById('globalNotificationCancelBtn');

        let onConfirmCallback = null;
        let onCancelCallback = null;

        /**
         * Menampilkan card notifikasi.
         * @param {object} options - Objek konfigurasi notifikasi.
         * @param {string} options.type - Tipe notifikasi ('info', 'error', 'confirmation', 'success').
         * @param {string} options.title - Judul notifikasi.
         * @param {string} options.message - Pesan detail notifikasi.
         * @param {boolean} options.hasActions - True jika ada tombol YES/NO, false jika tidak.
         * @param {function} [options.onConfirm] - Callback saat tombol YES ditekan.
         * @param {function} [options.onCancel] - Callback saat tombol NO/Close ditekan atau diklik di luar.
         */
        window.showNotificationCard = function(options) {
            // Default options untuk notifikasi
            const defaults = {
                type: 'info',
                title: '',
                message: '',
                hasActions: false,
                onConfirm: null,
                onCancel: null
            };
            const settings = Object.assign({}, defaults, options); // Gabungkan default dengan opsi yang diberikan

            // Update konten notifikasi berdasarkan settings
            iconElement.className = 'notification-modal-icon ' + settings.type;
            // Pilih emoji ikon berdasarkan tipe notifikasi
            switch (settings.type) {
                case 'error': iconElement.textContent = '😞'; break;
                case 'confirmation': iconElement.textContent = '❓'; break;
                case 'info': iconElement.textContent = '💡'; break;
                case 'success': iconElement.textContent = '✅'; break;
                default: iconElement.textContent = 'ℹ️'; // Default
            }
            titleElement.textContent = settings.title;
            messageElement.textContent = settings.message;

            // Tampilkan atau sembunyikan area tombol aksi (YES/NO)
            if (settings.hasActions) {
                actionsDiv.style.display = 'flex'; // Bootstrap modal-footer menggunakan flex
                onConfirmCallback = settings.onConfirm; // Simpan callback
                onCancelCallback = settings.onCancel;
            } else {
                actionsDiv.style.display = 'none';
                onConfirmCallback = null; // Hapus callback jika tidak ada aksi
                onCancelCallback = null;
            }

            // Tampilkan overlay notifikasi dengan menambahkan kelas 'show'
            overlay.classList.add('show');
        };

        /**
         * Menyembunyikan card notifikasi.
         */
        window.hideNotificationCard = function() {
            // Sembunyikan overlay dengan menghapus kelas 'show'
            overlay.classList.remove('show');
            // Reset callback setelah disembunyikan
            onConfirmCallback = null;
            onCancelCallback = null;
        };

        // --- Event Listeners untuk tombol-tombol di notifikasi ---

        // Event listener untuk tombol close (X)
        closeBtn.addEventListener('click', function() {
            hideNotificationCard(); // Sembunyikan notifikasi
            if (onCancelCallback) { // Panggil callback pembatalan jika ada
                onCancelCallback();
            }
        });

        // Event listener untuk tombol NO
        cancelBtn.addEventListener('click', function() {
            hideNotificationCard(); // Sembunyikan notifikasi
            if (onCancelCallback) { // Panggil callback pembatalan jika ada
                onCancelCallback();
            }
        });

        // Event listener untuk tombol YES
        confirmBtn.addEventListener('click', function() {
            hideNotificationCard(); // Sembunyikan notifikasi
            if (onConfirmCallback) { // Panggil callback konfirmasi jika ada
                onConfirmCallback();
            }
        });

        // Event listener untuk menutup notifikasi saat mengklik di luar area card
        // Ini hanya berlaku jika notifikasi TIDAK memiliki tombol aksi (hasActions: false)
        overlay.addEventListener('click', function(event) {
            const modalContent = overlay.querySelector('.modal-content');
            // Jika yang diklik adalah overlay itu sendiri (bukan modal-content atau anaknya)
            // DAN tombol aksi tidak ditampilkan (artinya ini notifikasi info/error/success biasa)
            if (!modalContent.contains(event.target) && actionsDiv.style.display === 'none') {
                hideNotificationCard(); // Sembunyikan notifikasi
                if (onCancelCallback) { // Panggil callback pembatalan
                    onCancelCallback();
                }
            }
        });
    });
</script>