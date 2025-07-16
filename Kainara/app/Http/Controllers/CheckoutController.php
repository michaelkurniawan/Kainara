<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Session;

class CheckoutController extends Controller
{
    public function showCheckoutPage()
    {
        // Initialize dummy addresses in session if not already present
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
                    'city' => '',
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

        foreach ($cartItems as &$item) {
            $product = Product::find($item['product_id']);
            if ($product) {
                $item['product_name'] = $product->name;
                $item['product_image'] = $product->image;
                $item['total_price'] = $item['price'] * $item['quantity'];
                $subtotal += $item['total_price'];
            } else {
                $item['product_name'] = 'Unknown Product';
                $item['product_image'] = 'https://placehold.co/80x80/cccccc/333333?text=No+Image'; // Placeholder
                $item['total_price'] = 0;
            }
        }

        // Determine the selected address ID
        $selectedAddressId = null;
        if (Session::has('selected_address_id')) {
            // Priority 1: From flash session (e.g., after adding/editing an address)
            $selectedAddressId = Session::get('selected_address_id');
        } else {
            // Priority 2: Find the primary address
            $defaultAddress = collect($userAddresses)->firstWhere('is_primary');
            if ($defaultAddress) {
                $selectedAddressId = $defaultAddress['id'];
            } elseif (!empty($userAddresses)) {
                // Priority 3: If no primary, take the first available address
                $selectedAddressId = $userAddresses[0]['id'];
            }
        }

        // Get the actual address data for display on the main page
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