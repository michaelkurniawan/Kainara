<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/tentangkainara', function () {
    return view('tentangkainara');
});

Route::get('/keranjang', function () {
    return view('keranjang');
});