<?php

use Antidote\LaravelCart\Domain\Discount\PercentageDiscount;
use Antidote\LaravelCart\Facades\Cart;
use Antidote\LaravelCart\Models\CartAdjustment;
use Tests\Fixtures\app\Models\Products\TestProduct;
use Tests\Fixtures\app\Models\ProductTypes\SimpleProductDataType;

it('will apply a discount', function() {

    $simple_product_data = SimpleProductDataType::create([
        'price' => '2000'
    ]);

    $simple_product = TestProduct::create([
        'name' => 'A Simple Product'
    ]);
    $simple_product->productType()->associate($simple_product_data);
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

    $this->assertEquals(2000, Cart::getSubtotal());
    $this->assertEquals(1800, Cart::getTotal());
});

it('will not apply a discount if it is not active', function () {

    $simple_product_data = SimpleProductDataType::create([
        'price' => '2000'
    ]);

    $simple_product = TestProduct::create([
        'name' => 'A Simple Product'
    ]);
    $simple_product->productType()->associate($simple_product_data);
    $simple_product->save();

    CartAdjustment::create([
        'name' => '10% off',
        'class' => PercentageDiscount::class,
        'parameters' => [
            'percentage' => 10
        ],
        'active' => false
    ]);

    Cart::add($simple_product);

    $this->assertEquals(2000, Cart::getSubtotal());
    $this->assertEquals(2000, Cart::getTotal());
});
