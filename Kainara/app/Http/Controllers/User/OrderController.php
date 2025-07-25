<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    /**
     * Memproses permintaan checkout, membuat Order, dan mengarahkan ke pembayaran.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function processCheckout(Request $request)
    {
        // Validasi data input dari formulir checkout
        $validatedData = $request->validate([
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'payment_method' => 'required|in:transfer_bank,credit_card,e_wallet',
            'total_amount' => 'required|numeric|min:0', // Ini adalah grand_total dari frontend

            // Hidden inputs untuk detail alamat pengiriman yang dipilih dari modal/form
            'address_type_input' => 'nullable|string',
            'street_input' => 'nullable|string',
            'sub_district_input' => 'nullable|string',
            'district_input' => 'nullable|string',
            'city_input' => 'nullable|string',
            'province_input' => 'nullable|string',
            'postal_code_input' => 'nullable|string',
            'user_name_input' => 'nullable|string',
            'user_phone_input' => 'nullable|string',
        ]);

        $cartItems = Session::get('cart', []);
        // Pastikan keranjang tidak kosong sebelum melanjutkan
        if (empty($cartItems)) {
            return redirect()->route('checkout.show')->with('error', 'Keranjang Anda kosong. Mohon tambahkan item sebelum checkout.');
        }

        DB::beginTransaction(); // Memulai transaksi database untuk memastikan atomisitas
        try {
            $shippingCost = 0; // Placeholder: Anda bisa menghitung biaya pengiriman di sini (misalnya dari API kurir)

            $calculatedSubtotal = 0;
            // Iterasi melalui item keranjang untuk melakukan validasi stok di backend dan menghitung subtotal yang sebenarnya
            foreach ($cartItems as $item) {
                $product = Product::find($item['product_id']);
                if (!$product) {
                    DB::rollBack(); // Rollback transaksi jika ada produk yang tidak ditemukan
                    return redirect()->back()->with('error', 'Satu atau lebih produk di keranjang tidak ditemukan.')->withInput();
                }

                $itemPrice = $product->price;
                $stockAvailable = 0; // Stok awal

                // Jika produk memiliki varian, cari varian yang sesuai
                if (isset($item['product_variant_id']) && $item['product_variant_id']) {
                    $variant = ProductVariant::find($item['product_variant_id']);
                    if (!$variant) {
                        DB::rollBack(); // Rollback transaksi jika varian tidak ditemukan
                        return redirect()->back()->with('error', 'Satu atau lebih varian produk di keranjang tidak ditemukan.')->withInput();
                    }
                    $itemPrice = $variant->price ?: $product->price; // Gunakan harga varian jika ada, jika tidak harga produk
                    $stockAvailable = $variant->stock;

                    // Periksa apakah stok yang cukup tersedia untuk varian
                    if ($stockAvailable < $item['quantity']) {
                        DB::rollBack(); // Rollback transaksi jika stok tidak cukup
                        return redirect()->back()->with('error', 'Stok tidak cukup untuk ' . $product->name . ' (' . $variant->size . '). Tersedia: ' . $stockAvailable)->withInput();
                    }
                    // Kurangi stok varian segera setelah validasi
                    $variant->decrement('stock', $item['quantity']);

                } else {
                    // Ini berlaku jika produk tidak memiliki varian, atau 'One Size' ditangani langsung di model Product.
                    // Anda bisa mengaktifkan blok ini jika model Product Anda langsung menyimpan stok untuk item non-varian.
                    // if (isset($product->stock) && $product->stock < $item['quantity']) {
                    //     DB::rollBack();
                    //     return redirect()->back()->with('error', 'Stok tidak cukup untuk ' . $product->name . '. Tersedia: ' . $product->stock)->withInput();
                    // }
                    // if (isset($product->stock)) { // Hanya kurangi stok jika stok ada di produk itu sendiri
                    //     $product->decrement('stock', $item['quantity']);
                    // }
                }
                $calculatedSubtotal += $itemPrice * $item['quantity'];
            }

            // Hitung grand total berdasarkan subtotal yang dihitung dan biaya pengiriman
            $grandTotalCalculated = $calculatedSubtotal + $shippingCost;

            // Verifikasi total_amount yang dikirim dari frontend terhadap perhitungan backend (cek keamanan)
            if (abs($validatedData['total_amount'] - $grandTotalCalculated) > 0.01) { // Izinkan perbedaan kecil karena floating point
                DB::rollBack(); // Rollback transaksi
                Log::warning('Jumlah total frontend tidak cocok dengan perhitungan backend.', [
                    'frontend_total' => $validatedData['total_amount'],
                    'backend_total' => $grandTotalCalculated,
                    'user_id' => Auth::id(), // Log ID pengguna jika terautentikasi
                ]);
                return redirect()->back()->with('error', 'Terjadi perbedaan harga. Mohon coba lagi atau refresh halaman.')->withInput();
            }

            // Siapkan data alamat pengiriman dari input tersembunyi
            $shippingAddressData = [
                'type' => $request->input('address_type_input'),
                'name' => $request->input('user_name_input'),
                'phone' => $request->input('user_phone_input'),
                'street' => $request->input('street_input'),
                'sub_district' => $request->input('sub_district_input'),
                'district' => $request->input('district_input'),
                'city' => $request->input('city_input'),
                'province' => $request->input('province_input'),
                'postal_code' => $request->input('postal_code_input'),
                'country' => 'Indonesia', // Asumsi default atau ambil dari input jika ada
            ];

            // Buat record Order baru
            $order = Order::create([
                'user_id' => Auth::id(), // ID pengguna terautentikasi, atau null jika guest checkout didukung
                // 'address_id' => null, // Biarkan null jika Anda tidak menggunakan foreign key ke user_addresses
                'subtotal' => $calculatedSubtotal, // Ini adalah subtotal produk saja
                'shipping_cost' => $shippingCost,
                // 'grand_total' tidak perlu diisi karena akan dihitung via accessor
                'status' => 'Awaiting Payment', // Status awal untuk pesanan baru
                'payment_method' => $validatedData['payment_method'],
                'original_user_name' => Auth::check() ? Auth::user()->first_name . ' ' . Auth::user()->last_name : ($validatedData['first_name'] . ' ' . $validatedData['last_name']),
                'original_user_email' => Auth::check() ? Auth::user()->email : $validatedData['email'],
                'shipping_recipient_name' => $shippingAddressData['name'],
                'shipping_phone' => $shippingAddressData['phone'],
                // Simpan alamat lengkap sebagai string
                'shipping_address' => $shippingAddressData['street'] .
                                      ($shippingAddressData['sub_district'] ? ', ' . $shippingAddressData['sub_district'] : '') .
                                      ($shippingAddressData['district'] ? ', ' . $shippingAddressData['district'] : ''),
                'shipping_country' => $shippingAddressData['country'],
                'shipping_city' => $shippingAddressData['city'],
                'shipping_province' => $shippingAddressData['province'],
                'shipping_postal_code' => $shippingAddressData['postal_code'],
            ]);

            // Buat record OrderItem untuk setiap item di keranjang
            foreach ($cartItems as $itemData) {
                // Ambil kembali detail produk dari DB untuk keamanan dan konsistensi
                $product = Product::find($itemData['product_id']);
                $productName = $product ? $product->name : 'Unknown Product';
                $productImage = $product ? $product->image : 'https://placehold.co/80x80/cccccc/333333?text=No+Image';

                // Tentukan harga sebenarnya untuk item pesanan, prioritaskan harga varian
                $actualPrice = $product->price;
                $variantSize = $itemData['variant_size'] ?? null;
                $variantColor = $itemData['variant_color'] ?? null;

                if (isset($itemData['product_variant_id']) && $itemData['product_variant_id']) {
                    $variant = ProductVariant::find($itemData['product_variant_id']);
                    if ($variant) {
                        $actualPrice = $variant->price ?: $product->price;
                        $variantSize = $variant->size;
                        $variantColor = $variant->color;
                    }
                }

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $itemData['product_id'],
                    'product_variant_id' => $itemData['product_variant_id'] ?? null,
                    'quantity' => $itemData['quantity'],
                    'price' => $actualPrice, // Gunakan harga yang divalidasi dari database
                    'product_name' => $productName,
                    'product_image' => $productImage,
                    'variant_size' => $variantSize,
                    'variant_color' => $variantColor,
                ]);
            }

            Session::forget('cart'); // Bersihkan keranjang setelah order berhasil dibuat
            DB::commit(); // Commit transaksi database

            // Arahkan berdasarkan metode pembayaran yang dipilih
            if ($validatedData['payment_method'] === 'credit_card') {
                return redirect()->route('stripe.payment.form', $order->id);
            } elseif ($validatedData['payment_method'] === 'transfer_bank') {
                return redirect()->route('order.awaitingPayment', $order->id)->with('info', 'Mohon selesaikan transfer bank dalam 24 jam.');
            } else { // e_wallet dan metode non-kartu lainnya
                return redirect()->route('order.awaitingPayment', $order->id)->with('info', 'Mohon ikuti instruksi untuk metode pembayaran yang Anda pilih.');
            }

        } catch (ValidationException $e) {
            DB::rollBack(); // Rollback transaksi jika validasi gagal
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback transaksi untuk pengecualian lainnya
            Log::error('Order processing failed: ' . $e->getMessage(), ['request' => $request->all()]);
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memproses pesanan. Mohon coba lagi. ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Menampilkan halaman detail pesanan.
     *
     * @param \App\Models\Order $order
     * @return \Illuminate\View\View
     */
    public function showOrderDetails(Order $order)
    {
        // Pastikan hubungan (payment, orderItems) dimuat jika diperlukan di view
        $order->load(['payment', 'orderItems.productVariant']);
        return view('welcome', compact('order'));
    }

    /**
     * Menampilkan halaman sukses pesanan setelah pembayaran berhasil.
     *
     * @param \App\Models\Order $order
     * @return \Illuminate\View\View
     */
    public function showOrderSuccess(Order $order)
    {
        return view('order.success', compact('order'));
    }

    /**
     * Menampilkan halaman kegagalan pesanan setelah percobaan pembayaran gagal.
     *
     * @param \App\Models\Order $order
     * @return \Illuminate\View\View
     */
    public function showOrderFail(Order $order)
    {
        return view('order.fail', compact('order'));
    }

    /**
     * Menampilkan halaman untuk pesanan yang menunggu pembayaran (untuk metode non-kartu).
     *
     * @param \App\Models\Order $order
     * @return \Illuminate\View\View
     */
    public function showOrderAwaitingPayment(Order $order)
    {
        return view('order.awaiting_payment', compact('order'));
    }
}