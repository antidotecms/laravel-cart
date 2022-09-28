<?php

namespace Antidote\LaravelCartStripe\Models;

use Antidote\LaravelCart\Contracts\Payment;
use Antidote\LaravelCartStripe\Domain\PaymentIntent;

class StripePayment extends Payment
{
    protected $fillable = [
        'body'
    ];

    protected $casts = [
        'body' => 'array'
    ];

//    public function order() : MorphOne
//    {
//        return $this->morphOne(getClassNameFor('order'), getKeyFor('order'));
//    }

    public function initialize(): void
    {
        PaymentIntent::create($this->order);
    }
}
