<?php

namespace Antidote\LaravelCartStripe\Models;

use Antidote\LaravelCart\Contracts\PaymentMethod;
use Antidote\LaravelCartStripe\Domain\PaymentIntent;

class StripePaymentMethod extends PaymentMethod
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
