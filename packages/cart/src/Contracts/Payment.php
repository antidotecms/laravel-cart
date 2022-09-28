<?php

namespace Antidote\LaravelCart\Contracts;

use Antidote\LaravelCart\Concerns\ConfiguresPaymentMethod;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

abstract class Payment extends Model
{
    use ConfiguresPaymentMethod;

    public function order() : MorphOne
    {
        return $this->morphOne(getClassNameFor('order'), 'payment_method');
    }

    public abstract function initialize() : void;
}
