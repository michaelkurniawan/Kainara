<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product; // Import Product model for stock validation
use App\Models\ProductVariant; // Import ProductVariant model for stock validation
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    /**
     * Processes the checkout request, creates an Order, and redirects to payment.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function processCheckout(Request $request)
    {
        // Validate incoming request data from the checkout form
        $validatedData = $request->validate([
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'payment_method' => 'required|in:transfer_bank,credit_card,e_wallet',
            'total_amount' => 'required|numeric|min:0', // This is the total amount submitted from the frontend

            // Hidden inputs for the selected shipping address details
            'address_type_input' => 'nullable|string',
            'street_input' => 'nullable|string',
            'sub_district_input' => 'nullable|string',
            'district_input' => 'nullable|string',
            'city_input' => 'nullable|string',
            'province_input' => 'nullable|string',
            'postal_code_input' => 'nullable|string',
            'user_name_input' => 'nullable|string',
            'user_phone_input' => 'nullable|string',
        ]);

        $cartItems = Session::get('cart', []);
        // Ensure the cart is not empty before proceeding
        if (empty($cartItems)) {
            return redirect()->route('checkout.show')->with('error', 'Your cart is empty. Please add items before checking out.');
        }

        DB::beginTransaction(); // Start a database transaction to ensure atomicity
        try {
            $shippingCost = 0; // Placeholder: Implement dynamic shipping cost calculation here if applicable

            $calculatedSubtotal = 0;
            // Iterate through cart items to perform backend stock validation and calculate the true subtotal
            foreach ($cartItems as $item) {
                $product = Product::find($item['product_id']);
                if (!$product) {
                    DB::rollBack(); // Rollback transaction on error
                    return redirect()->back()->with('error', 'One or more products in your cart could not be found.')->withInput();
                }

                $itemPrice = $product->price;
                $stockAvailable = $product->stock ?? 0; // Default stock if product doesn't have it explicitly

                if (isset($item['product_variant_id']) && $item['product_variant_id']) {
                    $variant = ProductVariant::find($item['product_variant_id']);
                    if (!$variant) {
                        DB::rollBack();
                        return redirect()->back()->with('error', 'One or more product variants in your cart could not be found.')->withInput();
                    }
                    $itemPrice = $variant->price ?: $product->price; // Use variant's price, fall back to product's
                    $stockAvailable = $variant->stock;

                    // Check if sufficient stock is available for the variant
                    if ($stockAvailable < $item['quantity']) {
                        DB::rollBack();
                        return redirect()->back()->with('error', 'Not enough stock for ' . $product->name . ' (' . $variant->size . '). Available: ' . $stockAvailable)->withInput();
                    }
                    // Decrement variant stock immediately (consider a more robust stock reservation system for high-volume)
                    $variant->decrement('stock', $item['quantity']);

                } else {
                    // This applies if the product has no variants, or 'One Size' is handled directly on the Product model.
                    // Uncomment this block if your Product model directly holds stock for non-variant items.
                    // if ($product->stock && $product->stock < $item['quantity']) {
                    //     DB::rollBack();
                    //     return redirect()->back()->with('error', 'Not enough stock for ' . $product->name . '. Available: ' . $product->stock)->withInput();
                    // }
                    // if ($product->stock) { // Only decrement if stock exists on the product itself
                    //     $product->decrement('stock', $item['quantity']);
                    // }
                }

                $calculatedSubtotal += $itemPrice * $item['quantity'];
            }

            // Verify the total_amount submitted from the frontend against the backend calculation (security check)
            $grandTotalCalculated = $calculatedSubtotal + $shippingCost;
            if (abs($validatedData['total_amount'] - $grandTotalCalculated) > 0.01) { // Allow for small floating point precision differences
                DB::rollBack();
                Log::warning('Frontend total amount mismatch with backend calculation.', [
                    'frontend_total' => $validatedData['total_amount'],
                    'backend_total' => $grandTotalCalculated,
                    'user_id' => Auth::id(), // Log user ID if authenticated
                ]);
                return redirect()->back()->with('error', 'There was a price discrepancy. Please try again or refresh the page.')->withInput();
            }

            // Create the new Order record
            $order = Order::create([
                'user_id' => Auth::id(), // Authenticated user's ID, or null if guest checkout is supported
                'total_amount' => $calculatedSubtotal, // This is the subtotal of products only
                'shipping_cost' => $shippingCost,
                'grand_total' => $grandTotalCalculated, // Total including shipping
                'status' => 'Awaiting Payment', // Initial status for new orders
                'payment_method' => $validatedData['payment_method'],
                'customer_email' => $validatedData['email'],
                'customer_phone' => $validatedData['phone'],
                'customer_first_name' => $validatedData['first_name'],
                'customer_last_name' => $validatedData['last_name'],
                'shipping_address' => json_encode([ // Store shipping address as JSON
                    'type' => $request->input('address_type_input'),
                    'name' => $request->input('user_name_input'),
                    'phone' => $request->input('user_phone_input'),
                    'street' => $request->input('street_input'),
                    'sub_district' => $request->input('sub_district_input'),
                    'district' => $request->input('district_input'),
                    'city' => $request->input('city_input'),
                    'province' => $request->input('province_input'),
                    'postal_code' => $request->input('postal_code_input'),
                ]),
            ]);

            // Create OrderItem records for each item in the cart
            foreach ($cartItems as $itemData) {
                // Re-fetch product details from DB for security and consistency when creating order items
                $product = Product::find($itemData['product_id']);
                $productName = $product ? $product->name : 'Unknown Product';
                $productImage = $product ? $product->image : 'https://placehold.co/80x80/cccccc/333333?text=No+Image';

                // Determine the actual price for the order item, prioritizing variant price
                $actualPrice = $product->price;
                $variantSize = $itemData['variant_size'] ?? null;
                $variantColor = $itemData['variant_color'] ?? null;

                if (isset($itemData['product_variant_id']) && $itemData['product_variant_id']) {
                    $variant = ProductVariant::find($itemData['product_variant_id']);
                    if ($variant) {
                        $actualPrice = $variant->price ?: $product->price;
                        $variantSize = $variant->size;
                        $variantColor = $variant->color;
                    }
                }

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $itemData['product_id'],
                    'product_variant_id' => $itemData['product_variant_id'] ?? null,
                    'quantity' => $itemData['quantity'],
                    'price' => $actualPrice, // Use the validated price from the database
                    'product_name' => $productName,
                    'product_image' => $productImage,
                    'variant_size' => $variantSize,
                    'variant_color' => $variantColor,
                ]);
            }

            Session::forget('cart'); // Clear the cart after successfully creating the order
            DB::commit(); // Commit the database transaction

            // Redirect based on the chosen payment method
            if ($validatedData['payment_method'] === 'credit_card') {
                return redirect()->route('stripe.payment.form', $order->id);
            } elseif ($validatedData['payment_method'] === 'transfer_bank') {
                return redirect()->route('order.awaitingPayment', $order->id)->with('info', 'Please complete your bank transfer within 24 hours.');
            } else { // e_wallet and other non-card methods
                return redirect()->route('order.awaitingPayment', $order->id)->with('info', 'Please follow the instructions for your chosen payment method.');
            }
        } catch (ValidationException $e) {
            DB::rollBack(); // Rollback transaction if validation fails
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback transaction for any other exception
            Log::error('Order processing failed: ' . $e->getMessage(), ['request' => $request->all()]);
            return redirect()->back()->with('error', 'An error occurred during order processing. Please try again.')->withInput();
        }
    }

    /**
     * Displays the order details page.
     *
     * @param \App\Models\Order $order
     * @return \Illuminate\View\View
     */
    public function showOrderDetails(Order $order)
    {
        // Make sure the order's relationships (payment, orderItems) are loaded if needed in the view
        $order->load(['payment', 'orderItems.productVariant']);
        return view('order.details', compact('order'));
    }

    /**
     * Displays the order success page after a successful payment.
     *
     * @param \App\Models\Order $order
     * @return \Illuminate\View\View
     */
    public function showOrderSuccess(Order $order)
    {
        return view('order.success', compact('order'));
    }

    /**
     * Displays the order failure page after a payment attempt fails.
     *
     * @param \App\Models\Order $order
     * @return \Illuminate\View\View
     */
    public function showOrderFail(Order $order)
    {
        return view('order.fail', compact('order'));
    }

    /**
     * Displays the page for orders awaiting payment (for non-card methods).
     *
     * @param \App\Models\Order $order
     * @return \Illuminate\View\View
     */
    public function showOrderAwaitingPayment(Order $order)
    {
        return view('order.awaiting_payment', compact('order'));
    }
}