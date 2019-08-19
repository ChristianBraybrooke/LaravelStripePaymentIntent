## Installation

### Composer

First, require the package from packagist.org using the snippet below in the terminal. Laravel 5.5 and greater will autoload our Service provider.

```sh
composer install christianbraybrooke/stripe-laravel-payment-intents
```

### Database

By default we will store all transactions in the database using the `payment_records` table. If this is not desirable, please place the following in your `\App\Providers\AppServiceProvider`'s `boot` method.

```php
use ChrisBraybrooke\LaravelStripePaymentIntent\Facades\StripePaymentIntent;

/**
 * Bootstrap any application services.
 *
 * @return void
 */
public function boot()
{
    StripePaymentIntent::dontSavePaymentRecords();
}
```

Next, you will need to publish the required migrations & migrate.

```sh
php artisan vendor:publish --tag="payment-migrations"

php artisan migrate
```

