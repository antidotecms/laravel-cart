<?php

namespace Antidote\LaravelCart\Tests\Fixtures\database\factories;

use Antidote\LaravelCart\Tests\Fixtures\cart\Models\Products\TestProduct;
use Antidote\LaravelCart\Tests\Fixtures\cart\Models\TestOrder;
use Antidote\LaravelCart\Tests\Fixtures\cart\Models\TestOrderItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class TestOrderItemFactory extends Factory
{
    protected $model = TestOrderItem::class;

    public function definition(): array
    {
        return [

        ];
    }

    public function withProduct(TestProduct $product) {
        return $this->state([
            'name' => $product->getName(),
            'price' => $product->getPrice(),
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
