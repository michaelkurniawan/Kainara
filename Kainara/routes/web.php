<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StoriesController;
use App\Http\Controllers\CheckoutController;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('/products', [ProductController::class, 'index'])->name('products.index'); 
Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');

Route::get('/stories', [StoriesController::class, 'index'])->name('Stories.ListStories');

Route::get('/stories/{slug}', [StoriesController::class, 'show'])->name('Stories.DetailStories');

Route::get('/trynotif', function () {
    return view('Notification.try-notif');
});

Route::get('/checkout', [CheckoutController::class, 'showCheckoutPage'])->name('checkout.show');

Route::post('/checkout/add', [CheckoutController::class, 'addToCheckout'])->name('checkout.add');

require __DIR__.'/admin.php';
