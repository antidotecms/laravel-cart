<?php

namespace Antidote\LaravelCart\Tests\Fixtures\database\factories;

use Antidote\LaravelCart\Contracts\Order;
use Antidote\LaravelCart\Contracts\PaymentMethod;
use Antidote\LaravelCart\Contracts\Product;
use Antidote\LaravelCart\Tests\Fixtures\cart\Models\Products\TestCustomer;
use Antidote\LaravelCart\Tests\Fixtures\cart\Models\TestOrder;
use Illuminate\Database\Eloquent\Factories\Factory;

class TestOrderFactory extends Factory
{
    protected $model = TestOrder::class;

    public function definition(): array
    {
        return [
            'test_customer_id' => TestCustomer::factory(),
        ];
    }

    public function withPaymentMethod(?PaymentMethod $payment_method)
    {
        return $this->state([
            'payment_method_id' => $payment_method->id,
            'payment_method_class' => get_class($payment_method)
        ]);
    }

    public function withProduct(Product $product, $quantity)
    {
        return $this->afterCreating(function(Order $order) use ($product, $quantity) {
            $order->items()->create([
                'name' => $product->getName(),
                'test_product_id' => $product->id,
                'price' => $product->getPrice(),
                'product_data' => [],
                'quantity' => $quantity,
                'test_order_id' => $order->id
            ]);
        });
    }

    public function forCustomer(TestCustomer $customer) {
        return [
            'test_customer_id' => $customer->id
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function(Order $order) {

            $payment_method_class = getClassNameFor('payment_method');
            if(!$order->paymentMethod) {
                $payment_method = $payment_method_class::make([
                    'test_order_id' => $order->id
                ]);
                $order->paymentMethod()->associate($payment_method);
                //$payment_method->save();
            }

            if(!$order->customer) {
                $customer = TestCustomer::factory()->make();
                $order->customer()->save($customer);
            }

        });
    }
}
