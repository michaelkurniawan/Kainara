<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\StripeClient;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Exception;

class StripePaymentController extends Controller
{
    protected $stripe;

    public function __construct(StripeClient $stripe)
    {
        $this->stripe = $stripe;
    }

    /**
     * Menampilkan halaman pembayaran Stripe.
     * Dipanggil setelah checkout awal jika metode pembayaran adalah 'credit_card'.
     *
     * @param Order $order
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function showPaymentForm(Order $order)
    {
        // Pastikan order masih 'Awaiting Payment' dan belum memiliki payment intent Stripe
        if ($order->status !== 'Awaiting Payment') {
            return redirect()->route('order.details', $order->id)->with('error', 'This order is not awaiting payment.');
        }

        // Cek apakah PaymentIntent sudah ada untuk order ini
        $payment = Payment::where('order_id', $order->id)->first();

        try {
            if (!$payment || !$payment->stripe_payment_intent_id) {
                // Buat PaymentIntent baru jika belum ada
                $paymentIntent = $this->stripe->paymentIntents->create([
                    'amount' => ($order->subtotal + $order->shipping_cost) * 100, // Amount in cents
                    'currency' => 'idr', // Menggunakan IDR untuk Indonesia
                    'metadata' => ['order_id' => $order->id],
                    'description' => 'Payment for Order #' . $order->id,
                    'receipt_email' => Auth::check() ? Auth::user()->email : null,
                ]);

                // Simpan detail PaymentIntent ke database Payments
                Payment::create([
                    'order_id' => $order->id,
                    'stripe_payment_intent_id' => $paymentIntent->id,
                    'status' => $paymentIntent->status,
                    'amount_paid' => ($order->subtotal + $order->shipping_cost),
                    'currency' => strtoupper($paymentIntent->currency),
                    'payment_method_type' => 'card', // Default to card for this flow
                ]);
                $clientSecret = $paymentIntent->client_secret;

            } else {
                // Ambil PaymentIntent yang sudah ada
                $paymentIntent = $this->stripe->paymentIntents->retrieve($payment->stripe_payment_intent_id);

                // Jika statusnya sudah selesai, redirect
                if (in_array($paymentIntent->status, ['succeeded', 'processing'])) {
                     return redirect()->route('order.success', $order->id)->with('success', 'Payment already processed.');
                }

                // Perbarui jumlah jika ada perubahan (opsional, tergantung logic bisnis)
                if ($paymentIntent->amount !== ($order->subtotal + $order->shipping_cost) * 100) {
                     $paymentIntent = $this->stripe->paymentIntents->update(
                         $payment->stripe_payment_intent_id,
                         ['amount' => ($order->subtotal + $order->shipping_cost) * 100]
                     );
                }

                $clientSecret = $paymentIntent->client_secret;
            }

            return view('payment.stripe', [
                'order' => $order,
                'stripePublicKey' => config('services.stripe.key'),
                'clientSecret' => $clientSecret,
            ]);

        } catch (Exception $e) {
            Log::error('Stripe Payment Intent creation failed: ' . $e->getMessage(), ['order_id' => $order->id]);
            return redirect()->route('checkout.index')->with('error', 'Failed to initialize payment. Please try again. ' . $e->getMessage());
        }
    }

    /**
     * Konfirmasi pembayaran setelah client-side Stripe JavaScript berhasil.
     * Ini dipanggil oleh AJAX dari frontend.
     *
     * @param Request $request
     * @param Order $order
     * @return \Illuminate\Http\JsonResponse
     */
    public function confirmPayment(Request $request, Order $order)
    {
        $request->validate([
            'payment_intent_id' => 'required|string',
            'payment_intent_status' => 'required|string',
        ]);

        try {
            $paymentIntentId = $request->input('payment_intent_id');
            $paymentIntentStatus = $request->input('payment_intent_status');

            // Ambil PaymentIntent dari Stripe untuk verifikasi
            $stripePaymentIntent = $this->stripe->paymentIntents->retrieve($paymentIntentId);

            // Temukan atau buat record Payment di database kita
            $payment = Payment::where('order_id', $order->id)
                              ->where('stripe_payment_intent_id', $paymentIntentId)
                              ->first();

            if (!$payment) {
                // Ini seharusnya tidak terjadi jika alur showPaymentForm sudah benar,
                // tapi sebagai fallback
                return response()->json([
                    'success' => false,
                    'message' => 'Payment record not found for this order and intent ID.',
                    'redirect_url' => route('checkout.index')
                ], 404);
            }

            // Update status Payment berdasarkan status dari Stripe
            $payment->status = $stripePaymentIntent->status;
            $payment->payment_method_type = $stripePaymentIntent->payment_method_types[0] ?? 'card'; // Ambil tipe metode pembayaran
            $payment->payment_method_details = $stripePaymentIntent->charges->data[0]->payment_method_details ?? null;

            if ($stripePaymentIntent->payment_method) {
                $paymentMethod = $this->stripe->paymentMethods->retrieve($stripePaymentIntent->payment_method);
                if ($paymentMethod->type === 'card') {
                    $payment->card_details = [
                        'last4' => $paymentMethod->card->last4,
                        'brand' => $paymentMethod->card->brand,
                    ];
                }
            }

            if ($stripePaymentIntent->status === 'succeeded') {
                $payment->paid_at = now();
                $order->status = 'Order Confirmed'; // Ubah status order
                $order->save();
                $payment->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Payment successful!',
                    'redirect_url' => route('order.success', $order->id) // Redirect ke halaman sukses
                ]);
            } elseif ($stripePaymentIntent->status === 'requires_action' || $stripePaymentIntent->status === 'requires_source_action') {
                // Payment requires additional action (e.g., 3D Secure)
                // The frontend JS will usually handle this by redirecting the user.
                // We just update the status in our DB.
                $payment->save();
                return response()->json([
                    'success' => false,
                    'message' => 'Payment requires additional action. Please complete it on Stripe.',
                    'redirect_url' => $stripePaymentIntent->next_action->redirect_to_url->url ?? route('checkout.index') // Fallback redirect
                ]);
            } else {
                // Payment failed or was canceled
                $order->status = 'Canceled'; // Atau status lain untuk gagal
                $order->save();
                $payment->save();

                return response()->json([
                    'success' => false,
                    'message' => 'Payment failed or was not completed: ' . $stripePaymentIntent->status,
                    'redirect_url' => route('order.fail', $order->id) // Redirect ke halaman gagal
                ]);
            }

        } catch (Exception $e) {
            Log::error('Stripe Payment confirmation failed: ' . $e->getMessage(), [
                'payment_intent_id' => $request->input('payment_intent_id'),
                'order_id' => $order->id
            ]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred during payment confirmation. Please try again. ' . $e->getMessage(),
                'redirect_url' => route('checkout.index') // Redirect ke halaman checkout dengan error
            ], 500);
        }
    }
}