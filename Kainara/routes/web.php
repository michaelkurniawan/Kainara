<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StoriesController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/stories', [StoriesController::class, 'index']);
