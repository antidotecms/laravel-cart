<?php

namespace Antidote\LaravelCart\Contracts;

use Antidote\LaravelCart\Concerns\ConfiguresPaymentMethod;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

abstract class PaymentMethod extends Model
{
    use ConfiguresPaymentMethod;

    public function order() : MorphOne
    {
        $order_class = config('laravel-cart.order_class');
        return $this->morphOne($order_class);
    }

    public abstract function initialize() : void;
}
