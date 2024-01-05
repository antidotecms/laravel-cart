<?php

namespace Antidote\LaravelCartStripe\Database\factories;

use Antidote\LaravelCart\Database\Factories\OrderFactory;
use Antidote\LaravelCartStripe\Models\StripeOrder;

/** ~@deprecated  */
class StripeOrderFactory extends OrderFactory
{
    protected $model = StripeOrder::class;

    public function definition(): array
    {
        return [

        ];
    }
}
