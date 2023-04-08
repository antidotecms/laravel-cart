<?php

it('has a product', function() {

    $product = \Antidote\LaravelCart\Tests\Fixtures\App\Models\Products\TestProduct::factory()->asSimpleProduct()->create([
        'name' => 'Simple Product'
    ]);

    expect($product->productType)->toBeInstanceOf(\Antidote\LaravelCart\Tests\Fixtures\App\Models\ProductTypes\SimpleProductDataType::class);
})
->covers(\Antidote\LaravelCart\Contracts\ProductType::class);

it('will be trashed if the product is trashed', function () {

    $product = \Antidote\LaravelCart\Tests\Fixtures\App\Models\Products\TestProduct::factory()->asSimpleProduct()->create([
        'name' => 'Simple Product'
    ]);

    expect(\Antidote\LaravelCart\Tests\Fixtures\App\Models\ProductTypes\SimpleProductDataType::count())->toBe(1);

    $product->delete();

    expect(\Antidote\LaravelCart\Tests\Fixtures\App\Models\ProductTypes\SimpleProductDataType::count())->toBe(0);
})
->covers(\Antidote\LaravelCart\Contracts\ProductType::class);
