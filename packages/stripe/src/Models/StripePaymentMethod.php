<?php

namespace Antidote\LaravelCartStripe\Models;

use Antidote\LaravelCart\Contracts\PaymentMethod;
use Antidote\LaravelCartStripe\Domain\PaymentIntent;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class StripePaymentMethod extends PaymentMethod
{
    protected $fillable = [
        'data'
    ];

    protected $casts = [
        'data' => 'array'
    ];

    public function order() : MorphOne
    {
        $order_class = config('laravel-cart.order_class');
        return $this->morphOne($order_class);
    }

    public function initialize(): void
    {
        PaymentIntent::create($this->order);
    }
}
