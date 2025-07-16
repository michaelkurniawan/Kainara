<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StoriesController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CartController; 
use App\Http\Controllers\LatestStoriesController; 
use App\Http\Controllers\AdminTestController;

use App\Http\Controllers\ArtisanRegistrationController;

Route::get('/', [LatestStoriesController::class, 'index'])->name('welcome');

Route::get('/join-as-artisan', [ArtisanRegistrationController::class, 'showForm'])->name('artisan.register');

// Route untuk MENYIMPAN data form (method POST)
Route::post('/join-as-artisan', [ArtisanRegistrationController::class, 'store'])->name('artisan.register.store');

Route::prefix('admin/test-submissions')->name('admin.test.')->group(function () {
    Route::get('/', [AdminTestController::class, 'index'])->name('submissions');
    
    // Route BARU untuk menampilkan halaman detail
    Route::get('/{profile}', [AdminTestController::class, 'show'])->name('show');

    // Route untuk approve (sekarang POST)
    Route::post('/{profile}/approve', [AdminTestController::class, 'approve'])->name('approve');

    // Route untuk reject (sekarang POST)
    Route::post('/{profile}/reject', [AdminTestController::class, 'reject'])->name('reject');
});




Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');

Route::get('/stories', [StoriesController::class, 'index'])->name('Stories.ListStories');
Route::get('/stories/{slug}', [StoriesController::class, 'show'])->name('Stories.DetailStories');

Route::get('/trynotif', function () {
    return view('Notification.try-notif');
});

Route::get('/checkout', [CheckoutController::class, 'showCheckoutPage'])->name('checkout.show');
Route::post('/checkout/add', [CheckoutController::class, 'addToCheckout'])->name('checkout.add');

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');

require __DIR__.'/admin.php';
