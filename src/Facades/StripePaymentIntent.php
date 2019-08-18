<?php

namespace ChrisBraybrooke\LaravelStripePaymentIntent\Facades;

use Illuminate\Support\Facades\Facade as BaseFacade;

class StripePaymentIntent extends BaseFacade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'stripe-laravel.intent';
    }
}