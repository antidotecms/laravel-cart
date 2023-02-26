<?php

namespace Antidote\LaravelCart\Tests\Fixtures\factories;

use Antidote\LaravelCart\Tests\Fixtures\App\Models\Products\TestProduct;
use Antidote\LaravelCart\Tests\Fixtures\App\Models\TestOrder;
use Antidote\LaravelCart\Tests\Fixtures\App\Models\TestOrderItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class TestOrderItemFactory extends Factory
{
    protected $model = TestOrderItem::class;

    public function definition(): array
    {
        return [
            'quantity' => 1
        ];
    }

    public function withProduct(TestProduct $product, ?array $product_data = []) {
        return $this->state([
            'name' => $product->getName($product_data),
            'price' => $product->getPrice($product_data),
            'test_product_id' => $product->id
        ]);
    }

    public function withProductData(array $product_data) {
        return $this->state([
            'product_data' => $product_data
        ]);
    }

    public function forOrder(TestOrder $order) {
        return $this->state([
            'test_order_id' => $order->id
        ]);
    }

    public function withQuantity(int $quantity) {
        return $this->state([
            'quantity' => $quantity
        ]);
    }
}
