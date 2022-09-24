<?php

namespace Database\Factories\Tests\Fixtures\app\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Tests\Fixtures\app\Models\Products\TestCustomer;
use Tests\Fixtures\app\Models\TestOrder;

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
