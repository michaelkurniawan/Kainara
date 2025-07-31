<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\User\ProductController;
use App\Http\Controllers\User\StoriesController;
use App\Http\Controllers\User\CheckoutController;
use App\Http\Controllers\User\CartController;
use App\Http\Controllers\User\LatestStoriesController;
use App\Http\Controllers\User\ArtisanRegistrationController;
use App\Http\Controllers\User\OrderController;
use App\Http\Controllers\User\StripePaymentController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\User\AddressController;
use App\Http\Controllers\User\ReviewController;
use App\Http\Controllers\User\Auth\LoginController;
use App\Http\Controllers\User\Auth\RegisterController;
use App\Http\Controllers\User\Auth\EmailVerificationController;
use App\Http\Controllers\User\Auth\ForgotPasswordController;
use App\Http\Controllers\User\Auth\ResetPasswordController;

// Admin Routes
require __DIR__.'/admin.php';


// User Routes
Route::get('/', [LatestStoriesController::class, 'index'])->name('welcome');

Route::get('/join-as-artisan', [ArtisanRegistrationController::class, 'showForm'])->name('artisan.register');
Route::post('/join-as-artisan', [ArtisanRegistrationController::class, 'store'])->name('artisan.register.store');

Route::get('/tentangkainara', function () {
    return view('tentangkainara');
})->name('tentangkainara');

Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/gender/{gender}', [ProductController::class, 'index'])->name('products.gender.index');
Route::get('/products/category/{category_name}', [ProductController::class, 'index'])->name('products.category.index');
Route::get('/products/fabric/{slug}', [ProductController::class, 'showFabricProduct'])->name('products.detailkain');
Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');


Route::get('/stories', [StoriesController::class, 'index'])->name('Stories.ListStories');
Route::get('/stories/{slug}', [StoriesController::class, 'show'])->name('Stories.DetailStories');

Route::middleware('auth')->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'showCheckoutPage'])->name('checkout.show');
    Route::post('/checkout/add', [CheckoutController::class, 'addToCheckout'])->name('checkout.add'); // This is the route to protect

    Route::post('/order/process', [OrderController::class, 'processCheckout'])->name('order.process');
    Route::get('/order/{order}/details', [OrderController::class, 'showOrderDetails'])->name('order.details');
    Route::get('/order/{order}/success', [OrderController::class, 'showOrderSuccess'])->name('order.success');
    Route::get('/order/{order}/fail', [OrderController::class, 'showOrderFail'])->name('order.fail');
    Route::get('/order/{order}/awaiting-payment', [OrderController::class, 'showOrderAwaitingPayment'])->name('order.awaitingPayment');
    Route::post('/order/{order}/complete', [OrderController::class, 'completeOrder'])->name('order.complete');
    Route::delete('/my-orders/{order}/cancel', [OrderController::class, 'cancelOrder'])->name('order.cancel');
    Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::get('/my-orders', [OrderController::class, 'myOrders'])->name('my.orders');
    Route::get('/orders/{order}/modal-details', [OrderController::class, 'showOrderModalDetails'])->name('order.modal.details');

    Route::get('/payment/stripe/{order}', [StripePaymentController::class, 'showPaymentForm'])->name('stripe.payment.form');
    Route::post('/payment/stripe/{order}/confirm', [StripePaymentController::class, 'confirmPayment'])->name('stripe.payment.confirm');
    Route::get('/order/{order}/continue-payment', [StripePaymentController::class, 'showPaymentForm'])->name('payment.continue');

    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::post('/addresses', [AddressController::class, 'store'])->name('addresses.store');
    Route::get('/addresses/{address}/edit', [AddressController::class, 'edit'])->name('addresses.edit'); // For fetching data for edit modal via AJAX
    Route::put('/addresses/{address}', [AddressController::class, 'update'])->name('addresses.update');
    Route::delete('/addresses/{address}', [AddressController::class, 'destroy'])->name('addresses.destroy');
    Route::post('/profile/picture', [ProfileController::class, 'updateProfilePicture'])->name('profile.update_picture');
    Route::put('/profile/personal-info', [ProfileController::class, 'updatePersonalInformation'])->name('profile.update_personal_info');
    Route::get('/my-orders', [OrderController::class, 'myOrders'])->name('my.orders');

    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
    Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
});


Route::middleware(['guest'])->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);

    Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');

    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');
});

Route::get('/email/verify', [EmailVerificationController::class, 'show'])
    ->middleware('auth')
    ->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
    ->middleware(['signed', 'throttle:6,1'])
    ->name('verification.verify');

Route::post('/email/verification-notification', [EmailVerificationController::class, 'resend'])
    ->middleware(['auth', 'throttle:6,1'])
    ->name('verification.send');