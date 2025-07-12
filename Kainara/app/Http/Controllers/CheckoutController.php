<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function showCheckoutPage()
    {
        $address = [
            'type' => 'Home',
            'street' => 'Jl. Pakuan No.3',
            'sub_district' => 'Sumur Batu',
            'district' => 'Kec. Babakan Madang',
            'city' => 'Kabupaten Bogor',
            'province' => 'Jawa Barat',
            'postal_code' => '16810'
        ];

        $cartItems = [
            [ 'id' => 1, 'name' => 'Batik Parang Rusak', 'size' => 'XL', 'quantity' => 1, 'price' => 125000, 'image' => 'images/batik_parang_rusak.jpg' ],
            [ 'id' => 2, 'name' => 'Batik Parang Rusak', 'size' => 'XL', 'quantity' => 1, 'price' => 125000, 'image' => 'images/batik_parang_rusak.jpg' ],
            [ 'id' => 3, 'name' => 'Batik Parang Rusak', 'size' => 'XL', 'quantity' => 1, 'price' => 125000, 'image' => 'images/batik_parang_rusak.jpg' ],
            [ 'id' => 4, 'name' => 'Batik Parang Rusak', 'size' => 'XL', 'quantity' => 1, 'price' => 125000, 'image' => 'images/batik_parang_rusak.jpg' ],
            [ 'id' => 5, 'name' => 'Batik Parang Rusak', 'size' => 'XL', 'quantity' => 1, 'price' => 125000, 'image' => 'images/batik_parang_rusak.jpg' ],
            [ 'id' => 6, 'name' => 'Batik Parang Rusak', 'size' => 'XL', 'quantity' => 1, 'price' => 125000, 'image' => 'images/batik_parang_rusak.jpg' ],
        ];
        
        $subtotal = collect($cartItems)->sum(fn($item) => $item['price'] * $item['quantity']);
        $shipping = 0;
        $total = $subtotal + $shipping;

        return view('products.checkout', compact('address', 'cartItems', 'subtotal', 'shipping', 'total'));
    }
}