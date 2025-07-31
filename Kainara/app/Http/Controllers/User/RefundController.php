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
use Illuminate\Validation\ValidationException;
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
     * Show the refund confirmation form for a FULL refund.
     *
     * @param Order $order
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function showRefundForm(Order $order)
    {
        // 1. Authorization: Ensure the user is the owner of the order
        if (Auth::id() !== $order->user_id) {
            return redirect()->route('my.orders')->with('error', 'Anda tidak memiliki izin untuk mengakses pesanan ini.');
        }

        // 2. Validate order status: Must be Delivered
        if ($order->status !== 'Delivered') {
            return redirect()->route('my.orders')->with('error', 'Refund hanya dapat diajukan untuk pesanan yang telah dikirim.');
        }

        $payment = $order->payment()->with('refunds')->first();

        // Validate if payment exists, has a Stripe Charge ID, and is in 'succeeded' status
        // For full refund only, we only allow if it's 'succeeded' and not already refunded/partially refunded
        if (!$payment || !$payment->stripe_charge_id || $payment->status !== 'succeeded') {
            return redirect()->route('my.orders')->with('error', 'Pembayaran untuk pesanan ini tidak memenuhi syarat untuk refund penuh. (Status pembayaran: ' . ($payment->status ?? 'Tidak ada') . ')');
        }

        // Calculate total already refunded for this payment
        $totalRefundedAmount = $payment->refunds->where('status', 'succeeded')->sum('refunded_amount');
        $fullOrderAmount = $payment->amount_paid;

        // Check if the order has already been fully refunded
        if (abs($fullOrderAmount - $totalRefundedAmount) < 0.01) {
            return redirect()->route('my.orders')->with('info', 'Pesanan ini sudah direfund sepenuhnya.');
        }

        // Pass the order and full refund amount to the view for confirmation
        // $amountToRefund should generally be $fullOrderAmount here due to earlier checks.
        $amountToRefund = $fullOrderAmount;

        return view('refund.form', compact('order', 'payment', 'amountToRefund'));
    }

    /**
     * Processes the FULL refund request for an order.
     * This method will update the payment and order records to 'Refunded'.
     *
     * @param Request $request
     * @param Order $order
     * @return \Illuminate\Http\JsonResponse
     */
    public function requestRefund(Request $request, Order $order)
    {
        // 1. Authorization & Initial Validations
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

        // Calculate the actual full amount to refund
        $amountToRefund = $payment->amount_paid; // Always attempt to refund the full original payment amount

        // Validate reason (only reason is required for full refund)
        try {
            $validated = $request->validate([
                'reason' => 'required|string|max:255',
            ]);
            $userProvidedReason = $validated['reason'];

        } catch (ValidationException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage(), 'errors' => $e->errors()], 422);
        }

        // Ensure there's a positive amount to refund
        if ($amountToRefund <= 0) {
            return response()->json(['success' => false, 'message' => 'Jumlah refund tidak valid. Tidak ada jumlah yang harus direfund.'], 400);
        }

        // 2. Initiate Stripe Refund for the FULL amount
        try {
            $params = [
                'charge' => $payment->stripe_charge_id,
                'amount' => (int)($amountToRefund * 100), // Amount must be in cents
            ];

            $stripeAllowedReasons = ['duplicate', 'fraudulent', 'requested_by_customer'];
            $reasonForStripe = in_array($userProvidedReason, $stripeAllowedReasons) ? $userProvidedReason : 'requested_by_customer';
            $params['reason'] = $reasonForStripe;

            $stripeRefund = $this->stripe->refunds->create($params);

            // 3. Record Refund in Local Database and Update Stock
            DB::beginTransaction();
            try {
                $refund = Refund::create([
                    'payment_id' => $payment->id,
                    'stripe_refund_id' => $stripeRefund->id,
                    'refunded_amount' => $amountToRefund, // Total monetary amount of THIS refund (should be full order total)
                    'reason' => $userProvidedReason,
                    'refunded_at' => ($stripeRefund->status === 'succeeded' || $stripeRefund->status === 'pending') ? now() : null,
                    'status' => $stripeRefund->status, // 'succeeded', 'pending', 'failed'
                ]);

                // Update stock for ALL items in the order, as it's a full refund
                if ($stripeRefund->status === 'succeeded' || $stripeRefund->status === 'pending') {
                    foreach ($order->orderItems as $orderItem) {
                        // Increment stock for each original item quantity
                        if ($orderItem->product_variant_id) {
                            $variant = ProductVariant::find($orderItem->product_variant_id);
                            if ($variant) {
                                $variant->increment('stock', $orderItem->quantity);
                            }
                        } else {
                            $product = Product::find($orderItem->product_id);
                            if ($product) {
                                $product->increment('stock', $orderItem->quantity);
                            }
                        }
                    }
                }

                // 4. Update Payment Status (always full refund attempt)
                $message = '';
                if ($stripeRefund->status === 'succeeded') {
                    $payment->status = 'refunded';
                    $message = 'Refund berhasil diproses dan pembayaran direfund sepenuhnya.';
                } elseif ($stripeRefund->status === 'pending') {
                    $payment->status = 'refund_pending';
                    $message = 'Permintaan refund sedang diproses.';
                } else { // 'failed'
                    $payment->status = 'refund_failed';
                    $message = 'Gagal memproses refund: ' . ($stripeRefund->failure_reason ?? 'Alasan tidak diketahui');
                }
                $payment->save();

                // 5. Update Order Status
                $redirectUrl = route('my.orders'); // Default redirect

                if ($payment->status === 'refunded') {
                    $order->status = 'Refunded';
                    $redirectUrl = route('profile.index', ['#order-history']); // Redirect to order history
                } elseif ($payment->status === 'refund_pending') {
                    $order->status = 'Refund Pending';
                } elseif ($payment->status === 'refund_failed') {
                    $order->status = 'Refund Failed';
                }
                $order->save(); // Save the final order status

                DB::commit(); // Commit all database changes

                return response()->json(['success' => true, 'message' => $message, 'redirect_url' => $redirectUrl]);

            } catch (Exception $e) {
                DB::rollBack(); // Rollback if any database operation fails
                Log::error('Refund database operation failed for order ' . $order->id . ': ' . $e->getMessage(), [
                    'trace' => $e->getTraceAsString(),
                    'order_id' => $order->id,
                    'payment_id' => $payment->id,
                    'stripe_refund_id' => $stripeRefund->id ?? 'N/A'
                ]);
                return response()->json(['success' => false, 'message' => 'Terjadi kesalahan internal saat mencatat refund. Silakan coba lagi. (' . $e->getMessage() . ')'], 500);
            }

        } catch (Exception $e) {
            Log::error('Stripe Refund API call failed for order ' . $order->id . ': ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'order_id' => $order->id,
                'payment_id' => $payment->id,
                'charge_id' => $payment->stripe_charge_id ?? 'N/A'
            ]);
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat mengajukan refund ke Stripe: ' . $e->getMessage()], 500);
        }
    }
}