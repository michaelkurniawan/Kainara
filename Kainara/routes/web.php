<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController; // Controller untuk produk user
use App\Http\Controllers\ProfileController; // Controller untuk profil user
use App\Http\Controllers\Admin\AdminProductController; // Controller admin produk
use App\Http\Controllers\Admin\AdminUserController; // Controller admin pengguna (dari setup sebelumnya)
use App\Http\Controllers\Admin\AdminDashboardController; // Controller admin dashboard (dari setup sebelumnya)
use App\Http\Controllers\Admin\AdminCategoryController; // Controller admin kategori (dari setup sebelumnya, jika digunakan)
use App\Http\Controllers\Admin\AdminArticlesController; // Controller admin articles (dari setup sebelumnya, jika digunakan)
use App\Http\Controllers\StoriesController;

// Rute umum untuk halaman utama (welcome)
Route::get('/', function () {
    return view('welcome');
});

// Rute untuk pengguna - Menampilkan produk
Route::get('/products', [ProductController::class, 'index'])->name('products.index'); // Halaman beranda user menampilkan semua produk
Route::get('/products/{id}', [ProductController::class, 'show'])->name('products.show'); // Halaman detail produk user

// Rute untuk dashboard pengguna (terlindungi autentikasi dan verifikasi email)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Rute untuk profil pengguna - Mengelola akun pengguna (terlindungi autentikasi)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Rute untuk admin
// Semua rute di bawah prefix 'admin' akan dilindungi oleh middleware 'auth' dan 'is_admin'
Route::prefix('admin')->middleware(['auth', 'is_admin'])->name('admin.')->group(function () {
    // Dashboard Admin
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Manajemen Produk Admin (menggunakan rute spesifik yang Anda berikan)
    Route::get('/products', [AdminProductController::class, 'index'])->name('products.index');
    Route::get('/products/create', [AdminProductController::class, 'create'])->name('products.create');
    Route::post('/products', [AdminProductController::class, 'store'])->name('products.store');
    Route::get('/products/{id}/edit', [AdminProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{id}', [AdminProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{id}', [AdminProductController::class, 'destroy'])->name('products.destroy');

    // Manajemen Articles Admin (menggunakan rute spesifik yang Anda berikan)
    Route::get('/articles', [AdminArticlesController::class, 'index'])->name('articles.index');
    Route::get('/articles/create', [AdminArticlesController::class, 'create'])->name('articles.create');
    Route::post('/articles', [AdminArticlesController::class, 'store'])->name('articles.store');
    Route::get('/articles/{id}/edit', [AdminArticlesController::class, 'edit'])->name('articles.edit');
    Route::put('/articles/{id}', [AdminArticlesController::class, 'update'])->name('articles.update');
    Route::delete('/articles/{id}', [AdminArticlesController::class, 'destroy'])->name('articles.destroy');

    // Manajemen Pengguna Admin (tetap menggunakan resource controller dari setup sebelumnya)
    Route::resource('users', AdminUserController::class);

    // Manajemen Kategori Admin (tetap menggunakan resource controller dari setup sebelumnya, jika digunakan)
    Route::resource('categories', AdminCategoryController::class);

    // Tambahkan rute admin lainnya di sini jika diperlukan
});

// Memuat rute autentikasi Laravel (dari vendor seperti Breeze/Jetstream)
require __DIR__.'/auth.php';
Route::get('/stories', [StoriesController::class, 'index'])->name('Stories.ListStories');

Route::get('/stories/{slug}', [StoriesController::class, 'show'])->name('Stories.DetailStories');

Route::get('/trynotif', function () {
    return view('Notification.try-notif'); // Ini akan me-load view 'demo.blade.php'
});
