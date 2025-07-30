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

        let currentOnConfirmCallback = null; // Use a distinct name for clarity
        let currentOnCancelCallback = null; // Use a distinct name for clarity

        // --- Helper function to clear previous listeners ---
        function clearButtonListeners() {
            confirmBtn.removeEventListener('click', handleConfirmClick);
            cancelBtn.removeEventListener('click', handleCancelClick);
            closeBtn.removeEventListener('click', handleCancelClick); // Also clear close button
            overlay.removeEventListener('click', handleOverlayClick);
        }

        // --- Event handler functions ---
        function handleConfirmClick() {
            hideNotificationCard(); // Hide first
            if (currentOnConfirmCallback) {
                console.log('Notification: Executing currentOnConfirmCallback.');
                currentOnConfirmCallback(); // Execute the specific callback
            }
        }

        function handleCancelClick() {
            hideNotificationCard(); // Hide first
            if (currentOnCancelCallback) {
                console.log('Notification: Executing currentOnCancelCallback.');
                currentOnCancelCallback(); // Execute the specific callback
            }
        }

        function handleOverlayClick(event) {
            const modalContent = overlay.querySelector('.modal-content');
            if (!modalContent.contains(event.target) && actionsDiv.style.display === 'none') {
                handleCancelClick(); // Treats outside click as cancel for non-actionable modals
            }
        }

        // --- BAGIAN 2: DEFINISIKAN FUNGSI GLOBAL showNotificationCard dan hideNotificationCard ---
        window.showNotificationCard = function(options) {
            console.log('showNotificationCard called with options:', options);

            // Clear any previously set listeners
            clearButtonListeners();

            const defaults = {
                type: 'info',
                title: '',
                message: '',
                hasActions: false,
                onConfirm: null,
                onCancel: null
            };
            const settings = Object.assign({}, defaults, options);

            iconElement.className = 'notification-modal-icon ' + settings.type;
            switch (settings.type) {
                case 'error': iconElement.textContent = '😞'; break;
                case 'confirmation': iconElement.textContent = '❓'; break;
                case 'info': iconElement.textContent = '💡'; break;
                case 'success': iconElement.textContent = '✅'; break;
                default: iconElement.textContent = 'ℹ️';
            }
            titleElement.textContent = settings.title;
            messageElement.textContent = settings.message;

            // Set dynamic text for buttons based on type if needed, or keep fixed
            if (settings.type === 'confirmation') {
                confirmBtn.textContent = 'YES';
                cancelBtn.textContent = 'NO';
            } else {
                confirmBtn.textContent = 'OK'; // Default for non-confirmation
            }

            if (settings.hasActions) {
                actionsDiv.style.display = 'flex';
                confirmBtn.style.display = 'inline-block';
                cancelBtn.style.display = 'inline-block';

                currentOnConfirmCallback = settings.onConfirm; // Store the specific callback
                currentOnCancelCallback = settings.onCancel;

                // Attach NEW listeners for this specific notification
                confirmBtn.addEventListener('click', handleConfirmClick);
                cancelBtn.addEventListener('click', handleCancelClick);

            } else {
                actionsDiv.style.display = 'none';
                confirmBtn.style.display = 'none';
                cancelBtn.style.display = 'none';

                currentOnConfirmCallback = null;
                currentOnCancelCallback = null;
            }

            // Always attach close button and overlay listeners for basic closing
            closeBtn.addEventListener('click', handleCancelClick); // Close button usually acts as cancel
            overlay.addEventListener('click', handleOverlayClick);

            overlay.classList.add('show');
        };

        window.hideNotificationCard = function() {
            console.log('hideNotificationCard called.');
            overlay.classList.remove('show');
            // Clear current callbacks only if needed,
            // they will be set again by the next showNotificationCard call.
            // currentOnConfirmCallback = null;
            // currentOnCancelCallback = null;
            clearButtonListeners(); // Ensure all listeners are removed after hiding
        };

        // --- BAGIAN 3: LOGIKA UNTUK MENAMPILKAN NOTIFIKASI DARI SESI FLASH ---
        @if(Session::has('notification'))
            const notificationData = @json(Session::get('notification'));
            console.log('Session notification detected:', notificationData);

            // Pass onConfirm/onCancel only if specifically needed for server-side
            // For simple "OK" messages, a simple hide is enough.
            window.showNotificationCard({
                type: notificationData.type || 'info',
                title: notificationData.title || 'Notification',
                message: notificationData.message || '',
                hasActions: notificationData.hasActions || false,
                onConfirm: notificationData.onConfirm ? () => { /* custom logic for flash confirm */ } : null,
                onCancel: notificationData.onCancel ? () => { /* custom logic for flash cancel */ } : null
            });

            // Customize button text for server-side notifications *after* showNotificationCard
            // This is to override the default "YES"/"NO" if needed for a simple "OK"
            if (!notificationData.hasActions) { // If it's a simple info/error/success from server
                 // Make sure only a single "OK" button appears and functions as close
                if (confirmBtn) {
                    confirmBtn.textContent = 'OK';
                    confirmBtn.style.display = 'inline-block';
                    // Re-attach listener to simply hide the card
                    confirmBtn.removeEventListener('click', handleConfirmClick); // Remove old one
                    confirmBtn.addEventListener('click', window.hideNotificationCard); // Just hide
                }
                if (cancelBtn) {
                    cancelBtn.style.display = 'none';
                }
            } else { // If hasActions IS true from session (e.g., login verification)
                if (confirmBtn) {
                    confirmBtn.textContent = 'OK'; // Force to OK
                    confirmBtn.style.display = 'inline-block';
                    confirmBtn.removeEventListener('click', handleConfirmClick); // Remove default
                    confirmBtn.addEventListener('click', handleConfirmClick); // Re-attach but it's now OK

                }
                if (cancelBtn) {
                    cancelBtn.style.display = 'none'; // Only OK button for login verification
                }
            }
        @endif
    });
</script>