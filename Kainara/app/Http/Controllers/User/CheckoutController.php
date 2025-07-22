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
        $cartItems = Session::get('cart', []);
        $subtotal = 0;

        foreach ($cartItems as &$item) {
            $product = Product::find($item['product_id']);
            if ($product) {
                $item['product_name'] = $product->name;
                $item['product_image'] = $product->image;
                $item['total_price'] = $item['price'] * $item['quantity'];
                $subtotal += $item['total_price'];
            } else {
                $item['product_name'] = 'Unknown Product';
                $item['product_image'] = 'path/to/default/image.jpg'; 
                $item['total_price'] = 0;
            }
        }

        $address = Session::get('user_address', [
            'type' => 'Home',
            'street' => 'Jl. Contoh No.123',
            'sub_district' => 'Kel. Contoh',
            'district' => 'Kec. Contoh',
            'city' => 'Kota Contoh',
            'province' => 'Provinsi Contoh',
            'postal_code' => '12345'
        ]);


        return view('products.checkout', compact('cartItems', 'subtotal', 'address'));
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

        if ($selectedSize) {
            $variantQuery = ProductVariant::where('product_id', $productId)
                                         ->where('size', $selectedSize);

            $productVariant = $variantQuery->first();

            if ($productVariant) {
                $price = $productVariant->price ?: $product->price;
                $productVariantId = $productVariant->id;
                $variantColor = $productVariant->color;
                if ($productVariant->stock < $quantity) {
                    return back()->with('error', 'Not enough stock for the selected variant.');
                }
            } else {
                return back()->with('error', 'Selected size is not available for this product.');
            }
        } else {
            $productVariant = ProductVariant::where('product_id', $productId)->where('size', 'One Size')->first();
            if ($productVariant) {
                $price = $productVariant->price ?: $product->price;
                $productVariantId = $productVariant->id;
                $variantColor = $productVariant->color;
                if ($productVariant->stock < $quantity) {
                    return back()->with('error', 'Not enough stock for the selected variant.');
                }
            }
        }

        if ($action === 'buy_now') {
            Session::forget('cart'); 
            $cart = [];
        } else {
            $cart = Session::get('cart', []);
        }

        $itemFound = false;
        foreach ($cart as $key => $cartItem) {
            if ($cartItem['product_id'] == $productId &&
                (
                    ($productVariantId && $cartItem['product_variant_id'] == $productVariantId) ||
                    (!$productVariantId && !$cartItem['product_variant_id'])
                )
            ) {
                if ($action === 'buy_now') {
                    $cart[$key]['quantity'] = $quantity;
                } else {
                    $cart[$key]['quantity'] += $quantity;
                }
                $itemFound = true;
                break;
            }
        }

        if (!$itemFound) {
            $cart[] = [
                'product_id' => $productId,
                'product_variant_id' => $productVariantId,
                'price' => $price,
                'quantity' => $quantity,
                'variant_size' => $selectedSize,
                'variant_color' => $variantColor,
            ];
        }

        Session::put('cart', $cart);

        // Redirect based on action
        if ($action === 'buy_now') {
            return redirect()->route('checkout.show')->with('success', 'Proceeding to checkout with your selection!');
        } else {
            return back()->with('success', 'Product added to cart!');
        }
    }
}