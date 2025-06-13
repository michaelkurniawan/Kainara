<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Daftarkan middleware Anda di sini
        // Ini setara dengan $routeMiddleware di Kernel.php versi lama
        $middleware->alias([
            'is_admin' => \App\Http\Middleware\AdminMiddleware::class,
        ]);

        // Anda juga bisa mendaftarkan middleware global atau kelompok di sini jika diperlukan.
        // Contoh: $middleware->web(append: [
        //     \App\Http\Middleware\LastLoginMiddleware::class,
        // ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
