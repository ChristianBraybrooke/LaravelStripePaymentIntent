<?php

namespace ChrisBraybrooke\LaravelStripePaymentIntent\Models;

use App\Traits\HasUUID;
use Illuminate\Database\Eloquent\Model;

class PaymentRecord extends Model
{
    use HasUUID;

    /**
     * Whether the ID collumn in auto incrementing.
     *
     * @return boolean
     */
    public $incrementing = false;
    
    /**
     * What the ID collumn key type is.
     *
     * @return string
     */
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'processor_name', 'processor_reference', 'refunded', 'notes',
        'method', 'currency', 'amount', 'fee', 'source_id', 'source_brand',
        'source_country', 'source_last4', 'source_exp_month', 'source_exp_year'
    ];

     /**
      * The attributes that should be cast to native types.
      *
      * @var array
      */
    protected $casts = [
        'refunded' => 'boolean'
    ];

    /**
     * The attribute that should be appended to the model.
     *
     * @var array
     */
    protected $appends = [
      //
    ];
}
