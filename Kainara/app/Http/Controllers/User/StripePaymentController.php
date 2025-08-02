<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Stripe\Stripe; // Import kelas utama Stripe
use Stripe\StripeClient; // Import StripeClient untuk melakukan panggilan API
use App\Models\Order; // Import model Order
use App\Models\Payment; // Import model Payment
use Illuminate\Support\Facades\Log; // Untuk logging kesalahan
use Illuminate\Support\Facades\Auth; // Untuk mengakses pengguna yang terautentikasi (opsional)
use Exception; // Untuk menangani pengecualian umum

class StripePaymentController extends Controller
{
    protected $stripe;

    /**
     * Konstruktor untuk menginisialisasi StripeClient dengan kunci rahasia Anda.
     */
    public function __construct()
    {
        // Tetapkan kunci API Stripe secara global dari file config/services.php Anda
        Stripe::setApiKey(config('services.stripe.secret'));

        // Buat instance StripeClient
        $this->stripe = new \Stripe\StripeClient(config('services.stripe.secret'));
    }

    /**
     * Menampilkan formulir pembayaran Stripe dan membuat/mengambil PaymentIntent.
     * Metode ini dipanggil setelah proses checkout awal jika 'credit_card' dipilih.
     *
     * @param \App\Models\Order $order
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function showPaymentForm(Order $order)
    {
        // Pertama, periksa apakah pesanan benar-benar menunggu pembayaran
        if ($order->status !== 'Awaiting Payment') {
            return redirect()->route('order.details', $order->id)->with('error', 'Pesanan ini tidak menunggu pembayaran atau sudah diproses.');
        }

        // Coba temukan record pembayaran yang sudah ada untuk pesanan ini untuk melihat apakah PaymentIntent sudah dibuat
        $payment = Payment::where('order_id', $order->id)->first();

        try {
            $clientSecret = null;
            // Jika tidak ada record pembayaran atau tidak memiliki Stripe Payment Intent ID, buat yang baru
            if (!$payment || !$payment->stripe_payment_intent_id) {
                $paymentIntent = $this->stripe->paymentIntents->create([
                    'amount' => (int)($order->grand_total * 100), // Jumlah harus dalam cent (unit mata uang terkecil)
                    'currency' => 'idr', // Tentukan mata uang, misalnya, 'idr' untuk Rupiah Indonesia
                    'metadata' => ['order_id' => $order->id], // Lampirkan ID order untuk referensi di Stripe
                    'description' => 'Pembayaran untuk Order #' . $order->id,
                    // Kirim resi ke email pengguna yang terautentikasi atau email asli di order
                    'receipt_email' => Auth::check() ? Auth::user()->email : $order->original_user_email,
                    'automatic_payment_methods' => ['enabled' => true], // Aktifkan metode pembayaran otomatis untuk integrasi yang lebih sederhana
                ]);

                // Simpan detail PaymentIntent baru di database lokal Anda
                Payment::create([
                    'order_id' => $order->id,
                    'stripe_payment_intent_id' => $paymentIntent->id,
                    'status' => $paymentIntent->status, // Status awal dari Stripe (misalnya, 'requires_payment_method')
                    'amount_paid' => $order->grand_total, // Simpan jumlah, akan dikonfirmasi setelah berhasil
                    'currency' => strtoupper($paymentIntent->currency),
                    'payment_method_type' => 'card', // Diasumsikan rute ini untuk pembayaran kartu
                    'card_details' => null,             // Ini akan diisi setelah pembayaran berhasil
                    'payment_method_details' => null,   // Ini akan diisi setelah pembayaran berhasil
                ]);
                $clientSecret = $paymentIntent->client_secret;

            } else {
                // Jika record pembayaran dan Stripe Payment Intent ID sudah ada, ambil PaymentIntent yang ada dari Stripe
                $paymentIntent = $this->stripe->paymentIntents->retrieve($payment->stripe_payment_intent_id);

                // Jika PaymentIntent yang ada sudah sukses atau sedang diproses, arahkan ke halaman sukses
                if (in_array($paymentIntent->status, ['succeeded', 'processing'])) {
                    // Flash a success notification before redirecting
                    session()->flash('notification', [
                        'type' => 'success',
                        'title' => 'Payment Already Processed',
                        'message' => 'This order has already been paid successfully.',
                        'hasActions' => false,
                        'redirect_url' => route('my.orders') // Provide redirect for JS
                    ]);
                    return redirect()->route('my.orders'); // Redirect for server-side
                }

                // Jika jumlah PaymentIntent tidak cocok dengan grand_total order saat ini (misalnya, perubahan harga), perbarui
                if ($paymentIntent->amount !== (int)($order->grand_total * 100)) {
                    $paymentIntent = $this->stripe->paymentIntents->update(
                        $payment->stripe_payment_intent_id,
                        ['amount' => (int)($order->grand_total * 100)]
                    );
                    // Juga perbarui amount_paid di record Pembayaran lokal Anda
                    $payment->amount_paid = $order->grand_total;
                    $payment->save();
                }
                $clientSecret = $paymentIntent->client_secret;
            }

            // Teruskan data order, kunci publik Stripe, dan client secret ke view pembayaran
            return view('payment.stripe', [
                'order' => $order,
                'stripePublicKey' => config('services.stripe.key'),
                'clientSecret' => $clientSecret,
            ]);

        } catch (Exception $e) {
            // Log setiap kesalahan yang terjadi selama pembuatan/pengambilan PaymentIntent
            Log::error('Stripe Payment Intent creation/retrieval failed: ' . $e->getMessage(), ['order_id' => $order->id]);
            return redirect()->route('checkout.show')->with('error', 'Gagal memulai pembayaran. Silakan coba lagi. ' . $e->getMessage());
        }
    }

    /**
     * Mengonfirmasi pembayaran setelah JavaScript Stripe sisi klien menyelesaikan alur pembayaran.
     * Metode ini biasanya dipanggil melalui permintaan AJAX dari frontend.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Order $order
     * @return \Illuminate\Http\JsonResponse
     */
    public function confirmPayment(Request $request, Order $order)
    {
        // Validasi permintaan masuk untuk detail Payment Intent Stripe yang diperlukan
        $request->validate([
            'payment_intent_id' => 'required|string',
            'payment_intent_status' => 'required|string', // Ini adalah status yang dilaporkan oleh Stripe JS sisi klien
        ]);

        try {
            $paymentIntentId = $request->input('payment_intent_id');
            $reportedPaymentIntentStatus = $request->input('payment_intent_status');

            // Ambil PaymentIntent dari API Stripe untuk mendapatkan status dan detail otentik
            // Penting: Perluas 'latest_charge' untuk mendapatkan ID Charge yang terkait
            $stripePaymentIntent = $this->stripe->paymentIntents->retrieve($paymentIntentId, ['expand' => ['latest_charge']]);

            // Temukan record Payment lokal yang sesuai di database Anda
            $payment = Payment::where('order_id', $order->id)
                             ->where('stripe_payment_intent_id', $paymentIntentId)
                             ->first();

            if (!$payment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Record pembayaran tidak ditemukan untuk pesanan ini dan ID intent di sistem kami.',
                    'redirect_url' => route('checkout.show') // Arahkan ke checkout jika record lokal tidak ada
                ], 404);
            }

