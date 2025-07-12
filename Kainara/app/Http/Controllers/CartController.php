<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\Product; // Import Product model
use App\Models\ProductVariant; // Import ProductVariant model

class CartController extends Controller
{
    /**
     * Display the shopping cart.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $cartItems = Session::get('cart', []);
        $subtotal = 0;

        // Populate cart items with full product details for display
        foreach ($cartItems as $key => &$item) {
            $product = Product::find($item['product_id']);

            if (!$product) {
                // If product no longer exists, remove it from cart
                unset($cartItems[$key]);
                continue;
            }

            $item['product_name'] = $product->name;
            $item['product_image'] = $product->image; // Use product's main image if variant image isn't stored in session cart yet

            // Ensure price is set from the session item (which might be variant price)
            $item_price = $item['price'];

            // Get variant details if product_variant_id exists
            if (!empty($item['product_variant_id'])) {
                $variant = ProductVariant::find($item['product_variant_id']);
                if ($variant) {
                    $item['variant_size'] = $variant->size;
                    $item['variant_color'] = $variant->color;
                    // Use variant price if available, fallback to session item price, then product base price
                    $item_price = $variant->price ?: $item_price;
                } else {
                    // If variant not found, try to reconstruct variant info from session or default
                    $item['variant_size'] = $item['variant_size'] ?? 'N/A';
                    $item['variant_color'] = $item['variant_color'] ?? 'N/A';
                }
            } else {
                $item['variant_size'] = 'N/A';
                $item['variant_color'] = 'N/A';
            }

            $item['price'] = $item_price;
            $item['total_item_price'] = $item_price * $item['quantity'];
            $subtotal += $item['total_item_price'];
        }

        // Save updated cart back to session in case items were removed
        Session::put('cart', $cartItems);

        return view('keranjang', compact('cartItems', 'subtotal'));
    }

    /**
     * Update the quantity of a product in the cart.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'product_variant_id' => 'nullable|exists:product_variants,id',
            'quantity' => 'required|integer|min:0', // Allow 0 to effectively remove item
        ]);

        $productId = $request->input('product_id');
        $productVariantId = $request->input('product_variant_id');
        $newQuantity = $request->input('quantity');

        $cart = Session::get('cart', []);
        $updated = false;

        foreach ($cart as $key => &$item) {
            if ($item['product_id'] == $productId && ($item['product_variant_id'] == $productVariantId)) {
                if ($newQuantity > 0) {
                    // Optional: Check stock before updating
                    $availableStock = 0;
                    if ($productVariantId) {
                        $variant = ProductVariant::find($productVariantId);
                        if ($variant) $availableStock = $variant->stock;
                    } else {
                        // Assuming non-variant products have stock on Product model, or 'One Size' variant
                        $product = Product::find($productId);
                        if ($product) {
                            $defaultVariant = $product->variants()->where('size', 'One Size')->first();
                            if ($defaultVariant) $availableStock = $defaultVariant->stock;
                            // Else, handle product with no variants having no stock
                        }
                    }

                    if ($newQuantity > $availableStock) {
                        return back()->with('error', "Only {$availableStock} of {$item['product_name']} available in stock.");
                    }

                    $item['quantity'] = $newQuantity;
                    $updated = true;
                } else {
                    // Remove item if quantity is 0
                    unset($cart[$key]);
                    $updated = true;
                }
                break;
            }
        }

        if (!$updated) {
            return back()->with('error', 'Item not found in cart.');
        }

        Session::put('cart', array_values($cart)); // Re-index array after unsetting
        return back()->with('success', 'Cart updated successfully.');
    }

    /**
     * Remove a product from the cart.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function remove(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'product_variant_id' => 'nullable|exists:product_variants,id',
        ]);

        $productId = $request->input('product_id');
        $productVariantId = $request->input('product_variant_id');

        $cart = Session::get('cart', []);
        $initialCartCount = count($cart);

        // Filter out the item to be removed
        $cart = array_filter($cart, function ($item) use ($productId, $productVariantId) {
            return !($item['product_id'] == $productId && ($item['product_variant_id'] == $productVariantId));
        });

        if (count($cart) < $initialCartCount) {
            Session::put('cart', array_values($cart)); // Re-index array
            return back()->with('success', 'Item removed from cart.');
        }

        return back()->with('error', 'Item not found in cart.');
    }
}