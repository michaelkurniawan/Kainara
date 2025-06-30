<?php

use App\Http\Controllers\Admin\Auth\AdminSessionController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminProfileController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminProductsController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController; 
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->group(function (){
    // Admin Login Routes
    Route::middleware('redirectAdmin')->group(function () {
        Route::get('login', [AdminSessionController::class, 'create'])->name('login');
        Route::post('login', [AdminSessionController::class, 'store']);
    });

    Route::middleware(['auth', 'admin'])->group(function () {
        // Admin Dashboard Routes
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])->name('password.confirm');
        // Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

        // Admin Profile Routes
        Route::get('/profile', [AdminProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [AdminProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [AdminProfileController::class, 'destroy'])->name('profile.destroy');

        Route::get('verify-email', EmailVerificationPromptController::class)->name('verification.notice');

        Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)->middleware(['signed', 'throttle:6,1'])->name('verification.verify');

        Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])->middleware('throttle:6,1')->name('verification.send');

        // Admin User Management Route
        Route::resource('users', AdminUserController::class);

        // Admin Products Management Route
        Route::resource('products', AdminProductsController::class);
        
        Route::put('password', [PasswordController::class, 'update'])->name('password.update'); 
        // Admin Logout Route
        Route::post('logout', [AdminSessionController::class, 'destroy'])->name('logout');
    });
});