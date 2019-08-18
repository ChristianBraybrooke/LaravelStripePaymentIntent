<?php

namespace ChrisBraybrooke\LaravelStripePaymentIntent\Http\Controllers;

use Illuminate\Http\Request;
use ChrisBraybrooke\LaravelStripePaymentIntent\Facades\StripePaymentIntent;

class StripePaymentController extends Controller
{
    public function __invoke(Request $request)
    {
        $this->validate($request, [
            'amount' => 'required|numeric'
        ]);

        return StripePaymentIntent::create([
            'payment_intent_id' => $request->input('payment_intent_id'),
            'payment_method' => $request->input('payment_method_id'),
            'amount' => $request->input('amount'),
            'currency' => strtolower($request->input('currency'))
        ]);
    }
}