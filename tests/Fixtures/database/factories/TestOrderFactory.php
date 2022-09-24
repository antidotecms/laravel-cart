<?php

namespace Antidote\LaravelCart\Tests\Fixtures\database\factories;

use Antidote\LaravelCart\Tests\Fixtures\cart\Models\Products\TestCustomer;
use Antidote\LaravelCart\Tests\Fixtures\cart\Models\TestOrder;
use Illuminate\Database\Eloquent\Factories\Factory;

class TestOrderFactory extends Factory
{
    protected $model = TestOrder::class;

    public function definition(): array
    {
        return [

        ];
    }

    public function forCustomer(TestCustomer $customer) {
        return [
            'test_customer_id' => $customer->id
        ];
    }
}
