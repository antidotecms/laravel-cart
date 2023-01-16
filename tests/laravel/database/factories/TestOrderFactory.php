<?php

namespace Antidote\LaravelCart\Tests\laravel\database\factories;

use Antidote\LaravelCart\Contracts\Order;
use Antidote\LaravelCart\Database\Factories\OrderFactory;
use Antidote\LaravelCart\Tests\laravel\app\Models\Products\TestCustomer;
use Antidote\LaravelCart\Tests\laravel\app\Models\TestOrder;

class TestOrderFactory extends OrderFactory
{
    protected $model = TestOrder::class;

    public function definition(): array
    {
        return [
            'test_customer_id' => TestCustomer::factory(),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function(Order $order) {

            $payment_method_class = getClassNameFor('payment');
            if(!$order->paymentMethod) {
                $payment_method = $payment_method_class::make([
                    getKeyFor('order') => $order->id
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
