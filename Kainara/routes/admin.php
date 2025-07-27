<?php

use App\Http\Controllers\Admin\Auth\AdminSessionController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminProfileController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminProductsController;
use App\Http\Controllers\Admin\AdminArticlesController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\AdminAffiliateReqController; 
use App\Http\Controllers\Admin\AdminVendorController; 
use App\Http\Controllers\Admin\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Admin\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Admin\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Admin\Auth\NewPasswordController;
use App\Http\Controllers\Admin\Auth\PasswordController;
use App\Http\Controllers\Admin\Auth\PasswordResetLinkController;
use App\Http\Controllers\Admin\Auth\RegisteredUserController;
use App\Http\Controllers\Admin\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('login', [AdminSessionController::class, 'create'])->name('login');
        Route::post('login', [AdminSessionController::class, 'store']);
    });

    Route::middleware(['adminAuth'])->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        Route::get('/profile', [AdminProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [AdminProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [AdminProfileController::class, 'destroy'])->name('profile.destroy');

        Route::get('verify-email', EmailVerificationPromptController::class)->name('verification.notice');
        Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
            ->middleware(['signed', 'throttle:6,1'])
            ->name('verification.verify');
        Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
            ->middleware('throttle:6,1')
            ->name('verification.send');

        Route::resource('users', AdminUserController::class);

        // Admin Products Management Route
        Route::resource('products', AdminProductsController::class);

        // Admin Articles Management Route
        Route::resource('articles', AdminArticlesController::class);

        // Admin Order Management Route
        Route::resource('orders', AdminOrderController::class);
        Route::get('orders/{order}/download-label', [AdminOrderController::class, 'downloadShippingLabel'])->name('orders.download-label');

        // Password Update Route
        Route::put('password', [PasswordController::class, 'update'])->name('password.update');

        // Admin Test Submissions Routes (Combined from the second block)
        Route::prefix('affiliations')->name('affiliations.')->group(function () {
            Route::get('/', [AdminAffiliateReqController::class, 'index'])->name('index');
            Route::get('/{profile}', [AdminAffiliateReqController::class, 'show'])->name('show');
            Route::post('/{profile}/approve', [AdminAffiliateReqController::class, 'approve'])->name('approve');
            Route::post('/{profile}/reject', [AdminAffiliateReqController::class, 'reject'])->name('reject');
        });

        Route::resource('vendors', AdminVendorController::class); // Added Vendor CRUD routes

        // Admin Logout Route
        Route::post('logout', [AdminSessionController::class, 'destroy'])->name('logout');
    });
});