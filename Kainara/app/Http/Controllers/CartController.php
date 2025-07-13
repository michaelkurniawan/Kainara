<?php

namespace App\Http\Controllers;

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
                unset($cartItems[$key]);
                continue;
            }

            $item['product_name'] = $product->name;
            $item['product_image'] = $product->image;

            $item_price = $item['price'];

            if (!empty($item['product_variant_id'])) {
                $variant = ProductVariant::find($item['product_variant_id']);
                if ($variant) {
                    $item['variant_size'] = $variant->size;
                    $item['variant_color'] = $variant->color;
                    $item_price = $variant->price ?: $item_price;
                } else {
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
                if ($newQuantity > 0) {
                    $availableStock = 0;
                    if ($productVariantId) {
                        $variant = ProductVariant::find($productVariantId);
                        if ($variant) $availableStock = $variant->stock;
                    } else {
                        $product = Product::find($productId);
                        if ($product) {
                            $defaultVariant = $product->variants()->where('size', 'One Size')->first();
                            if ($defaultVariant) $availableStock = $defaultVariant->stock;
                        }
                    }

                    if ($newQuantity > $availableStock) {
                        return back()->with('error', "Only {$availableStock} of {$item['product_name']} available in stock.");
                    }

                    $item['quantity'] = $newQuantity;
                    $updated = true;
                } else {
                    unset($cart[$key]);
                    $updated = true;
                }
                break;
            }
        }

        if (!$updated) {
            return back()->with('error', 'Item not found in cart.');
        }

        Session::put('cart', array_values($cart));
        return back();
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