<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\Product;
use App\Models\ProductVariant;

class CartController extends Controller
{
    public function index()
    {
        $cartItems = Session::get('cart', []);
        $subtotal = 0;

        foreach ($cartItems as $key => &$item) {
            $product = Product::find($item['product_id']);

            if (!$product) {
                // If product is not found, remove it from cart
                unset($cartItems[$key]);
                Session::flash('error', 'One or more products in your cart are no longer available and have been removed.');
                continue;
            }

            $item['product_name'] = $product->name;
            $item['product_image'] = $product->image;

            $item_price = $product->price; // Default to product price
            $max_stock_available = 0;

            if (!empty($item['product_variant_id'])) {
                $variant = ProductVariant::find($item['product_variant_id']);
                if ($variant) {
                    $item['variant_size'] = $variant->size;
                    $item['variant_color'] = $variant->color;
                    $item_price = $variant->price ?: $product->price; // Use variant price if set, else product price
                    $max_stock_available = $variant->stock;
                } else {
                    // If variant not found, assume base product or 'One Size' if it exists
                    $item['variant_size'] = $item['variant_size'] ?? 'N/A';
                    $item['variant_color'] = $item['variant_color'] ?? 'N/A';
                    $defaultVariant = $product->variants()->where('size', 'One Size')->first();
                    if ($defaultVariant) {
                        $max_stock_available = $defaultVariant->stock;
                    } else {
                        $max_stock_available = $product->stock; // Fallback to product stock
                    }
                }
            } else {
                // If no specific variant is selected, check for 'One Size' variant or base product stock
                $item['variant_size'] = 'N/A';
                $item['variant_color'] = 'N/A';
                $defaultVariant = $product->variants()->where('size', 'One Size')->first();
                if ($defaultVariant) {
                    $max_stock_available = $defaultVariant->stock;
                } else {
                    $max_stock_available = $product->stock; // Fallback to product stock
                }
            }
            
            // Ensure item quantity does not exceed available stock upon loading cart
            if ($item['quantity'] > $max_stock_available) {
                $item['quantity'] = $max_stock_available > 0 ? $max_stock_available : 1; // Clamp to available stock or 1
                Session::flash('info', "Quantity for '{$item['product_name']}' was adjusted to available stock: {$item['quantity']}.");
            }
            if ($max_stock_available === 0 && $item['quantity'] > 0) {
                 unset($cartItems[$key]);
                 Session::flash('error', "'{$item['product_name']}' is out of stock and has been removed from your cart.");
                 continue; // Skip to next item
            }


            $item['price'] = $item_price;
            $item['total_item_price'] = $item_price * $item['quantity'];
            $item['max_stock_available'] = $max_stock_available; // Pass max stock to frontend
            $subtotal += $item['total_item_price'];
        }

        Session::put('cart', $cartItems);

        return view('keranjang', compact('cartItems', 'subtotal'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'product_variant_id' => 'nullable|exists:product_variants,id',
            'quantity' => 'required|integer|min:0',
        ]);

        $productId = $request->input('product_id');
        $productVariantId = $request->input('product_variant_id');
        $newQuantity = $request->input('quantity');

        $cart = Session::get('cart', []);
        $updated = false;

        foreach ($cart as $key => &$item) {
            if ($item['product_id'] == $productId && ($item['product_variant_id'] == $productVariantId)) {
                $availableStock = 0;
                if ($productVariantId) {
                    $variant = ProductVariant::find($productVariantId);
                    if ($variant) $availableStock = $variant->stock;
                } else {
                    $product = Product::find($productId);
                    if ($product) {
                        $defaultVariant = $product->variants()->where('size', 'One Size')->first();
                        if ($defaultVariant) {
                            $availableStock = $defaultVariant->stock;
                        } else {
                            $availableStock = $product->stock; // Fallback for products without variants
                        }
                    }
                }

                if ($newQuantity > 0) {
                    if ($newQuantity > $availableStock) {
                        return back()->with('error', "Only {$availableStock} of {$item['product_name']} available in stock. Your quantity was limited.");
                    }

                    $item['quantity'] = $newQuantity;
                    $updated = true;
                } else {
                    // If new quantity is 0, remove item from cart
                    unset($cart[$key]);
                    $updated = true;
                }
                break;
            }
        }

        if (!$updated) {
            return back()->with('error', 'Item not found in cart.');
        }

        Session::put('cart', array_values($cart)); // Re-index the array
        return back()->with('success', 'Cart updated successfully.');
    }

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

        $cart = array_filter($cart, function ($item) use ($productId, $productVariantId) {
            return !($item['product_id'] == $productId && ($item['product_variant_id'] == $productVariantId));
        });

        if (count($cart) < $initialCartCount) {
            Session::put('cart', array_values($cart));
            return back()->with('success', 'Item removed from cart.');
        }

        return back()->with('error', 'Item not found in cart.');
    }
}