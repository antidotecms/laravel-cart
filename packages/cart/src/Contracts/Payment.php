<?php

namespace Antidote\LaravelCart\Contracts;

use Antidote\LaravelCart\Concerns\ConfiguresPayment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

//@todo remove and create OrderData - stripe payment_intent_id should be stored here
abstract class Payment extends Model
{
    use ConfiguresPayment;

    public function order() : MorphOne
    {
        return $this->morphOne(getClassNameFor('order'), 'payment');
    }

    public abstract function initialize() : void;
}
