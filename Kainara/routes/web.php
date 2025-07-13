<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StoriesController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CartController; 
use App\Http\Controllers\LatestStoriesController; 

use App\Http\Controllers\ArtisanRegistrationController;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/join-as-artisan', [ArtisanRegistrationController::class, 'showForm'])->name('artisan.register');

Route::get('/', [LatestStoriesController::class, 'index'])->name('welcome');

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
