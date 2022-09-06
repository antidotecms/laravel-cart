<?php

use Tests\Fixtures\app\Models\Products\Product;
use Tests\Fixtures\app\Models\ProductTypes\ComplexProductDataType;
use Tests\Fixtures\app\Models\ProductTypes\SimpleProductDataType;
use Tests\Fixtures\app\Models\ProductTypes\VariableProductDataType;

test('a product type has a product', function()
{
    $product_data = \Tests\Fixtures\app\Models\ProductTypes\SimpleProductDataType::create([
        'name' => 'Simple Product Type',
        'description' => 'It\'s really very simple',
        'price' => 100
    ]);

    $product = Product::create();

    $product->productDataType()->associate($product_data);
    $product->save();

    expect(get_class($product_data->product))->toBe(Product::class);
    expect($product_data->product->id)->toBe($product->id);
});

it('it can create a product and associated product data', function()
{
    $product_data = \Tests\Fixtures\app\Models\ProductTypes\SimpleProductDataType::create([
        'name' => 'A Simple Product',
        'description' => 'It\'s really very simple',
        'price' => 100
    ]);

    $product = Product::create();

    $product->productDataType()->associate($product_data);
    // or $product_data->product()->save($product);

    $this->assertEquals(1, Product::count());

    $this->assertEquals('A Simple Product', $product->getName());
    $this->assertEquals('It\'s really very simple', $product->getDescription());
    $this->assertEquals(100, $product->getPrice());
});

it('can utilize product data to determine name, price and specification', function()
{
    $product_data = \Tests\Fixtures\app\Models\ProductTypes\VariableProductDataType::create([
        'name' => 'A Simple Product',
        'description' => 'It\'s really very simple',
        'price' => 100
    ]);

    $product = Product::create();

    $product->productDataType()->associate($product_data);

    $name = $product->getName([
       'width' => 10,
       'height' => 10
    ]);

    $expected_name = "A Simple Product with width of 10 and height of 10";

    $this->assertEquals($expected_name, $name);
    $this->assertEquals(100, $product->getPrice([
        'width' => 10,
        'height' => 10
    ]));

});

it('products_will_return_the_correct_name_and_price', function () {

    $simple_product = SimpleProductDataType::create([
        'name' => 'A Simple Product',
        'price' => '100',
        'description' => 'its really very simple'
    ]);

    $this->assertEquals('A Simple Product', $simple_product->getName());
    $this->assertEquals('its really very simple', $simple_product->getDescription());
    $this->assertEquals(100, $simple_product->getPrice());

    $complex_product = ComplexProductDataType::create([
        'name' => 'A Complex Product',
        'width' => 20,
        'height' => 10
    ]);

    $this->assertEquals('A Complex Product', $complex_product->getName());
    $this->assertEquals(200, $complex_product->getPrice());

    $specification = [
        'width' => 20,
        'height' => 10
    ];

    $variable_product = VariableProductDataType::create([
        'name' => 'A Variable Product'
    ]);

    $this->assertEquals('A Variable Product', $variable_product->getName());
    $this->assertEquals(120, $variable_product->getPrice());
    $this->assertEquals('A Variable Product with width of 20 and height of 10', $variable_product->getName($specification));
    $this->assertEquals('width: 20, height: 10', $variable_product->getDescription($specification));
    $this->assertEquals(200, $variable_product->getPrice($specification));

});

it('will soft delete, force delete and restore product data type when soft deleted, force deleted and restored', function() {

    $variable_product_data = VariableProductDataType::create([
        'name' => 'A variable product'
    ]);

    $variable_product = Product::create();

    $variable_product->productDataType()->associate($variable_product_data);
    $variable_product->save();

    expect(Product::count())->toBe(1);
    expect(VariableProductDataType::count())->toBe(1);

    $variable_product->delete();

    expect(Product::count())->toBe(0);
    expect(VariableProductDataType::count())->toBe(0);

    $variable_product->restore();

    expect(Product::count())->toBe(1);
    expect(VariableProductDataType::count())->toBe(1);

    $variable_product->forceDelete();

    expect(Product::count())->toBe(0);
    expect(VariableProductDataType::count())->toBe(0);

    $variable_product->restore();

    expect(Product::count())->toBe(0);
    expect(VariableProductDataType::count())->toBe(0);

});

it('will not allow soft deletion of a product type if the product is not soft deleted', function () {

    $variable_product_data = VariableProductDataType::create([
        'name' => 'A variable product'
    ]);

    $variable_product = Product::create();

    $variable_product->productDataType()->associate($variable_product_data);
    $variable_product->save();

    expect(Product::count())->toBe(1);
    expect(VariableProductDataType::count())->toBe(1);

    $variable_product_data->delete();

    expect(Product::count())->toBe(1);
    expect(VariableProductDataType::count())->toBe(1);

    //bypass model events on product
    Product::where('id', $variable_product->id)->update(['deleted_at' => now()]);

    $variable_product->refresh();
    expect($variable_product->trashed())->toBeTrue();

    expect(Product::count())->toBe(0);
    expect(VariableProductDataType::count())->toBe(1);

    $variable_product_data->refresh();
    $variable_product_data->delete();

    expect(Product::count())->toBe(0);
    expect(VariableProductDataType::count())->toBe(0);

});

it('will not allow restoration of a product data type if the product is deleted', function() {

    $variable_product_data = VariableProductDataType::create([
        'name' => 'A variable product'
    ]);

    $variable_product = Product::create();

    $variable_product->productDataType()->associate($variable_product_data);
    $variable_product->save();

    expect(Product::count())->toBe(1);
    expect(VariableProductDataType::count())->toBe(1);

    $variable_product_data->delete();

    expect(Product::count())->toBe(1);
    expect(VariableProductDataType::count())->toBe(1);
});
