<?php

namespace Antidote\LaravelCart\Tests\Fixtures\factories;

use Antidote\LaravelCart\Tests\Fixtures\App\Models\TestStripeOrder;

/** @deprecated */
class TestStripeOrderFactory extends TestOrderFactory
{
    protected $model = TestStripeOrder::class;
}
