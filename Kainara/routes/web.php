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
use App\Http\Controllers\OrderController;
use App\Http\Controllers\StripePaymentController;

Route::get('/', [LatestStoriesController::class, 'index'])->name('welcome');

Route::get('/join-as-artisan', [ArtisanRegistrationController::class, 'showForm'])->name('artisan.register');
Route::post('/join-as-artisan', [ArtisanRegistrationController::class, 'store'])->name('artisan.register.store');

Route::get('/tentangkainara', function () {
    return view('tentangkainara');
})->name('tentangkainara');

Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/gender/{gender}', [ProductController::class, 'index'])->name('products.gender.index');
Route::get('/products/category/{category_name}', [ProductController::class, 'index'])->name('products.category.index');

Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');

Route::get('/stories', [StoriesController::class, 'index'])->name('Stories.ListStories');
Route::get('/stories/{slug}', [StoriesController::class, 'show'])->name('Stories.DetailStories');

Route::get('/trynotif', function () {
    return view('Notification.try-notif');
});

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');

Route::get('/checkout', [CheckoutController::class, 'showCheckoutPage'])->name('checkout.show');
Route::post('/checkout/add', [CheckoutController::class, 'addToCheckout'])->name('checkout.add');

Route::post('/order/process', [OrderController::class, 'processCheckout'])->name('order.process');
Route::get('/order/{order}/details', [OrderController::class, 'showOrderDetails'])->name('order.details');
Route::get('/order/{order}/success', [OrderController::class, 'showOrderSuccess'])->name('order.success');
Route::get('/order/{order}/fail', [OrderController::class, 'showOrderFail'])->name('order.fail');

Route::middleware('web')->group(function () {
    Route::get('/payment/stripe/{order}', [StripePaymentController::class, 'showPaymentForm'])->name('stripe.payment.form');
    Route::post('/payment/stripe/{order}/confirm', [StripePaymentController::class, 'confirmPayment'])->name('stripe.payment.confirm');
});

require __DIR__.'/admin.php';