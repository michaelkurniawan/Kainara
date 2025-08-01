<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Refund;
use App\Models\Product; // Still needed for stock management in case of full refund
use App\Models\ProductVariant; // Still needed for stock management in case of full refund
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage; // Make sure Storage is imported
use Illuminate\Validation\ValidationException;
use Stripe\Stripe; // Still needed for showPaymentForm if it was the original Stripe controller
use Stripe\StripeClient; // Still needed for showPaymentForm if it was the original Stripe controller
use Exception; // Still needed for general exception handling
use Illuminate\Support\Str; // Make sure Str is imported for string manipulation

class RefundController extends Controller
{
    // Note: Stripe properties are commented out as per the new flow where Stripe API calls
    // are handled by the AdminRefundController.
    // However, if this controller also handles initial Stripe payments (like StripePaymentController),
    // then these properties and the __construct method would be necessary.
    // For a dedicated RefundController (user-facing request only), they are not.
    // protected $stripe;

    public function __construct()
    {
        //
    }

    public function showRefundForm(Order $order)
    {
        if (Auth::id() !== $order->user_id) {
            return redirect()->route('my.orders')->with('error', 'Anda tidak memiliki izin untuk mengakses pesanan ini.');
        }

        if ($order->status !== 'Delivered') {
            return redirect()->route('my.orders')->with('error', 'Refund hanya dapat diajukan untuk pesanan yang telah dikirim.');
        }

        $payment = $order->payment()->with('refunds')->first();

        if (!$payment || !$payment->stripe_charge_id || $payment->status !== 'succeeded') {
            return redirect()->route('my.orders')->with('error', 'Pembayaran untuk pesanan ini tidak memenuhi syarat untuk refund penuh. (Status pembayaran: ' . ($payment->status ?? 'Tidak ada') . ')');
        }

        $totalRefundedAmount = $payment->refunds->where('status', 'succeeded')->sum('refunded_amount');
        $fullOrderAmount = $payment->amount_paid;

        $availableForRefund = $fullOrderAmount - $totalRefundedAmount;

        if (abs($availableForRefund) < 0.01) {
            return redirect()->route('my.orders')->with('info', 'Pesanan ini sudah direfund sepenuhnya.');
        }

        return view('refund.form', compact('order', 'payment', 'availableForRefund'));
    }

    public function requestRefund(Request $request, Order $order)
    {
        if (Auth::id() !== $order->user_id) {
            return response()->json(['success' => false, 'message' => 'Akses tidak sah.'], 403);
        }

        if ($order->status !== 'Delivered') {
            return response()->json(['success' => false, 'message' => 'Refund hanya dapat diajukan untuk pesanan yang telah dikirim.'], 400);
        }

        $payment = $order->payment()->with('refunds')->first();
        if (!$payment || !$payment->stripe_charge_id || $payment->status !== 'succeeded') {
            return response()->json(['success' => false, 'message' => 'Pembayaran untuk pesanan ini tidak memenuhi syarat untuk refund penuh.'], 400);
        }

        try {
            $validated = $request->validate([
                'reason' => 'required|string|max:255',
                'refund_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);
            $userProvidedReason = $validated['reason'];

        } catch (ValidationException $e) {
            return response()->json(['success' => false, 'message' => 'Validasi Gagal', 'errors' => $e->errors()], 422);
        }

        $imagePath = null;
        if ($request->hasFile('refund_image')) {
            try {
                $imagePath = $request->file('refund_image')->store('public/refund_images');
            } catch (\Exception $e) {
                Log::error('Gagal mengunggah gambar refund: ' . $e->getMessage(), ['order_id' => $order->id]);
                return response()->json(['success' => false, 'message' => 'Gagal mengunggah gambar: ' . $e->getMessage()], 500);
            }
        }

        $amountToRefund = $payment->amount_paid;

        if ($amountToRefund <= 0) {
            return response()->json(['success' => false, 'message' => 'Jumlah refund tidak valid. Tidak ada jumlah yang harus direfund.'], 400);
        }

        DB::beginTransaction();
        try {
            $refund = Refund::create([
                'payment_id' => $payment->id,
                'stripe_refund_id' => null,
                'refunded_amount' => $amountToRefund,
                'reason' => $userProvidedReason,
                'refund_image' => $imagePath,
                'refunded_at' => null,
                'status' => 'pending',
                'admin_notes' => 'Permintaan refund diajukan oleh pengguna.',
            ]);

            $payment->status = 'refund_pending';
            $payment->save();

            $order->status = 'Refund Pending';
            $order->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Permintaan refund Anda telah diajukan dan sedang menunggu tinjauan admin.',
                'redirect_url' => route('profile.index', ['#order-history']) // Redirect to order history
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal mengajukan permintaan refund oleh pengguna: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'order_id' => $order->id,
                'payment_id' => $payment->id,
            ]);
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat mengajukan permintaan refund: ' . $e->getMessage()], 500);
        }
    }
}