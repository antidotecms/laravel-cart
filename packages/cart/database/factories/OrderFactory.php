<?php

namespace Antidote\LaravelCart\Database\Factories;

use Antidote\LaravelCart\Contracts\Customer;
use Antidote\LaravelCart\Contracts\Order;
use Antidote\LaravelCart\Contracts\Payment;
use Antidote\LaravelCart\Contracts\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;
    /**
     * @inheritDoc
     */
    public function definition()
    {
        return [];
    }

    public function withProduct(Product $product, $quantity = 1, $product_data = [])
    {
        return $this->afterCreating(function(Order $order) use ($product, $quantity, $product_data) {
            $order->items()->create([
                'name' => $product->getName($product_data),
                getKeyFor('product') => $product->id,
                'price' => $product->getPrice($product_data),
                'product_data' => $product_data,
                'quantity' => $quantity,
                getKeyFor('order') => $order->id
            ]);
        });
    }

    public function withPaymentMethod(?Payment $payment_method)
    {
        return $this->state([
            'payment_method_id' => $payment_method->id,
            'payment_method_class' => get_class($payment_method)
        ]);
    }

    public function forCustomer(Customer $customer) {
        return $this->state([
            getKeyFor('customer') => $customer->id
        ]);
    }
}