            // Perbarui record Payment lokal dengan status terbaru dari Stripe
            $payment->status = $stripePaymentIntent->status;
            // Tangkap tipe metode pembayaran pertama yang digunakan untuk transaksi
            $payment->payment_method_type = $stripePaymentIntent->payment_method_types[0] ?? 'card';

            // Ekstrak dan simpan detail metode pembayaran, terutama untuk pembayaran kartu
            if (isset($stripePaymentIntent->charges->data[0]->payment_method_details)) {
                $payment->payment_method_details = $stripePaymentIntent->charges->data[0]->payment_method_details;
                if ($payment->payment_method_details['type'] === 'card') {
                    // Simpan detail kartu spesifik untuk pencatatan
                    $payment->card_details = [
                        'last4' => $payment->payment_method_details['card']['last4'],
                        'brand' => $payment->payment_method_details['card']['brand'],
                        'exp_month' => $payment->payment_method_details['card']['exp_month'],
                        'exp_year' => $payment->payment_method_details['card']['exp_year'],
                        'funding' => $payment->payment_method_details['card']['funding'] ?? null,
                        'country' => $payment->payment_method_details['card']['country'] ?? null,
                    ];
                }
            }

            // --- PENAMBAHAN PENTING: Simpan stripe_charge_id ---
            // Charge ID diperlukan untuk memproses refund di Stripe
            if ($stripePaymentIntent->latest_charge && $stripePaymentIntent->latest_charge->id) {
                $payment->stripe_charge_id = $stripePaymentIntent->latest_charge->id;
            }
            // --- AKHIR PENAMBAHAN PENTING ---

