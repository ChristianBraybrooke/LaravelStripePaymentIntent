@php
  $currencyCode = $currencyCode ?? 'GBP';
  $numberFormat = new \NumberFormatter(config('app.local') . "@currency=" . $currencyCode, \NumberFormatter::CURRENCY);
  $inputClass = \ChrisBraybrooke\LaravelStripePaymentIntent\Facades\StripePaymentIntent::getInputClass();
  $themeColor = \ChrisBraybrooke\LaravelStripePaymentIntent\Facades\StripePaymentIntent::getThemeColor();
@endphp

<div style="max-width: 500px; display: block; margin: auto;">
  <form
    method="POST"
    id="paymentForm"
    novalidate="true"
    data-amount="{{ $amount }}"
    data-currency="{{ $currencyCode }}"
    action="{{ $successUrl }}"
  >
    @csrf

    <div id="paymentError" style="display: none;"></div>

    {{-- Cardholder Name --}}
    <div style="padding: 10px 0px;">
      <label style="font-weight: bold; margin-bottom: 2px;">
        @lang('laravel-stripe::payments.cardholder-label')
        <span style="color: red;">*</span>
      </label>
      <input
        type="text"
        name="cardholder_name"
        id="cardHolderName"
        placeholder="@lang('laravel-stripe::payments.cardholder-placeholder')"
        required
        style=" display: block; width: 100%; padding: 8.5px; border-radius: 3px; border: 1px solid #e4e4e4;"
        class="{{ $inputClass }}"
        autocomplete="cc-name"
      >
    </div>

    {{-- Stripe Payment Feilds --}}
    <div style="padding: 10px 0px;">
      <label style="font-weight: bold; margin-bottom: 2px;">
        @lang('laravel-stripe::payments.card-label')
        <span style="color: red;">*</span>
      </label>
      <div
        style="background: white; display: block; width: 100%; padding: 11.5px 8.5px; border-radius: 3px; border: 1px solid #e4e4e4;"
        class="{{ $inputClass }}"
        id="card-element">
      </div>
    </div>
  
    {{-- Submit BTN --}}
    <div style="padding-top: 10px;">
      <button
        id="cardSubmitBtn"
        style="display: block; width: 100%; background: {{ $themeColor }}; color: white; padding: 10px; border: 0; border-radius: 3px; font-weight: bold;"
        type="submit">@lang('laravel-stripe::payments.pay-btn') {{ $numberFormat->getSymbol(NumberFormatter::CURRENCY_SYMBOL) }}{{ $amount / 100 }}
      </button>
    </div>
  </form>
</div>
