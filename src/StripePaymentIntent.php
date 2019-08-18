<?php

namespace ChrisBraybrooke\LaravelStripePaymentIntent;

use ChrisBraybrooke\LaravelStripePaymentIntent\Models\PaymentRecord;
use Stripe\PaymentIntent;
use Stripe\Stripe;

class StripePaymentIntent
{
    protected $stripeKey;

    protected $stripeSecret;

    public $intent;

    public $currency;

    public $amount;

    public $inputClass = 'payment-input';

    public $savePaymentRecords = true;

    public $paymentRecord = null;

    public $themeColor = '#1976d2';

    public function __construct($stripeKey, $stripeSecret)
    {
        $this->stripeKey = $stripeKey;
        $this->stripeSecret = $stripeSecret;
        Stripe::setApiKey($this->stripeSecret);
    }

    public function setThemeColor($color)
    {
        $this->themeColor = $color;
    }

    public function getThemeColor()
    {
        return $this->themeColor;
    }

    /**
     * Set the savePaymentRecords variable as false.
     *
     * @return void
     */
    public function dontSavePaymentRecords()
    {
        $this->savePaymentRecords = false;
    }

    /**
     * Set the input class for inputs within the payment form.
     *
     * @param string $class
     * @return void
     */
    public function setInputClass($class)
    {
        $this->inputClass = $class;
    }

    /**
     * Get the input class for inputs within the payment form.
     *
     * @return string
     */
    public function getInputClass()
    {
        return $this->inputClass;
    }

    /**
     * Handle the creation of a new payment intent.
     *
     * @param  array<payment_method_id, payment_intent_id, amount, currency>  $data
     * @return Response
     */
    public function create($data)
    {
        // Save currency
        $this->currency = strtoupper($data['currency']);

        // Save amount
        $this->amount = $data['amount'];

        try {
            if ($data['payment_method'] ?? false) {
                $this->createPaymentIntent($data);
            }
            if ($data['payment_intent_id'] ?? false) {
                $this->retrievePaymentIntent($data['payment_intent_id']);
            }
            return $this->generatePaymentResponse();
        } catch (Exception $e) {
            return $this->responseAdapter(true, true, $e->getMessage());
        }
    }

    /**
     * Create a payment intent.
     *
     * @param  array $data
     * @return \ChrisBraybrooke\LaravelStripePaymentIntent\StripePaymentIntent
     */
    public function createPaymentIntent(array $data)
    {
        if (!isset($data['confirmation_method'])) {
            $data['confirmation_method'] = 'manual';
        }
        if (!isset($data['confirm'])) {
            $data['confirm'] = true;
        }
        $this->intent = PaymentIntent::create($data);

        return $this;
    }

    /**
     * Retrieve a payment intent.
     *
     * @param  string $id
     * @return \ChrisBraybrooke\LaravelStripePaymentIntent\StripePaymentIntent
     */
    public function retrievePaymentIntent($id)
    {
        $this->intent = PaymentIntent::retrieve($id);
        $this->intent->confirm();

        return $this;
    }

    /**
     * Generate the response back to the client.
     *
     * @return [type] [description]
     */
    public function generatePaymentResponse()
    {
        if ($this->intent) {
            if (($this->intent->status == 'requires_action' || $this->intent->status == 'requires_source_action') && $this->intent->next_action->type == 'use_stripe_sdk') {
                return $this->responseAdapter(true, true);
            } else if ($this->intent->status == 'succeeded') {
                if ($this->savePaymentRecords) {
                    $this->createPaymentRecord();
                }
                return $this->responseAdapter();
            }
        }
        return $this->responseAdapter(false, true, 'Invalid PaymentIntent status');
    }

    /**
     * Create the payment record in the database.
     *
     * @return \ChrisBraybrooke\LaravelStripePaymentIntent\Models\PaymentRecord
     */
    private function createPaymentRecord()
    {
        $firstCharge = $this->intent->charges->data[0] ?? [];
        $card = $firstCharge['payment_method_details']['card'] ?? [];

        $this->paymentRecord = PaymentRecord::create([
            'notes' => 'Payment via payment form.',
            'processor_reference' => $firstCharge['id'] ?? '',
            'processor_name' => 'PROCESSOR_STRIPE',
            'method' => 'METHOD_CARD',
            'currency' => $this->currency,
            'amount' => $this->amount,
            'fee' => 0,
            'source_id' => $this->intent->payment_method,
            'source_brand' => $card['brand'] ?? null,
            'source_country' => $card['country'] ?? null,
            'source_last4' => $card['last4'] ?? null,
            'source_exp_month' => $card['exp_month'] ?? null,
            'source_exp_year' => $card['exp_year'] ?? null
        ]);
    }

    /**
     * Adapt the data ready for a response.
     *
     * @param  boolean $success
     * @param  boolean $action
     * @param  string  $error
     * @return Response
     */
    public function responseAdapter(bool $success = true, bool $action = false, string $error = null)
    {
        return response()->json([
            "success" => $success,
            "requires_action" => $action,
            "payment_record" => $this->paymentRecord,
            "intent" => $this->intent,
            "error" => $error,
            "payment_intent_client_secret" => $this->intent->client_secret
        ]);
    }

}