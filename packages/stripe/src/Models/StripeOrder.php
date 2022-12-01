<?php

namespace Antidote\LaravelCartStripe\Models;

use Antidote\LaravelCartStripe\Database\factories\StripeOrderFactory;

class StripeOrder extends \Antidote\LaravelCart\Contracts\Order
{
    protected static function newFactory()
    {
        return StripeOrderFactory::new();
    }
}
