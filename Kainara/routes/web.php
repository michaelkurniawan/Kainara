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
use App\Http\Controllers\User\Auth\LoginController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Di sini Anda dapat mendaftarkan rute web untuk aplikasi Anda. Rute ini
| dimuat oleh RouteServiceProvider dan semuanya akan ditetapkan ke
| grup middleware "web". Jadikan sesuatu yang hebat!
|
*/

// Rute Beranda
Route::get('/', [LatestStoriesController::class, 'index'])->name('welcome');

// Rute Pendaftaran Artisan
Route::get('/join-as-artisan', [ArtisanRegistrationController::class, 'showForm'])->name('artisan.register');
Route::post('/join-as-artisan', [ArtisanRegistrationController::class, 'store'])->name('artisan.register.store');

// Rute Tentang Kainara
Route::get('/tentangkainara', function () {
    return view('tentangkainara');
})->name('tentangkainara');

// Rute Produk
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/gender/{gender}', [ProductController::class, 'index'])->name('products.gender.index');
Route::get('/products/category/{category_name}', [ProductController::class, 'index'])->name('products.category.index');
Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show'); 

// Rute Cerita/Blog
Route::get('/stories', [StoriesController::class, 'index'])->name('Stories.ListStories');
Route::get('/stories/{slug}', [StoriesController::class, 'show'])->name('Stories.DetailStories');

// Rute Notifikasi (contoh)
Route::get('/trynotif', function () {
    return view('Notification.try-notif');
});

// Rute Keranjang
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');

// Rute Checkout
Route::get('/checkout', [CheckoutController::class, 'showCheckoutPage'])->name('checkout.show');
Route::post('/checkout/add', [CheckoutController::class, 'addToCheckout'])->name('checkout.add');

// Rute Proses Order
Route::post('/order/process', [OrderController::class, 'processCheckout'])->name('order.process');
Route::get('/order/{order}/details', [OrderController::class, 'showOrderDetails'])->name('order.details');
Route::get('/order/{order}/success', [OrderController::class, 'showOrderSuccess'])->name('order.success');
Route::get('/order/{order}/fail', [OrderController::class, 'showOrderFail'])->name('order.fail');
Route::get('/order/{order}/awaiting-payment', [OrderController::class, 'showOrderAwaitingPayment'])->name('order.awaitingPayment');


Route::get('/payment/stripe/{order}', [StripePaymentController::class, 'showPaymentForm'])->name('stripe.payment.form');
Route::post('/payment/stripe/{order}/confirm', [StripePaymentController::class, 'confirmPayment'])->name('stripe.payment.confirm');


Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::post('/logout', [LoginController::class, 'logout']);
    Route::get('/my-orders', [OrderController::class, 'myOrders'])->name('my.orders');
});

require __DIR__.'/admin.php';