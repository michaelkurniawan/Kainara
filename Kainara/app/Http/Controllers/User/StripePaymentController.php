<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Stripe\Stripe;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Exception;

class StripePaymentController extends Controller
{
    protected $stripe;

    /**
     * Constructor to initialize StripeClient with your secret key.
     */
    public function __construct()
    {
        // Set Stripe's API key globally from your config/services.php file
        Stripe::setApiKey(config('services.stripe.secret'));

        // Create a StripeClient instance
        $this->stripe = new \Stripe\StripeClient(config('services.stripe.secret'));
    }

    /**
     * Displays the Stripe payment form and creates/retrieves a PaymentIntent.
     * This method is called after the initial checkout process if 'credit_card' is selected.
     *
     * @param \App\Models\Order $order
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function showPaymentForm(Order $order)
    {
        // First, check if the order is actually awaiting payment
        if ($order->status !== 'Awaiting Payment') {
            return redirect()->route('order.details', $order->id)->with('error', 'This order is not awaiting payment or has already been processed.');
        }

        // Try to find an existing payment record for this order to see if a PaymentIntent was already created
        $payment = Payment::where('order_id', $order->id)->first();

        try {
            $clientSecret = null;
            // If no payment record or it lacks a Stripe Payment Intent ID, create a new one
            if (!$payment || !$payment->stripe_payment_intent_id) {
                $paymentIntent = $this->stripe->paymentIntents->create([
                    'amount' => (int)($order->grand_total * 100), // Amount must be in cents (the smallest currency unit)
                    'currency' => 'idr', // Specify the currency, e.g., 'usd' or 'idr' for Indonesian Rupiah
                    'metadata' => ['order_id' => $order->id], // Attach the order ID for reference in Stripe
                    'description' => 'Payment for Order #' . $order->id,
                    // Send a receipt to the authenticated user's email or the original email on the order
                    'receipt_email' => Auth::check() ? Auth::user()->email : $order->original_user_email,
                    'automatic_payment_methods' => ['enabled' => true], // Enable automatic payment methods for simpler integration
                ]);

                // Store the new PaymentIntent details in your local database
                Payment::create([
                    'order_id' => $order->id,
                    'stripe_payment_intent_id' => $paymentIntent->id,
                    'status' => $paymentIntent->status, // The initial status from Stripe (e.g., 'requires_payment_method')
                    'amount_paid' => $order->grand_total, // Store the amount, will be confirmed upon success
                    'currency' => strtoupper($paymentIntent->currency),
                    'payment_method_type' => 'card', // Assume this route is for card payments
                    'card_details' => null,             // This will be filled after successful payment
                    'payment_method_details' => null,   // This will be filled after successful payment
                ]);
                $clientSecret = $paymentIntent->client_secret;

            } else {
                // If a payment record and Stripe Payment Intent ID exist, retrieve the existing PaymentIntent from Stripe
                $paymentIntent = $this->stripe->paymentIntents->retrieve($payment->stripe_payment_intent_id);

                // If the existing PaymentIntent is already succeeded or processing, redirect to a success page
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

                // If the PaymentIntent's amount does not match the current order's grand_total (e.g., price changed), update it
                if ($paymentIntent->amount !== (int)($order->grand_total * 100)) {
                    $paymentIntent = $this->stripe->paymentIntents->update(
                        $payment->stripe_payment_intent_id,
                        ['amount' => (int)($order->grand_total * 100)]
                    );
                    // Also update the amount_paid in your local Payment record
                    $payment->amount_paid = $order->grand_total;
                    $payment->save();
                }
                $clientSecret = $paymentIntent->client_secret;
            }

            // Pass the order data, Stripe public key, and client secret to the payment view
            return view('payment.stripe', [
                'order' => $order,
                'stripePublicKey' => config('services.stripe.key'),
                'clientSecret' => $clientSecret,
            ]);

        } catch (Exception $e) {
            // Log any errors that occurred during PaymentIntent creation/retrieval
            Log::error('Stripe Payment Intent creation/retrieval failed: ' . $e->getMessage(), ['order_id' => $order->id]);
            return redirect()->route('checkout.show')->with('error', 'Failed to initiate payment. Please try again. ' . $e->getMessage());
        }
    }

    /**
     * Confirms the payment after the client-side Stripe JavaScript completes the payment flow.
     * This method is typically called via an AJAX request from the frontend.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Order $order
     * @return \Illuminate\Http\JsonResponse
     */
    public function confirmPayment(Request $request, Order $order)
    {
        // Validate incoming request for the required Stripe Payment Intent details
        $request->validate([
            'payment_intent_id' => 'required|string',
            'payment_intent_status' => 'required|string', // This is the status reported by the client-side Stripe JS
        ]);

        try {
            $paymentIntentId = $request->input('payment_intent_id');
            $reportedPaymentIntentStatus = $request->input('payment_intent_status');

            // Retrieve the PaymentIntent from the Stripe API to get the authentic status and details
            // Important: Expand 'latest_charge' to get the associated Charge ID
            $stripePaymentIntent = $this->stripe->paymentIntents->retrieve($paymentIntentId, ['expand' => ['latest_charge']]);

            // Find the corresponding local Payment record in your database
            $payment = Payment::where('order_id', $order->id)
                             ->where('stripe_payment_intent_id', $paymentIntentId)
                             ->first();

            if (!$payment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment record not found for this order and intent ID in our system.',
                    'redirect_url' => route('checkout.show') // Redirect to checkout if local record is missing
                ], 404);
            }

            // Update the local Payment record with the latest status from Stripe
            $payment->status = $stripePaymentIntent->status;
            // Capture the first payment method type used for the transaction
            $payment->payment_method_type = $stripePaymentIntent->payment_method_types[0] ?? 'card';

            // Extract and store payment method details, especially for card payments
            if (isset($stripePaymentIntent->charges->data[0]->payment_method_details)) {
                $payment->payment_method_details = $stripePaymentIntent->charges->data[0]->payment_method_details;
                if ($payment->payment_method_details['type'] === 'card') {
                    // Save specific card details for record-keeping
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

            // --- IMPORTANT ADDITION: Store the stripe_charge_id ---
            // The Charge ID is necessary for processing refunds in Stripe
            if ($stripePaymentIntent->latest_charge && $stripePaymentIntent->latest_charge->id) {
                $payment->stripe_charge_id = $stripePaymentIntent->latest_charge->id;
            }
            // --- END IMPORTANT ADDITION ---

            // Handle different PaymentIntent statuses reported by Stripe
            if ($stripePaymentIntent->status === 'succeeded') {
                $payment->paid_at = now(); // Set the payment timestamp
                $order->status = 'Order Confirmed'; // Update order status to confirmed
                $order->save();
                $payment->save(); // Save the updated payment record

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
                    'message' => 'Payment requires additional action. Please complete on Stripe.',
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
                'message' => 'An unexpected error occurred during payment confirmation. Please try again.',
                'redirect_url' => route('checkout.show') // We still send this for the JS to handle after notification
            ], 500); // Internal Server Error Status
            // **CHANGE END**
        }
    }
}