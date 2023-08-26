<?php

namespace Antidote\LaravelCart\Tests\Fixtures\factories;

use Antidote\LaravelCart\Database\Factories\OrderFactory;
use Antidote\LaravelCart\Models\Customer;
use Antidote\LaravelCart\Models\Order;

class TestOrderFactory extends OrderFactory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory()
        ];
    }
}
