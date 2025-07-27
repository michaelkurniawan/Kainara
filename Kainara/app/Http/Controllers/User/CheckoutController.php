<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Session;

class CheckoutController extends Controller
{
    public function showCheckoutPage()
    {
        // Initialize dummy addresses in session if not already present
        // In a real application, this data would typically come from a database,
        // associated with the authenticated user.
        if (!Session::has('user_addresses')) {
            Session::put('user_addresses', [
                [
                    'id' => 1,
                    'type' => 'Home',
                    'name' => 'Michael Kurniawan',
                    'phone' => '085175059853',
                    'street' => 'Jl. Pakuan No.3, Sumur Batu',
                    'sub_district' => 'Babakan Madang',
                    'district' => 'Kabupaten Bogor',
                    'city' => '', // City might be empty if sub_district/district is sufficient for the region
                    'province' => 'Jawa Barat',
                    'postal_code' => '16810',
                    'is_primary' => true,
                ],
                [
                    'id' => 2,
                    'type' => 'Work',
                    'name' => 'Michael Kurniawan',
                    'phone' => '085175059853',
                    'street' => 'Sentul City, Jl. Pakuan No.3, Sumur Batu',
                    'sub_district' => 'Babakan Madang',
                    'district' => 'Bogor Regency',
                    'city' => '',
                    'province' => 'West Java',
                    'postal_code' => '16810',
                    'is_primary' => false,
                ],
            ]);
        }

        $userAddresses = Session::get('user_addresses', []);
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
        if (Session::has('selected_address_id')) {
            // Priority 1: Use address ID from flash session (e.g., after user selects/adds an address in a modal)
            $selectedAddressId = Session::get('selected_address_id');
        } else {
            // Priority 2: Find if a primary address is set
            $defaultAddress = collect($userAddresses)->firstWhere('is_primary');
            if ($defaultAddress) {
                $selectedAddressId = $defaultAddress['id'];
            } elseif (!empty($userAddresses)) {
                // Priority 3: If no primary, use the first available address
                $selectedAddressId = $userAddresses[0]['id'];
            }
        }

        // Get the full address data for the selected address ID to pass to the view
        $address = collect($userAddresses)->firstWhere('id', $selectedAddressId);

        return view('products.checkout', compact('cartItems', 'subtotal', 'userAddresses', 'selectedAddressId', 'address'));
    }

    public function addToCheckout(Request $request)
    {
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
                return back()->with('error', 'Not enough stock for the selected variant.');
            }
        } else {
            // If a specific size was requested but not found, or no 'One Size' variant exists for a product needing one
            if ($selectedSize && $selectedSize !== 'One Size') {
                return back()->with('error', 'Selected size is not available for this product.');
            }
            // Fallback for products that truly have no variants and rely on product-level stock (if applicable)
            // if ($product->stock < $quantity) { return back()->with('error', 'Not enough stock for this product.'); }
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
                // Include product name and image directly for convenience in checkout view;
                // though it's still best practice to re-fetch from DB in showCheckoutPage.
                'product_name' => $product->name,
                'product_image' => $product->image,
            ];
        }

        Session::put('cart', $cart);

        // Redirect based on the action performed
        if ($action === 'buy_now') {
            return redirect()->route('checkout.show')->with('success', 'Proceeding to checkout with your selection!');
        } else {
            return back()->with('success', 'Product added to cart!');
        }
    }
}