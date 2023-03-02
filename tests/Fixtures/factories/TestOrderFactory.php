<?php

namespace Antidote\LaravelCart\Tests\Fixtures\factories;

use Antidote\LaravelCart\Database\Factories\OrderFactory;
use Antidote\LaravelCart\Models\Order;
use Antidote\LaravelCart\Tests\Fixtures\App\Models\TestCustomer;
use Antidote\LaravelCart\Tests\Fixtures\App\Models\TestOrder;

class TestOrderFactory extends OrderFactory
{
    protected $model = TestOrder::class;

    public function definition(): array
    {
        return [
            'customer_id' => TestCustomer::factory(),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function(Order $order) {

            $payment_method_class = getClassNameFor('payment');
            if(!$order->paymentMethod) {
                $payment_method = $payment_method_class::make([
                    'order_id' => $order->id
                ]);
                $order->payment()->associate($payment_method);
                //$payment_method->save();
            }

            if(!$order->customer) {
                $customer = TestCustomer::factory()->make();
                $order->customer()->save($customer);
            }

        });
    }
}
