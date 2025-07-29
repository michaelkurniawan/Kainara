<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\UserAddress; // Import the UserAddress model
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth; // Import Auth facade

class CheckoutController extends Controller
{
    public function showCheckoutPage()
    {
        // Get the authenticated user
        $user = Auth::user();

        // Fetch user addresses from the database
        // If the user is not logged in, userAddresses will be empty.
        $userAddresses = $user ? $user->addresses()->get() : collect();

        $cartItems = Session::get('cart', []);
        $subtotal = 0;

        // Iterate through cart items to fetch fresh product/variant data and calculate totals
        foreach ($cartItems as &$item) {
            $product = Product::find($item['product_id']);
            if ($product) {
                $item['product_name'] = $product->name;
                $item['product_image'] = $product->image;

                // Check for product variant details if applicable
                if (isset($item['product_variant_id']) && $item['product_variant_id']) {
                    $variant = ProductVariant::find($item['product_variant_id']);
                    if ($variant) {
                        $item['price'] = $variant->price ?: $product->price; // Use variant's price if set, otherwise product's
                        $item['variant_size'] = $variant->size;
                        $item['variant_color'] = $variant->color;
                    } else {
                        // Fallback if variant is not found (e.g., deleted)
                        $item['price'] = $product->price;
                        $item['variant_size'] = null;
                        $item['variant_color'] = null;
                    }
                } else {
                    $item['price'] = $product->price;
                    $item['variant_size'] = null;
                    $item['variant_color'] = null;
                }

                $item['total_price'] = $item['price'] * $item['quantity'];
                $subtotal += $item['total_price'];
            } else {
                // If product not found (e.g., deleted from database), mark it as unknown
                $item['product_name'] = 'Unknown Product';
                $item['product_image'] = 'https://placehold.co/80x80/cccccc/333333?text=No+Image'; // Placeholder image
                $item['price'] = 0;
                $item['total_price'] = 0;
            }
        }

        // Determine the address to display on the checkout page
        $selectedAddressId = null;
        $address = null; // Initialize address to null

        // If a user is logged in and has addresses
        if ($user && $userAddresses->isNotEmpty()) {
            if (Session::has('selected_address_id')) {
                // Priority 1: Use address ID from session (e.g., after user selects/adds an address in a modal)
                $selectedAddressId = Session::get('selected_address_id');
                $address = $userAddresses->firstWhere('id', $selectedAddressId);

                // If the selected ID from session is not found in current user's addresses, fallback
                if (!$address) {
                    $selectedAddressId = null;
                }
            }

            if (!$selectedAddressId) {
                // Priority 2: Find if a primary/default address is set in the database
                $defaultAddress = $userAddresses->firstWhere('is_default', true);
                if ($defaultAddress) {
                    $selectedAddressId = $defaultAddress->id;
                    $address = $defaultAddress;
                } elseif ($userAddresses->isNotEmpty()) {
                    // Priority 3: If no default, use the first available address
                    $selectedAddressId = $userAddresses->first()->id;
                    $address = $userAddresses->first();
                }
            }
        }


        // The $address variable now holds the full address object (or null)
        return view('products.checkout', compact('cartItems', 'subtotal', 'userAddresses', 'selectedAddressId', 'address'));
    }

    public function addToCheckout(Request $request)
    {
        // ... (your existing addToCheckout method remains the same)
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'selected_size' => 'nullable|string',
            'action' => 'required|string|in:add_to_cart,buy_now',
        ]);

        $productId = $request->input('product_id');
        $quantity = $request->input('quantity');
        $selectedSize = $request->input('selected_size');
        $action = $request->input('action');

        $product = Product::findOrFail($productId);
        $price = $product->price;
        $productVariantId = null;
        $variantColor = null;

        $productVariant = null;
        // Logic to find the correct product variant based on selected size
        if ($selectedSize) {
            $productVariant = ProductVariant::where('product_id', $productId)
                                            ->where('size', $selectedSize)
                                            ->first();
        } else {
            // If no specific size is selected, try to find a 'One Size' variant
            $productVariant = ProductVariant::where('product_id', $productId)
                                            ->where('size', 'One Size')
                                            ->first();
        }

        if ($productVariant) {
            $price = $productVariant->price ?: $product->price; // Use variant price if set, otherwise base product price
            $productVariantId = $productVariant->id;
            $variantColor = $productVariant->color;
            // Check stock before adding to cart/checkout
            if ($productVariant->stock < $quantity) {
                return back()->with('notification', [
                    'type' => 'error',
                    'title' => 'Limited Stock!',
                    'message' => 'Not enough stock for the selected variant. Available: ' . $productVariant->stock,
                    'hasActions' => false
                ]);
            }
        } else {
            // If a specific size was requested but not found, or no 'One Size' variant exists for a product needing one
            if ($selectedSize && $selectedSize !== 'One Size') {
                return back()->with('notification', [
                    'type' => 'error',
                    'title' => 'Size Not Available!',
                    'message' => 'The selected size is not available for this product.',
                    'hasActions' => false
                ]);
            }
        }

        // Handle 'buy_now' action: clear the cart first
        if ($action === 'buy_now') {
            Session::forget('cart');
            $cart = [];
        } else {
            $cart = Session::get('cart', []);
        }

        $itemFound = false;
        // Check if the exact product and variant combination already exists in the cart
        foreach ($cart as $key => $cartItem) {
            if ($cartItem['product_id'] == $productId &&
                (
                    ($productVariantId && $cartItem['product_variant_id'] == $productVariantId) ||
                    (!$productVariantId && !$cartItem['product_variant_id']) // For products without variants
                )
            ) {
                // If found, update the quantity
                if ($action === 'buy_now') {
                    $cart[$key]['quantity'] = $quantity; // For buy now, overwrite quantity
                } else {
                    $cart[$key]['quantity'] += $quantity; // For add to cart, increment quantity
                }
                $itemFound = true;
                break;
            }
        }

        if (!$itemFound) {
            // If the item (product + variant) is new to the cart, add it
            $cart[] = [
                'product_id' => $productId,
                'product_variant_id' => $productVariantId,
                'price' => $price, // The per-unit price determined above
                'quantity' => $quantity,
                'variant_size' => $selectedSize,
                'variant_color' => $variantColor,
                'product_name' => $product->name,
                'product_image' => $product->image,
            ];
        }

        Session::put('cart', $cart);

        if ($action === 'buy_now') {
            return redirect()->route('checkout.show')->with('notification', [
                'type' => 'success',
                'title' => 'Proceeding to Checkout!',
                'message' => 'Your selection has been added. Please complete your purchase.',
                'hasActions' => false
            ]);
        } else {
            return back()->with('notification', [
                'type' => 'success',
                'title' => 'Added to Cart!',
                'message' => 'The product has been successfully added to your shopping cart.',
                'hasActions' => false
            ]);
        }
    }
}