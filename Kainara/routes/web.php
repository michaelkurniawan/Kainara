<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ArtisanRegistrationController;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/join-as-artisan', [ArtisanRegistrationController::class, 'showForm'])->name('artisan.register');