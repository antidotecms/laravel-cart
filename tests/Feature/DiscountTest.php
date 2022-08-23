<?php

use Antidote\LaravelCart\Domain\Discount\PercentageDiscount;
use Antidote\LaravelCart\Facades\Cart;
use Antidote\LaravelCart\Models\CartAdjustment;
use Tests\Fixtures\app\Models\Products\Product;
use Tests\Fixtures\app\Models\ProductTypes\SimpleProductDataType;

it('will apply a discount', function() {
    $simple_product_data = SimpleProductDataType::create([
        'name' => 'A Simple Product',
        'price' => '2000'
    ]);

    $simple_product = Product::create();
    $simple_product->productDataType()->associate($simple_product_data);
    $simple_product->save();

    CartAdjustment::create([
        'name' => '10% off',
        'class' => PercentageDiscount::class,
        'parameters' => [
            'percentage' => 10
        ],
        'active' => true
    ]);

    Cart::add($simple_product);

    $this->assertEquals(1800, Cart::getTotal());
});
