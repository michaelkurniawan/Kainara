<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Stripe\Stripe; // Impor kelas Stripe utama

class StripeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     * Daftarkan StripeClient ke container Laravel.
     * Ini memungkinkan kita untuk menggunakan dependency injection di controller.
     */
    public function register(): void
    {
        $this->app->singleton(\Stripe\StripeClient::class, function ($app) {
            return new \Stripe\StripeClient(config('services.stripe.secret'));
        });
    }

    /**
     * Bootstrap services.
     * Atur kunci API Stripe secara global.
     */
    public function boot(): void
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }
}