<?php

namespace Antidote\LaravelCart\Tests\laravel\database\factories;

use Antidote\LaravelCart\Tests\laravel\app\Models\TestStripeOrder;

class TestStripeOrderFactory extends TestOrderFactory
{
    protected $model = TestStripeOrder::class;
}