            // Tangani berbagai status PaymentIntent yang dilaporkan oleh Stripe
            if ($stripePaymentIntent->status === 'succeeded') {
                $payment->paid_at = now(); // Atur timestamp pembayaran
                $order->status = 'Order Confirmed'; // Perbarui status order menjadi dikonfirmasi
                $order->save();
                $payment->save(); // Simpan record pembayaran yang diperbarui

                // **CHANGE START: Use session flash for notification**
                session()->flash('notification', [
                    'type' => 'success',
                    'title' => 'Payment Successful!',
                    'message' => 'Your payment for Order #' . $order->id . ' was successful. Your order has been confirmed.',
                    'hasActions' => false, // No actions needed for a simple success
                    'redirect_url' => route('my.orders') // Redirect URL after notification dismissal
                ]);
                return response()->json([
                    'success' => true,
                    'message' => 'Payment successful!',
                    'redirect_url' => route('my.orders') // We still send this for the JS to handle after notification
                ]);
                // **CHANGE END**

            } elseif ($stripePaymentIntent->status === 'requires_action' || $stripePaymentIntent->status === 'requires_source_action') {
                $payment->save();
                // For 'requires_action', Stripe.js usually handles the necessary redirection (e.g.: 3D Secure)
                // This JSON response tells the frontend that further action is needed and provides a potential redirect URL
                return response()->json([
                    'success' => false,
                    'message' => 'Pembayaran memerlukan tindakan tambahan. Harap selesaikan di Stripe.',
                    // Fallback redirect if no specific action URL
                    'redirect_url' => $stripePaymentIntent->next_action->redirect_to_url->url ?? route('my.orders')
                ]);
            } else {
                // Handle all other unsuccessful statuses (e.g., 'failed', 'canceled', 'requires_payment_method' if not handled earlier)
                $order->status = 'Canceled'; // Mark order as canceled due to payment failure
                $order->save();
                $payment->save(); // Save final status of the payment record

                // **CHANGE START: Use session flash for notification**
                session()->flash('notification', [
                    'type' => 'error',
                    'title' => 'Payment Failed',
                    'message' => 'Your payment could not be completed. Please try again or use a different payment method. Status: ' . $stripePaymentIntent->status,
                    'hasActions' => false,
                    'redirect_url' => route('order.fail', $order->id) // Redirect URL after notification dismissal
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Payment failed or incomplete: ' . $stripePaymentIntent->status,
                    'redirect_url' => route('order.fail', $order->id) // We still send this for the JS to handle after notification
                ]);
                // **CHANGE END**
            }
        } catch (Exception $e) {
            // Catch any exception during the confirmation process (e.g., network issues, invalid API key)
            Log::error('Stripe Payment confirmation failed: ' . $e->getMessage(), [
                'payment_intent_id' => $request->input('payment_intent_id'),
                'order_id' => $order->id,
                'request_data' => $request->all() // Include request data for debugging
            ]);
            // **CHANGE START: Use session flash for notification**
            session()->flash('notification', [
                'type' => 'error',
                'title' => 'Payment Error',
                'message' => 'An unexpected error occurred during payment confirmation. Please try again. ' . $e->getMessage(),
                'hasActions' => false,
                'redirect_url' => route('checkout.show') // Redirect URL after notification dismissal
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan tak terduga selama konfirmasi pembayaran. Silakan coba lagi.',
                'redirect_url' => route('checkout.show') // We still send this for the JS to handle after notification
            ], 500); // Internal Server Error Status
            // **CHANGE END**
        }
    }
}