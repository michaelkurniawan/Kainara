<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User; // Impor model User
use App\Models\Product; // Impor model Product
use App\Models\Category; // Impor model Category (jika Anda menggunakannya)
// use App\Models\Order; // Jika Anda punya model Order

class AdminDashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard admin dengan ringkasan data.
     */
    public function index()
    {
        // Mendapatkan total pengguna
        $totalUsers = User::count();
        // Mendapatkan total admin
        $totalAdmins = User::where('role', 'admin')->count();
        // Mendapatkan total produk
        $totalProducts = Product::count();
        // Mendapatkan total produk
        $totalArticles = Article::count();
        // Mendapatkan total kategori (jika model Category ada)
        $totalCategories = Category::count();
        // Mendapatkan total pesanan (jika Anda punya model Order)
        // $totalOrders = Order::count();

        // Mengirim data ke view dashboard admin
        return view('admin.dashboard', compact('totalUsers', 'totalAdmins', 'totalProducts', 'totalArticles', 'totalCategories')); // Tambahkan 'totalOrders' jika digunakan
    }
}
