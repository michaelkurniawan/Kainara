<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StoriesController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/stories', [StoriesController::class, 'index'])->name('Stories.ListStories');

Route::get('/stories/{id}', [StoriesController::class, 'show'])->name('Stories.DetailStories');

Route::get('/trynotif', function () {
    return view('Notification.try-notif'); // Ini akan me-load view 'demo.blade.php'
});