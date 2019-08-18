<?php

namespace ChrisBraybrooke\LaravelStripePaymentIntent;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;

class ServiceProvider extends LaravelServiceProvider
{
    public function boot()
    {
        $this->app->bind('stripe-laravel.intent', function ($app) {
            return new StripePaymentIntent(config('services.stripe.key'), config('services.stripe.secret'));
        });
        $this->app->alias('stripe-laravel.intent', 'ChrisBraybrooke\LaravelStripePaymentIntent\StripePaymentIntent');

        if (config('app.env') === 'local') {
            $this->handleMigrations();
        }
        $this->handleRoutes();
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'laravel-stripe');
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'laravel-stripe');

        Blade::component('laravel-stripe::payment-fields', 'paymentFields');
        Blade::component('laravel-stripe::payment-scripts', 'paymentScripts');
    }

    /**
     * Handle the migrations that are required by this package.
     *
     * @return void
     */
    private function handleMigrations()
    {
        $this->publishes([
            __DIR__.'/../database/migrations/2019_08_18_153506_create_payment_records_table.php.stub' =>
            database_path('migrations/2019_08_18_153506_create_payment_records_table.php'),
        ], 'payment-migrations');
    }

    /**
     * Register the web and api routes.
     *
     * @return void
     */
    private function handleRoutes()
    {
        Route::group([
            'namespace' => 'ChrisBraybrooke\LaravelStripePaymentIntent\Http\Controllers',
            'middleware' => ['bindings']
        ], function () {
            $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
        });
    }
}
