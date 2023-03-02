<?php

namespace Antidote\LaravelCart\Database\Factories;

use Antidote\LaravelCart\Models\Order;
use Antidote\LaravelCart\Models\OrderItem;
use Antidote\LaravelCart\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;

    public function definition(): array
    {
        return [
            'quantity' => 1
        ];
    }

    public function withProduct(Product $product, ?array $product_data = []) {
        return $this->state([
            'name' => $product->getName($product_data),
            'price' => $product->getPrice($product_data),
            'product_id' => $product->id
        ]);
    }

    public function withProductData(array $product_data) {
        return $this->state([
            'product_data' => $product_data
        ]);
    }

    public function forOrder(Order $order) {
        return $this->state([
            'order_id' => $order->id
        ]);
    }

    public function withQuantity(int $quantity) {
        return $this->state([
            'quantity' => $quantity
        ]);
    }
}
