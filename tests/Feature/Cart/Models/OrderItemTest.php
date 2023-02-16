<?php

use Antidote\LaravelCart\Tests\laravel\app\Models\Products\TestCustomer;
use Antidote\LaravelCart\Tests\laravel\app\Models\Products\TestProduct;
use Antidote\LaravelCart\Tests\laravel\app\Models\TestOrder;
use Antidote\LaravelCart\Tests\laravel\app\Models\TestOrderItem;

it('automatically populates the fillable fields', function () {

    $test_order_item = new TestOrderItem;
    expect($test_order_item->getFillable())->toBe([
        'name',
        'test_product_id',
        'product_data',
        'price',
        'quantity',
        'test_order_id'
    ]);

    class NewProduct extends \Antidote\LaravelCart\Contracts\Product {};
    Config::set('laravel-cart.classes.product', NewProduct::class);
    $new_order_item = new class extends \Antidote\LaravelCart\Contracts\OrderItem {};
    expect($new_order_item->getFillable())->toBe([
        'name',
        'new_product_id',
        'product_data',
        'price',
        'quantity',
        'test_order_id'
    ]);

});

it('populates the casts', function () {

    $test_order_item = new TestOrderItem;

    expect($test_order_item->getCasts())->toHaveKey('product_data');
    expect($test_order_item->getCasts()['product_data'])->toBe('array');
});

it('has a product', function () {

    $simple_product = TestProduct::factory()->asSimpleProduct([
        'price' => 1999
    ])->create();

    $customer = TestCustomer::factory()->create();

    $test_order = TestOrder::factory()->create([
        'test_customer_id' => $customer->id
    ]);

    $test_order_item = TestOrderItem::factory()
        ->withProduct($simple_product)
        ->withQuantity(2)
        ->forOrder($test_order)
        ->create();

    expect($test_order_item->product->id)->toBe($simple_product->id);
    expect($test_order_item->getPrice())->toBe($simple_product->getPrice());
    expect($test_order_item->getName())->toBe($simple_product->getName());
    expect($test_order_item->getCost())->toBe($simple_product->getPrice() * 2);

});

it('will not change values of product after being created as an order', function () {

    $simple_product = TestProduct::factory()->asSimpleProduct([
        'price' => 1999
    ])->create();

    $customer = TestCustomer::factory()->create();

    $test_order = TestOrder::factory()->create([
        'test_customer_id' => $customer->id
    ]);

    $test_order_item = TestOrderItem::factory()
        ->withProduct($simple_product)
        ->withQuantity(2)
        ->forOrder($test_order)
        ->create();

    $simple_product->name = 'A different name';
    $simple_product->productType->price = 1000;
    $simple_product->save();

    expect($test_order_item->product->id)->toBe($simple_product->id);
    expect($test_order_item->getPrice())->toBe(1999);
    expect($test_order_item->getName())->toBe('A Simple Product');
    expect($test_order_item->getCost())->toBe(3998);

});
