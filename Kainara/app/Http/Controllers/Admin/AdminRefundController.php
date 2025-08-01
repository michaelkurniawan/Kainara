<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Refund;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Stripe\Stripe;
use Stripe\StripeClient;
use Exception;

class AdminRefundController extends Controller
{
    protected $stripe;

    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
        $this->stripe = new \Stripe\StripeClient(config('services.stripe.secret'));
    }

    const ALL_REFUND_STATUSES = [
        'pending',
        'approved',
        'rejected',
        'succeeded',
        'failed',
    ];

    public function index(Request $request)
    {
        $filterStatus = $request->query('status');
        $searchQuery = $request->query('search');

        $refundsQuery = Refund::query()->with(['payment.order.user', 'payment.order.orderItems.product']);

        if ($filterStatus && $filterStatus !== 'all') {
            $refundsQuery->where('status', $filterStatus);
        }

        if ($searchQuery) {
            $refundsQuery->where(function ($query) use ($searchQuery) {
                $query->where('stripe_refund_id', 'like', '%' . $searchQuery . '%')
                      ->orWhere('reason', 'like', '%' . $searchQuery . '%')
                      ->orWhere('admin_notes', 'like', '%' . $searchQuery . '%')
                      ->orWhereHas('payment.order', function ($q) use ($searchQuery) {
                          $q->where('id', $searchQuery)
                            ->orWhere('original_user_name', 'like', '%' . $searchQuery . '%');
                      });
            });
        }

        $refunds = $refundsQuery->latest()->paginate(15);

        $allStatuses = self::ALL_REFUND_STATUSES;

        return view('admin.refund.index', compact('refunds', 'allStatuses', 'filterStatus', 'searchQuery'));
    }

    public function show(Refund $refund)
    {
        $refund->load(['payment.order.user', 'payment.order.orderItems.product', 'payment.order.orderItems.productVariant']);
        $allStatuses = self::ALL_REFUND_STATUSES;

        return view('admin.refund.show', compact('refund', 'allStatuses'));
    }

    public function updateStatus(Request $request, Refund $refund)
    {
        try {
            $validated = $request->validate([
                'status' => ['required', 'string', 'in:' . implode(',', self::ALL_REFUND_STATUSES)],
                'admin_notes' => ['nullable', 'string', 'max:500'],
            ]);

            $oldStatus = $refund->status;
            $newStatus = $validated['status'];
            $adminNotes = $validated['admin_notes'];

            // Validasi transisi status (opsional, sesuaikan kebutuhan)
            if ($oldStatus === 'succeeded' && $newStatus !== 'succeeded') {
                return redirect()->back()->with('error', 'Cannot change refund status from "succeeded" to a different status.');
            }
            if ($oldStatus === 'failed' && $newStatus !== 'failed') {
                return redirect()->back()->with('error', 'Cannot change refund status from "failed" to a different status.');
            }
            if ($oldStatus === 'rejected' && $newStatus !== 'rejected') {
                return redirect()->back()->with('error', 'Cannot change refund status from "rejected" to a different status.');
            }
            if ($oldStatus === 'approved' && in_array($newStatus, ['pending', 'rejected'])) {
                return redirect()->back()->with('error', 'Cannot change refund status from "approved" to "pending" or "rejected".');
            }
            if ($newStatus === 'approved' && $oldStatus !== 'pending') {
                return redirect()->back()->with('error', 'Only "pending" refund requests can be approved.');
            }
            if ($newStatus === 'rejected' && $oldStatus !== 'pending') {
                return redirect()->back()->with('error', 'Only "pending" refund requests can be rejected.');
            }


            DB::beginTransaction();
            try {
                $payment = $refund->payment;
                $order = $payment->order;

                $stripeRefund = null;
                $message = 'Refund status successfully updated.';

                if ($newStatus === 'approved' && $oldStatus === 'pending') {
                    $amountToRefundStripe = (int)($refund->refunded_amount * 100);

                    try {
                        $params = [
                            'charge' => $payment->stripe_charge_id,
                            'amount' => $amountToRefundStripe,
                            'reason' => 'requested_by_customer',
                        ];
                        $stripeRefund = $this->stripe->refunds->create($params);

                        $refund->status = $stripeRefund->status;
                        $refund->stripe_refund_id = $stripeRefund->id;
                        $refund->refunded_at = ($stripeRefund->status === 'succeeded') ? now() : null;

                        if ($stripeRefund->status === 'succeeded') {
                            $payment->status = 'refunded';
                            $order->status = 'Refunded';
                            $message = 'Refund successfully approved and processed by Stripe.';
                            foreach ($order->orderItems as $orderItem) {
                                if ($orderItem->product_variant_id) {
                                    $variant = ProductVariant::find($orderItem->product_variant_id);
                                    if ($variant) $variant->increment('stock', $orderItem->quantity);
                                } else {
                                    $product = Product::find($orderItem->product_id);
                                    if ($product) $product->increment('stock', $orderItem->quantity);
                                }
                            }
                        } elseif ($stripeRefund->status === 'pending') {
                            $payment->status = 'refund_pending';
                            $order->status = 'Refund Pending';
                            $message = 'Refund approved, but is pending processing by Stripe.';
                        } else { // failed
                            $payment->status = 'refund_failed';
                            $order->status = 'Refund Failed';
                            $message = 'Refund approved, but failed to process by Stripe: ' . ($stripeRefund->failure_reason ?? 'Unknown reason');
                        }

                    } catch (\Stripe\Exception\ApiErrorException $e) {
                        $refund->status = 'failed';
                        $payment->status = 'refund_failed';
                        $order->status = 'Refund Failed';
                        $message = 'Refund approved, but failed to process by Stripe API: ' . $e->getMessage();
                        Log::error('Stripe API error during refund approval: ' . $e->getMessage(), ['refund_id' => $refund->id, 'trace' => $e->getTraceAsString()]);
                    }
                }
                // --- PERBAIKAN DI SINI ---
                elseif ($newStatus === 'rejected' && $oldStatus === 'pending') {
                    $refund->status = 'rejected';
                    $payment->status = 'succeeded'; // Pembayaran tidak direfund, jadi kembali ke status sukses
                    $order->status = 'Refund Rejected'; // <-- UBAH STATUS ORDER KE 'Refund Rejected'
                    $message = 'Refund request has been rejected.';
                }
                // --- AKHIR PERBAIKAN ---
                else {
                    $refund->status = $newStatus;
                    // Untuk status lain, tidak ada perubahan status payment/order di sini
                    // karena sudah ditangani oleh Stripe webhook atau status final dari proses approved/rejected.
                }

                $refund->admin_notes = $adminNotes;
                $refund->save();
                $payment->save();
                $order->save();

                DB::commit();

                return redirect()->route('admin.refunds.show', $refund)->with('success', $message);

            } catch (Exception $e) {
                DB::rollBack();
                Log::error('Refund failed (Stripe API or DB) for order ' . $order->id . ': ' . $e->getMessage(), [
                    'trace' => $e->getTraceAsString(),
                    'refund_id' => $refund->id,
                ]);
                return redirect()->back()->with('error', 'Failed to update refund status. Message: ' . $e->getMessage());
            }

        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (Exception $e) {
            Log::error('Failed to update refund status (General): ' . $e->getMessage(), ['refund_id' => $refund->id, 'trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'Failed to update refund status. Message: ' . $e->getMessage());
        }
    }
}