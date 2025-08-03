<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function processCheckout(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'payment_method' => 'required|in:transfer_bank,credit_card,e_wallet',
            'total_amount' => 'required|numeric|min:0',

            'shipping_recipient_name' => 'required|string|max:255',
            'shipping_phone' => 'required|string|max:255',
            'shipping_address_line' => 'required|string|max:255',
            'shipping_sub_district' => 'nullable|string',
            'shipping_district' => 'nullable|string',
            'shipping_city' => 'required|string',
            'shipping_province' => 'required|string',
            'shipping_postal_code' => 'required|string|max:10',
            'shipping_country' => 'required|string',
        ]);

        $cartItems = Session::get('cart', []);
        if (empty($cartItems)) {
            return redirect()->route('checkout.show')->with('error', 'Your cart is empty. Please add items before checking out.');
        }

        DB::beginTransaction();
        try {
            $shippingCost = 0;

            $calculatedSubtotal = 0;
            foreach ($cartItems as $item) {
                $product = Product::find($item['product_id']);
                if (!$product) {
                    DB::rollBack();
                    return redirect()->back()->with('error', 'One or more products in your cart were not found.')->withInput();
                }

                $itemPrice = $product->price;
                $stockAvailable = $product->stock;

                if (isset($item['product_variant_id']) && $item['product_variant_id']) {
                    $variant = ProductVariant::find($item['product_variant_id']);
                    if (!$variant) {
                        DB::rollBack();
                        return redirect()->back()->with('error', 'One or more product variants in your cart were not found.')->withInput();
                    }
                    $itemPrice = $variant->price ?: $product->price;
                    $stockAvailable = $variant->stock;

                    if ($stockAvailable < $item['quantity']) {
                        DB::rollBack();
                        return redirect()->back()->with('error', 'Insufficient stock for ' . $product->name . ' (' . $variant->size . '). Available: ' . $stockAvailable)->withInput();
                    }
                    $variant->decrement('stock', $item['quantity']);
                } else {
                    if ($stockAvailable < $item['quantity']) {
                        DB::rollBack();
                        return redirect()->back()->with('error', 'Insufficient stock for ' . $product->name . '. Available: ' . $stockAvailable)->withInput();
                    }
                    $product->decrement('stock', $item['quantity']);
                }

                $calculatedSubtotal += $itemPrice * $item['quantity'];
            }

            $grandTotalCalculated = $calculatedSubtotal + $shippingCost;

            if (abs($validatedData['total_amount'] - $grandTotalCalculated) > 0.01) {
                DB::rollBack();
                Log::warning('Frontend total amount does not match backend calculation.', [
                    'frontend_total' => $validatedData['total_amount'],
                    'backend_total' => $grandTotalCalculated,
                    'user_id' => Auth::id(),
                ]);
                return redirect()->back()->with('error', 'There was a price discrepancy. Please try again or refresh the page.')->withInput();
            }

            $shippingAddressData = [
                'recipient_name' => $validatedData['shipping_recipient_name'],
                'phone' => $validatedData['shipping_phone'],
                'address' => $validatedData['shipping_address_line'],
                'sub_district' => $validatedData['shipping_sub_district'] ?? null,
                'district' => $validatedData['shipping_district'] ?? null,
                'city' => $validatedData['shipping_city'],
                'province' => $validatedData['shipping_province'],
                'postal_code' => $validatedData['shipping_postal_code'],
                'country' => $validatedData['shipping_country'],
            ];

            $fullShippingAddressForDb = $shippingAddressData['address'];
            if ($shippingAddressData['sub_district']) {
                $fullShippingAddressForDb .= ', ' . $shippingAddressData['sub_district'];
            }
            if ($shippingAddressData['district']) {
                $fullShippingAddressForDb .= ', ' . $shippingAddressData['district'];
            }

            $order = Order::create([
                'user_id' => Auth::id(),
                'subtotal' => $calculatedSubtotal,
                'shipping_cost' => $shippingCost,
                'grand_total' => $grandTotalCalculated,
                'status' => 'Awaiting Payment',
                'payment_method' => $validatedData['payment_method'],
                'original_user_name' => Auth::check() ? Auth::user()->first_name . ' ' . Auth::user()->last_name : ($validatedData['first_name'] . ' ' . $validatedData['last_name']),
                'original_user_email' => Auth::check() ? Auth::user()->email : $validatedData['email'],
                'shipping_recipient_name' => $shippingAddressData['recipient_name'],
                'shipping_phone' => $shippingAddressData['phone'],
                'shipping_address' => $fullShippingAddressForDb,
                'shipping_country' => $shippingAddressData['country'],
                'shipping_city' => $shippingAddressData['city'],
                'shipping_province' => $shippingAddressData['province'],
                'shipping_postal_code' => $shippingAddressData['postal_code'],
            ]);

            foreach ($cartItems as $itemData) {
                $product = Product::find($itemData['product_id']);
                $productName = $product ? $product->name : 'Unknown Product';
                $productImage = $product ? asset('storage/' . $product->image) : 'https://placehold.co/80x80/cccccc/333333?text=No+Image';

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
                    'price' => $actualPrice,
                    'product_name' => $productName,
                    'product_image' => $productImage,
                    'variant_size' => $variantSize,
                    'variant_color' => $variantColor,
                ]);
            }

            Session::forget('cart');
            DB::commit();

            if ($validatedData['payment_method'] === 'credit_card') {
                return redirect()->route('stripe.payment.form', $order->id);
            } elseif ($validatedData['payment_method'] === 'transfer_bank') {
                return redirect()->route('order.awaitingPayment', $order->id)->with('info', 'Please complete the bank transfer within 24 hours.');
            } else {
                return redirect()->route('order.awaitingPayment', $order->id)->with('info', 'Please follow the instructions for your chosen payment method.');
            }

        } catch (ValidationException $e) {
            DB::rollBack();
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order processing failed: ' . $e->getMessage(), ['request' => $request->all()]);
            return redirect()->back()->with('error', 'An error occurred while processing your order. Please try again. ' . $e->getMessage())->withInput();
        }
    }

    public function showOrderSuccess(Order $order)
    {
        if (Auth::id() !== $order->user_id) {
            abort(403, 'Access Denied. You do not have permission to view this order.');
        }
        return view('order.success', compact('order'));
    }

    public function showOrderFail(Order $order)
    {
        if (Auth::id() !== $order->user_id) {
            abort(403, 'Access Denied. You do not have permission to view this order.');
        }
        return view('order.fail', compact('order'));
    }

    public function showOrderAwaitingPayment(Order $order)
    {
        if (Auth::id() !== $order->user_id) {
            abort(403, 'Access Denied. You do not have permission to view this order.');
        }
        return view('order.awaiting_payment', compact('order'));
    }

    /**
     * Menampilkan daftar pesanan saya untuk pengguna yang terautentikasi.
     * Pesanan dengan status yang dianggap "riwayat" akan dikecualikan.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function myOrders()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'You must be logged in to view your orders.');
        }

        $user = Auth::user();

        // Define statuses that belong in "Order History"
        $historyStatuses = [
            'Completed',
            'Canceled',
            'Returned',
            'Refunded',
            'Refund Pending',
            'Refund Failed',
            'Refund Rejected' // <--- Pastikan ini termasuk
        ];

        $orders = Order::where('user_id', $user->id)
                        // Exclude any order whose status is in the history list
                        ->whereNotIn('status', $historyStatuses)
                        ->with(['orderItems.product', 'orderItems.productVariant', 'payment.refunds'])
                        ->orderByDesc('created_at')
                        ->get();

        return view('myorder', compact('orders'));
    }

    public function completeOrder(Order $order)
    {
        if (Auth::id() !== $order->user_id) {
            return response()->json(['success' => false, 'message' => 'Access Denied. You do not have permission to modify this order.'], 403);
        }

        if ($order->status === 'Delivered' && !$order->hasReview()) {
            try {
                $order->status = 'Completed';
                $order->save();

                return response()->json(['success' => true, 'message' => 'Order has been successfully completed!']);
            } catch (\Exception $e) {
                Log::error('Failed to complete order (backend): ' . $e->getMessage(), ['order_id' => $order->id, 'user_id' => Auth::id()]);
                return response()->json(['success' => false, 'message' => 'An error occurred while trying to complete the order. Please try again.'], 500);
            }
        } elseif ($order->hasReview()) {
            return response()->json(['success' => false, 'message' => 'This order has already been completed and reviewed.'], 400);
        }

        return response()->json(['success' => false, 'message' => 'Order cannot be completed from its current status (' . $order->status . ').'], 400);
    }

    public function cancelOrder(Order $order)
    {
        if (Auth::id() !== $order->user_id) {
            return back()->with('error', 'Access Denied. You do not have permission to cancel this order.');
        }

        if (!in_array($order->status, ['Awaiting Payment', 'Order Confirmed'])) {
            return back()->with('error', 'This order cannot be canceled as its status is ' . $order->status . '. Please contact support for further assistance.');
        }

        DB::beginTransaction();
        try {
            foreach ($order->orderItems as $item) {
                if ($item->product_variant_id) {
                    $variant = ProductVariant::find($item->product_variant_id);
                    if ($variant) {
                        $variant->increment('stock', $item->quantity);
                    }
                } else {
                    $product = Product::find($item->product_id);
                    if ($product) {
                        $product->increment('stock', $item->quantity);
                    }
                }
            }

            $order->status = 'Canceled';
            $order->save();

            DB::commit();
            return redirect()->route('profile.index', ['#order-history'])->with('notification', [
                'type' => 'success',
                'title' => 'Order Cancellation!',
                'message' => 'Order has been successfully canceled.',
                'hasActions' => false
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order cancellation failed: ' . $e->getMessage(), ['order_id' => $order->id, 'user_id' => Auth::id()]);
            return back()->with('error', 'An error occurred while canceling the order. Please try again.');
        }
    }

    public function showOrderModalDetails(Order $order)
    {
        if (Auth::id() !== $order->user_id) {
            return response()->json(['message' => 'Access Denied. You do not have permission to view this order.'], 403);
        }

        $order->load(['orderItems.product', 'orderItems.productVariant', 'payment.refunds']);

        $orderItems = $order->orderItems->map(function ($item) {
            return [
                'product_name' => $item->product_name,
                'quantity' => $item->quantity,
                'price' => $item->price,
                'variant_size' => $item->variant_size,
                'variant_color' => $item->variant_color,
                'product_image' => $item->product ? asset('storage/' . $item->product->image) : 'https://placehold.co/60x60/cccccc/333333?text=No+Image',
            ];
        });

        $paymentDetails = null;
        if ($order->payment) {
            $paymentStatusString = (string) $order->payment->status;

            $paymentDetails = [
                'status' => Str::replace('_', ' ', Str::title($paymentStatusString)),
                'amount_paid' => $order->payment->amount_paid,
                'currency' => $order->payment->currency,
                'payment_method_type' => ucfirst($order->payment->payment_method_type),
                'card_details' => $order->payment->card_details,
                'refunds' => $order->payment->refunds->map(function ($refund) {
                    return [
                        'id' => $refund->id,
                        'stripe_refund_id' => $refund->stripe_refund_id,
                        'refunded_amount' => $refund->refunded_amount,
                        'reason' => $refund->reason,
                        'status' => Str::title($refund->status),
                        'refunded_at' => $refund->refunded_at ? \Carbon\Carbon::parse($refund->refunded_at)->format('d F Y H:i') : null,
                        'refund_image' => $refund->refund_image,
                        'admin_notes' => $refund->admin_notes,
                    ];
                }),
            ];
        }

        return response()->json([
            'order_id' => $order->id,
            'invoice' => 'INV/' . \Carbon\Carbon::parse($order->created_at)->format('Ymd') . '/' . $order->id,
            'order_date' => \Carbon\Carbon::parse($order->created_at)->format('d F Y'),
            'status' => $order->status,
            'payment_method' => ucfirst(str_replace('_', ' ', $order->payment_method)),
            'total_amount' => $order->grand_total,
            'subtotal' => $order->subtotal,
            'shipping_cost' => $order->shipping_cost,
            'shipping_recipient_name' => $order->shipping_recipient_name,
            'shipping_phone' => $order->shipping_phone,
            'shipping_address' => $order->shipping_address,
            'shipping_city' => $order->shipping_city,
            'shipping_province' => $order->shipping_province,
            'shipping_postal_code' => $order->shipping_postal_code,
            'shipping_country' => $order->shipping_country,
            'order_items' => $orderItems,
            'payment_details' => $paymentDetails,
        ]);
    }
}