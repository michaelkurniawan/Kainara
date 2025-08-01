<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Refund;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Stripe\Stripe;
use Stripe\StripeClient;
use Exception;
use Illuminate\Support\Str;

class RefundController extends Controller
{
    public function __construct()
    {
        //
    }

    public function showRefundForm(Order $order)
    {
        if (Auth::id() !== $order->user_id) {
            return redirect()->route('my.orders')->with('error', 'You are not authorized to access this order.');
        }

        if ($order->status !== 'Delivered') {
            return redirect()->route('my.orders')->with('error', 'Refunds can only be requested for delivered orders.');
        }

        $payment = $order->payment()->with('refunds')->first();

        if (!$payment || !$payment->stripe_charge_id || $payment->status !== 'succeeded') {
            return redirect()->route('my.orders')->with('error', 'Payment for this order is not eligible for a full refund. (Payment status: ' . ($payment->status ?? 'None') . ')');
        }

        $totalRefundedAmount = $payment->refunds->where('status', 'succeeded')->sum('refunded_amount');
        $fullOrderAmount = $payment->amount_paid;

        $availableForRefund = $fullOrderAmount - $totalRefundedAmount;

        if (abs($availableForRefund) < 0.01) {
            return redirect()->route('my.orders')->with('info', 'This order has already been fully refunded.');
        }

        return view('refund.form', compact('order', 'payment', 'availableForRefund'));
    }

    public function requestRefund(Request $request, Order $order)
    {
        if (Auth::id() !== $order->user_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized access.'], 403);
        }

        if ($order->status !== 'Delivered') {
            return response()->json(['success' => false, 'message' => 'Refunds can only be requested for delivered orders.'], 400);
        }

        $payment = $order->payment()->with('refunds')->first();
        if (!$payment || !$payment->stripe_charge_id || $payment->status !== 'succeeded') {
            return response()->json(['success' => false, 'message' => 'Payment for this order is not eligible for a full refund.'], 400);
        }

        try {
            // Updated validation rules: 'refund_image' is now required
            $validated = $request->validate([
                'reason' => 'required|string|max:255',
                'refund_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);
            $userProvidedReason = $validated['reason'];

        } catch (ValidationException $e) {
            return response()->json(['success' => false, 'message' => 'Validation Failed', 'errors' => $e->errors()], 422);
        }

        $imagePath = null;
        // The file check is no longer strictly necessary here due to the 'required' validation,
        // but it's good practice to wrap the store call in case of other issues.
        if ($request->hasFile('refund_image')) {
            try {
                $imagePath = $request->file('refund_image')->store('public/refund_images');
            } catch (\Exception $e) {
                Log::error('Failed to upload refund image: ' . $e->getMessage(), ['order_id' => $order->id]);
                return response()->json(['success' => false, 'message' => 'Failed to upload image: ' . $e->getMessage()], 500);
            }
        }

        $amountToRefund = $payment->amount_paid;

        if ($amountToRefund <= 0) {
            return response()->json(['success' => false, 'message' => 'Invalid refund amount. No amount to refund.'], 400);
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
                'admin_notes' => 'Refund request submitted by user.',
            ]);

            $payment->status = 'refund_pending';
            $payment->save();

            $order->status = 'Refund Pending';
            $order->save();

            DB::commit();


            return redirect()->route('profile.index', ['#order-history'])->with('notification', [
                'type' => 'success',
                'title' => 'Refund Request!',
                'message' => 'Your refund request has been submitted and is awaiting admin review.',
                'hasActions' => false
            ]);
            

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to submit refund request by user: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'order_id' => $order->id,
                'payment_id' => $payment->id,
            ]);
            return response()->json(['success' => false, 'message' => 'An error occurred while submitting the refund request: ' . $e->getMessage()], 500);
        }
    }
}