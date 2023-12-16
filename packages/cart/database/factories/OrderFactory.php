<?php

namespace Antidote\LaravelCart\Database\Factories;

use Antidote\LaravelCart\Models\Customer;
use Antidote\LaravelCart\Models\Order;
use Antidote\LaravelCart\Models\OrderItem;
use Antidote\LaravelCart\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

class OrderFactory extends Factory
{
    protected $model = Order::class;
    /**
     * @inheritDoc
     */
    public function definition()
    {
        return [
            'customer_id' => Customer::factory()
        ];
    }

    public function withProduct(Product $product, $quantity = 1, $product_data = [])
    {
        return $this->afterCreating(function(Model $order) use ($product, $quantity, $product_data) {
            /** @var Order $order */
            OrderItem::factory()->create([
                'name' => $product->name,
                'product_id' => $product->id,
                'price' => $product->getPrice($product_data),
                'product_data' => $product_data,
                'quantity' => $quantity,
                'order_id' => $order->id
            ]);
        });
    }

    public function forCustomer(Customer $customer) {
        return $this->state([
            'customer_id' => $customer->id
        ]);
    }
}
