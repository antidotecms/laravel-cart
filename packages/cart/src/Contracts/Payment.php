<?php

namespace Antidote\LaravelCart\Contracts;

use Antidote\LaravelCart\Concerns\ConfiguresPayment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

abstract class Payment extends Model
{
    use ConfiguresPayment;

    public function order() : MorphOne
    {
        return $this->morphOne(getClassNameFor('order'), 'payment');
    }

    public abstract function initialize() : void;
}
