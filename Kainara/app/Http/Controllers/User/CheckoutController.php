<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\UserAddress;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    public function showCheckoutPage()
    {
        $user = Auth::user();

        $userAddresses = $user ? $user->addresses()->get() : collect();

        $cartItems = Session::get('cart', []);
        $subtotal = 0;

        foreach ($cartItems as &$item) {
            $product = Product::find($item['product_id']);
            if ($product) {
                $item['product_name'] = $product->name;
                $item['product_image'] = $product->image;

                if (isset($item['product_variant_id']) && $item['product_variant_id']) {
                    $variant = ProductVariant::find($item['product_variant_id']);
                    if ($variant) {
                        $item['price'] = $variant->price ?: $product->price;
                        $item['variant_size'] = $variant->size;
                        $item['variant_color'] = $variant->color;
                    } else {
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
                // If product not found, remove it from the cart to prevent issues
                // Note: This modifies the array during iteration, which can be tricky.
                // A safer approach might be to build a new $validCartItems array.
                // For simplicity here, we're setting placeholders.
                $item['product_name'] = 'Unknown Product';
                $item['product_image'] = 'https://placehold.co/80x80/cccccc/333333?text=No+Image'; // Placeholder image
                $item['price'] = 0;
                $item['total_price'] = 0;
            }
        }

        // After the loop, if you removed items, you'd want to re-save the session.
        // For now, let's just make sure the iteration safety note is there.

        $selectedAddressId = null;
        $address = null;

        if ($user && $userAddresses->isNotEmpty()) {
            if (Session::has('selected_address_id')) {
                $selectedAddressId = Session::get('selected_address_id');
                $address = $userAddresses->firstWhere('id', $selectedAddressId);
                if (!$address) {
                    $selectedAddressId = null;
                    // Flash notification if previously selected address is no longer valid
                    session()->flash('notification', [
                        'type' => 'warning',
                        'title' => 'Address Not Found!',
                        'message' => 'The previously selected address could not be found. Please choose another one.',
                        'hasActions' => false
                    ]);
                }
            }

            if (!$selectedAddressId) {
                $defaultAddress = $userAddresses->firstWhere('is_default', true);
                if ($defaultAddress) {
                    $selectedAddressId = $defaultAddress->id;
                    $address = $defaultAddress;
                } elseif ($userAddresses->isNotEmpty()) {
                    $selectedAddressId = $userAddresses->first()->id;
                    $address = $userAddresses->first();
                }
            }
        }

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
        if ($selectedSize) {
            $productVariant = ProductVariant::where('product_id', $productId)
                                            ->where('size', $selectedSize)
                                            ->first();
        } else {
            $productVariant = ProductVariant::where('product_id', $productId)
                                            ->where('size', 'One Size')
                                            ->first();
        }

        if ($productVariant) {
            $price = $productVariant->price ?: $product->price;
            $productVariantId = $productVariant->id;
            $variantColor = $productVariant->color;
            if ($productVariant->stock < $quantity) {
                return back()->with('notification', [
                    'type' => 'error',
                    'title' => 'Limited Stock!',
                    'message' => 'Sorry, only ' . $productVariant->stock . ' items are available for the selected variant.',
                    'hasActions' => false
                ]);
            }
        } else {
            if ($selectedSize && $selectedSize !== 'One Size') {
                return back()->with('notification', [
                    'type' => 'error',
                    'title' => 'Size Not Available!',
                    'message' => 'The selected size is currently out of stock or not available for this product.',
                    'hasActions' => false
                ]);
            }
            // If it's a product without specific size variants and no 'One Size' variant found,
            // or if it's meant to be a simple product, continue without variant details.
            // You might want to add a stock check for the base product here if applicable.
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
                'product_name' => $product->name,
                'product_image' => $product->image,
            ];
        }

        Session::put('cart', $cart);

        if ($action === 'buy_now') {
            return redirect()->route('checkout.show')->with('notification', [
                'type' => 'success',
                'title' => 'Proceeding to Checkout!',
                'message' => 'Your selected item is ready for checkout. Please review your order.',
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