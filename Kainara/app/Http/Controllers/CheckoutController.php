<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductVariant; // Don't forget to import ProductVariant
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
                // Use the price from the session item, as it might be a variant price
                $item['total_price'] = $item['price'] * $item['quantity'];
                $subtotal += $item['total_price'];
            } else {
                // If product not found, remove it or handle error
                // For this example, we'll just set name to 'Unknown Product'
                $item['product_name'] = 'Unknown Product';
                $item['product_image'] = 'path/to/default/image.jpg'; // Or a placeholder
                $item['total_price'] = 0; // Don't add to subtotal
            }
        }

        return view('products.checkout', compact('cartItems', 'subtotal'));
    }

    public function addToCheckout(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'selected_size' => 'nullable|string', // Size is optional for some products
        ]);

        $productId = $request->input('product_id');
        $quantity = $request->input('quantity');
        $selectedSize = $request->input('selected_size');

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
                // Handle case where variant with selected size isn't found
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
            } else {
                // If no variant and no size selected, use base product price and no variant ID
            }
        }

        $cart = Session::get('cart', []);

        $itemFound = false;
        foreach ($cart as $key => $cartItem) {
            if ($cartItem['product_id'] == $productId &&
                (
                    ($productVariantId && $cartItem['product_variant_id'] == $productVariantId) ||
                    (!$productVariantId && !$cartItem['product_variant_id'])
                )
            ) {
                $cart[$key]['quantity'] += $quantity;
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

        return redirect()->route('checkout.show')->with('success', 'Product added to checkout!');
    }
}