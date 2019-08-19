## Installation

This package is still under development, and will require some more documentation. For help, please email chris@purplemountmedia.com.

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

### Payment Form

Out of the box, we give you a simple payment form component which will allow you to get up and running in no time. Simply add the payment feilds and scripts components to the desired page and you are good to go!

```php
@paymentFields([
  // Amount in lowest currency (pence, cents etc.).
  // Currency in valid currency code.
  // Success URL is the route we will post to completed form data to.
  'amount' =>  12500, 'currency' => 'GBP', 'successUrl' => route('payment.submit')
])
@endpaymentFields
  
// Somewhere towards the footer
@paymentScripts()
@endpaymentScripts
```

### Customisation

Currently, there are two things you can do to change the look of the payment form. These can be registered in your `\App\Providers\AppServiceProvider`'s `boot` method. 

```php
use ChrisBraybrooke\LaravelStripePaymentIntent\Facades\StripePaymentIntent;

/**
 * Bootstrap any application services.
 *
 * @return void
 */
public function boot()
{
  	// Will give the inputs on the payment form a class of: 'form-control'
    StripePaymentIntent::setInputClass('form-control');
    
  	// Will change the colour of elements like the submit button.
  	StripePaymentIntent::setThemeColor('#efefef');
}
```