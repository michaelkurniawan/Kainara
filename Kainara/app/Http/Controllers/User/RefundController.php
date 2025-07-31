<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Refund;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Exception;

class RefundController extends Controller
{
    protected $stripe;

    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
        $this->stripe = new \Stripe\StripeClient(config('services.stripe.secret'));
    }

    /**
     * Memproses permintaan refund untuk pesanan.
     * Metode ini akan memperbarui record pembayaran dan refund, tidak langsung order.
     *
     * @param Request $request
     * @param Order $order
     * @return \Illuminate\Http\JsonResponse
     */
    public function requestRefund(Request $request, Order $order)
    {
        // 1. Otorisasi: Pastikan pengguna adalah pemilik pesanan
        if (Auth::id() !== $order->user_id) {
            return response()->json(['success' => false, 'message' => 'Akses tidak sah.'], 403);
        }

        // 2. Dapatkan pembayaran terkait untuk pesanan ini
        // Kita perlu eager load refunds di sini untuk mendapatkan jumlah yang sudah direfund
        $payment = $order->payment()->with('refunds')->first();

        // Validasi apakah pembayaran ada dan memiliki Stripe Charge ID (diperlukan untuk refund)
        if (!$payment || !$payment->stripe_charge_id) {
            return response()->json(['success' => false, 'message' => 'Record pembayaran tidak ditemukan atau belum berhasil (tidak ada ID Charge Stripe).'], 404);
        }

        // 3. Validasi Status Pembayaran untuk Refund
        // Pembayaran harus 'succeeded' atau 'refund_pending'
        if ($payment->status !== 'succeeded' && $payment->status !== 'refund_pending') {
             return response()->json(['success' => false, 'message' => 'Status pembayaran tidak memungkinkan refund (saat ini: ' . $payment->status . ').'], 400);
        }

        // Hitung total jumlah yang sudah direfund untuk pembayaran ini
        $totalRefundedAmount = $payment->refunds->where('status', 'succeeded')->sum('refunded_amount');
        $availableForRefund = $payment->amount_paid - $totalRefundedAmount;

        // Cek jika ada jumlah yang tersisa untuk direfund (beri toleransi kecil untuk masalah floating point)
        if ($availableForRefund <= 0.01) {
            return response()->json(['success' => false, 'message' => 'Pesanan ini sudah direfund sepenuhnya atau tidak ada lagi jumlah yang tersedia untuk direfund.'], 400);
        }

        // 4. Validasi Data Permintaan untuk jumlah dan alasan
        $validated = $request->validate([
            'amount' => 'nullable|numeric|min:0.01', // Jumlah dalam mata uang penuh (IDR)
            'reason' => 'nullable|string|max:255',
        ]);

        $requestedRefundAmount = $validated['amount'] ? (float)$validated['amount'] : null; // Simpan sebagai float untuk perbandingan
        $reason = $validated['reason'] ?? null;

        // Jika jumlah spesifik diminta, pastikan tidak melebihi yang tersedia
        if ($requestedRefundAmount !== null && $requestedRefundAmount > $availableForRefund + 0.01) { // Tambahkan buffer kecil untuk perbandingan float
            return response()->json(['success' => false, 'message' => 'Jumlah refund yang diminta (' . number_format($requestedRefundAmount, 0, ',', '.') . ') melebihi jumlah yang tersedia untuk direfund (' . number_format($availableForRefund, 0, ',', '.') . ').'], 400);
        }

        // Tentukan jumlah sebenarnya yang akan dikirim ke Stripe (dalam cent)
        $amountToSendToStripe = $requestedRefundAmount ? (int)($requestedRefundAmount * 100) : null;
        // Jika amount null, Stripe akan mengembalikan sisa jumlah penuh

        try {
            // 5. Buat Refund melalui Stripe API
            $params = [
                'charge' => $payment->stripe_charge_id,
            ];

            if ($amountToSendToStripe !== null) {
                $params['amount'] = $amountToSendToStripe; // Kirim dalam cent
            }
            if ($reason) {
                $params['reason'] = $reason;
            }

            $stripeRefund = $this->stripe->refunds->create($params);

            // 6. Catat Refund di Database Lokal
            $refund = Refund::create([
                'payment_id' => $payment->id,
                'stripe_refund_id' => $stripeRefund->id,
                'refunded_amount' => $stripeRefund->amount / 100, // Simpan dalam mata uang penuh (IDR)
                'reason' => $reason,
                'refunded_at' => $stripeRefund->status === 'succeeded' ? now() : null,
                'status' => $stripeRefund->status, // e.g., 'succeeded', 'pending', 'failed'
            ]);

            // 7. Perbarui Status Pembayaran berdasarkan refund
            // Jika refund Stripe berhasil DAN sisa jumlah yang bisa direfund secara efektif nol,
            // maka status pembayaran dapat disetel menjadi 'refunded'.
            // Jika tidak, itu mungkin tetap 'succeeded' atau menjadi 'refund_pending'.
            if ($stripeRefund->status === 'succeeded') {
                // Hitung ulang total yang direfund setelah refund berhasil saat ini
                $newTotalRefundedAmount = $totalRefundedAmount + ($stripeRefund->amount / 100);
                if ($newTotalRefundedAmount >= $payment->amount_paid - 0.01) { // Periksa apakah efektif sepenuhnya direfund
                    $payment->status = 'refunded';
                } else {
                    $payment->status = 'partially_refunded'; // Status baru untuk kejelasan
                }
                $message = 'Refund berhasil diproses.';
            } elseif ($stripeRefund->status === 'pending') {
                $payment->status = 'refund_pending';
                $message = 'Permintaan refund sedang diproses.';
            } else {
                $payment->status = 'refund_failed';
                $message = 'Gagal memproses refund: ' . ($stripeRefund->failure_reason ?? 'Alasan tidak diketahui');
            }
            $payment->save();

            // Sesuai permintaan Anda, tidak ada modifikasi langsung pada $order->status di sini.
            // Status utama order (misalnya, 'Delivered', 'Completed') akan tetap sama.
            // Informasi refund akan disimpulkan dari status pembayaran dan record refund.

            return response()->json(['success' => true, 'message' => $message]);

        } catch (Exception $e) {
            Log::error('Stripe Refund gagal untuk order ' . $order->id . ': ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'order_id' => $order->id,
                'payment_id' => $payment->id,
                'charge_id' => $payment->stripe_charge_id ?? 'N/A'
            ]);
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat mengajukan refund: ' . $e->getMessage()], 500);
        }
    }
}