<?php

use Antidote\LaravelCart\Contracts\ProductType;
use Antidote\LaravelCart\Models\Product;
use Antidote\LaravelCart\Models\Products\SimpleProductType;
use Antidote\LaravelCart\Tests\Fixtures\App\Models\Products\TestProduct;

it('has a product', function() {

    $product = TestProduct::factory()->asSimpleProduct()->create([
        'name' => 'Simple Product'
    ]);

    expect($product->productType)->toBeInstanceOf(SimpleProductType::class);
})
->covers(ProductType::class);

it('will be trashed if the product is trashed', function () {

    $product = TestProduct::factory()->asSimpleProduct()->create([
        'name' => 'Simple Product'
    ]);

    expect(SimpleProductType::count())->toBe(1);

    $product->delete();

    expect(SimpleProductType::count())->toBe(0);
})
->covers(ProductType::class);

it('will not be trashed if there is a product attached', function () {

    $product = TestProduct::factory()->asSimpleProduct()->create([
        'name' => 'Simple Product'
    ]);

    expect(Product::count())->toBe(1);
    expect(SimpleProductType::count())->toBe(1);

    $product->productType->delete();

    expect(SimpleProductType::count())->toBe(1);
})
->covers(ProductType::class);
