<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Stripe\Stripe; // Import the main Stripe class
use Stripe\StripeClient; // Import StripeClient for making API calls
use App\Models\Order; // Import the Order model
use App\Models\Payment; // Import the Payment model
use Illuminate\Support\Facades\Log; // For logging errors
use Illuminate\Support\Facades\Auth; // For accessing the authenticated user (optional)
use Exception; // For handling general exceptions

class StripePaymentController extends Controller
{
    protected $stripe;

    // Constructor to initialize the StripeClient with your secret key
    public function __construct()
    {
        // Set the Stripe API key globally from your config/services.php file
        Stripe::setApiKey(config('services.stripe.secret'));

        // Create an instance of the StripeClient
        $this->stripe = new \Stripe\StripeClient(config('services.stripe.secret'));
    }

    /**
     * Display the Stripe payment form and create/retrieve a PaymentIntent.
     * This method is called after the initial checkout process if 'credit_card' is chosen.
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
            // If no payment record exists or it doesn't have a Stripe Payment Intent ID, create a new one
            if (!$payment || !$payment->stripe_payment_intent_id) {
                $paymentIntent = $this->stripe->paymentIntents->create([
                    'amount' => (int)($order->grand_total * 100), // Amount must be in cents (smallest currency unit)
                    'currency' => 'idr', // Specify currency, e.g., 'idr' for Indonesian Rupiah
                    'metadata' => ['order_id' => $order->id], // Attach order ID for future reference in Stripe
                    'description' => 'Payment for Order #' . $order->id,
                    'receipt_email' => Auth::check() ? Auth::user()->email : $order->customer_email, // Send receipt to this email
                    'automatic_payment_methods' => ['enabled' => true], // Enable automatic payment methods for a simpler integration
                ]);

                // Store the new PaymentIntent details in your local database
                Payment::create([
                    'order_id' => $order->id,
                    'stripe_payment_intent_id' => $paymentIntent->id,
                    'status' => $paymentIntent->status, // Initial status from Stripe (e.g., 'requires_payment_method')
                    'amount_paid' => $order->grand_total, // Store the amount, will be confirmed after success
                    'currency' => strtoupper($paymentIntent->currency),
                    'payment_method_type' => 'card', // Assuming this route is for card payments
                    'card_details' => null,             // These will be populated after successful payment
                    'payment_method_details' => null,   // These will be populated after successful payment
                ]);
                $clientSecret = $paymentIntent->client_secret;

            } else {
                // If a payment record and Stripe Payment Intent ID exist, retrieve the existing PaymentIntent from Stripe
                $paymentIntent = $this->stripe->paymentIntents->retrieve($payment->stripe_payment_intent_id);

                // If the existing PaymentIntent is already succeeded or processing, redirect to success page
                if (in_array($paymentIntent->status, ['succeeded', 'processing'])) {
                    return redirect()->route('order.success', $order->id)->with('success', 'Payment for this order has already been processed.');
                }

                // If the amount of the PaymentIntent doesn't match the current order grand total (e.g., price change), update it
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

            // Pass order data, Stripe public key, and client secret to the payment view
            return view('payment.stripe', [
                'order' => $order,
                'stripePublicKey' => config('services.stripe.key'),
                'clientSecret' => $clientSecret,
            ]);

        } catch (Exception $e) {
            // Log any errors that occur during PaymentIntent creation/retrieval
            Log::error('Stripe Payment Intent creation/retrieval failed: ' . $e->getMessage(), ['order_id' => $order->id]);
            return redirect()->route('checkout.show')->with('error', 'Failed to initialize payment. Please try again. ' . $e->getMessage());
        }
    }

    /**
     * Confirms the payment after client-side Stripe JavaScript completes the payment flow.
     * This method is typically called via an AJAX request from the frontend.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Order $order
     * @return \Illuminate\Http\JsonResponse
     */
    public function confirmPayment(Request $request, Order $order)
    {
        // Validate the incoming request for required Stripe Payment Intent details
        $request->validate([
            'payment_intent_id' => 'required|string',
            'payment_intent_status' => 'required|string', // This is the status reported by client-side Stripe JS
        ]);

        try {
            $paymentIntentId = $request->input('payment_intent_id');
            $reportedPaymentIntentStatus = $request->input('payment_intent_status');

            // Retrieve the PaymentIntent from Stripe's API to get the authoritative status and details
            $stripePaymentIntent = $this->stripe->paymentIntents->retrieve($paymentIntentId);

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
                    // Store specific card details for record-keeping
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

            // Handle different PaymentIntent statuses as reported by Stripe
            if ($stripePaymentIntent->status === 'succeeded') {
                $payment->paid_at = now(); // Set the payment timestamp
                $order->status = 'Order Confirmed'; // Update order status to confirmed
                $order->save();
                $payment->save(); // Save the updated payment record

                return response()->json([
                    'success' => true,
                    'message' => 'Payment successful!',
                    'redirect_url' => route('order.success', $order->id) // Redirect to success page
                ]);
            } elseif ($stripePaymentIntent->status === 'requires_action' || $stripePaymentIntent->status === 'requires_source_action') {
                $payment->save();
                // For 'requires_action', Stripe.js usually handles the necessary redirects (e.g., 3D Secure)
                // This JSON response tells the frontend that more action is needed and provides a potential redirect URL
                return response()->json([
                    'success' => false,
                    'message' => 'Payment requires additional action. Please complete it on Stripe.',
                    'redirect_url' => $stripePaymentIntent->next_action->redirect_to_url->url ?? route('checkout.show') // Fallback redirect if no specific action URL
                ]);
            } else {
                // Handle all other non-successful statuses (e.g., 'failed', 'canceled', 'requires_payment_method' if not handled previously)
                // In a real application, you might want to revert stock for 'failed' or 'canceled' payments here,
                // or handle such reversals via Stripe webhooks for more robustness.
                $order->status = 'Canceled'; // Mark the order as canceled due to payment failure
                $order->save();
                $payment->save(); // Save the final status of the payment record

                return response()->json([
                    'success' => false,
                    'message' => 'Payment failed or was not completed: ' . $stripePaymentIntent->status,
                    'redirect_url' => route('order.fail', $order->id) // Redirect to failure page
                ]);
            }
        } catch (Exception $e) {
            // Catch any exceptions during the confirmation process (e.g., network issues, invalid API keys)
            Log::error('Stripe Payment confirmation failed: ' . $e->getMessage(), [
                'payment_intent_id' => $request->input('payment_intent_id'),
                'order_id' => $order->id,
                'request_data' => $request->all() // Include request data for debugging
            ]);
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred during payment confirmation. Please try again.',
                'redirect_url' => route('checkout.show') // Redirect to checkout as a safe fallback
            ], 500); // Internal Server Error status
        }
    }
}