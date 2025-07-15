<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Stripe\Stripe;

class StripeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Bind the Stripe client to the container
        $this->app->singleton(\Stripe\StripeClient::class, function ($app) {
            return new \Stripe\StripeClient(config('services.stripe.secret'));
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Set the Stripe API key for the global Stripe library
        Stripe::setApiKey(config('services.stripe.secret'));
    }
}