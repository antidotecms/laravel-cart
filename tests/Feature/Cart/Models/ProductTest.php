<?php

use Antidote\LaravelCart\Tests\laravel\app\Models\Products\TestProduct;
use Antidote\LaravelCart\Tests\laravel\app\Models\ProductTypes\SimpleProductDataType;
use Antidote\LaravelCart\Tests\laravel\app\Models\ProductTypes\VariableProductDataType;

test('a product type has a product', function()
{
    $product_data = SimpleProductDataType::create([
        'price' => 100
    ]);

    $product = TestProduct::create([
        'name' => 'Simple Product Type'
    ]);

    $product->productType()->associate($product_data);
    $product->save();

    expect(get_class($product_data->product))->toBe(TestProduct::class);
    expect($product_data->product->id)->toBe($product->id);
});

it('it can create a product and associated product data', function()
{
    $product_data = SimpleProductDataType::create([
        'price' => 100
    ]);

    $product = TestProduct::create([
        'name' => 'A Simple Product',
        'description' => 'It\'s really very simple',
    ]);

    $product->productType()->associate($product_data);
    // or $product_data->product()->save($product);

    $this->assertEquals(1, TestProduct::count());

    $this->assertEquals('A Simple Product', $product->getName());
    $this->assertEquals('It\'s really very simple', $product->getDescription());
    $this->assertEquals(100, $product->getPrice());
});

it('can utilize product data to determine name, price and specification', function()
{
    //$this->markTestIncomplete('sort out how products are named by default');

    $product_data = VariableProductDataType::create();

    $product = TestProduct::create([
        'description' => 'It\'s really very simple'
    ]);

    $product->productType()->associate($product_data);

    $name = $product->getName([
       'width' => 10,
       'height' => 10
    ]);

    $expected_name = "A Variable Product with width of 10 and height of 10";

    $this->assertEquals($expected_name, $name);
    $this->assertEquals(100, $product->getPrice([
        'width' => 10,
        'height' => 10
    ]));

});

it('products_will_return_the_correct_name_and_price', function () {

    $simple_product = TestProduct::factory()->asSimpleProduct([
        'price' => 100
    ])->create([
        'description' => 'its really very simple'
    ]);

    $this->assertEquals('A Simple Product', $simple_product->getName());
    $this->assertEquals('its really very simple', $simple_product->getDescription());
    $this->assertEquals(100, $simple_product->getPrice());

    $complex_product = TestProduct::factory()->asComplexProduct([
        'width' => 8,
        'height' => 8
    ])
    ->create([
        'description' => 'A Complex Product'
    ]);

    $this->assertEquals('8 x 8 object', $complex_product->getName());
    $this->assertEquals('A Complex Product', $complex_product->getDescription());
    $this->assertEquals(64, $complex_product->getPrice());

    $product_data = [
        'width' => 20,
        'height' => 10
    ];

    $variable_product = TestProduct::factory()->asVariableProduct()
    ->create([
        'description' => 'A Variable Product'
    ]);

    $this->assertEquals('A Variable Product with width of 20 and height of 10', $variable_product->getName($product_data));
    $this->assertEquals('A Variable Product', $variable_product->getDescription($product_data));
    $this->assertEquals(200, $variable_product->getPrice($product_data));
    //$this->assertEquals('A Variable Product with width of 20 and height of 10', $variable_product->getName($product_data));
    //$this->assertEquals(200, $variable_product->getPrice($specification));

});

it('will soft delete, force delete and restore product data type when soft deleted, force deleted and restored', function() {

    $variable_product_data = VariableProductDataType::create();

    $variable_product = TestProduct::create([
        'name' => 'A variable product'
    ]);

    $variable_product->productType()->associate($variable_product_data);
    $variable_product->save();

    expect(TestProduct::count())->toBe(1)
        ->and(VariableProductDataType::count())->toBe(1);

    $variable_product->delete();

    expect(TestProduct::count())->toBe(0)
        ->and(VariableProductDataType::count())->toBe(0)
        ->and(TestProduct::withTrashed()->count())->toBe(1)
        ->and(VariableProductDataType::withTrashed()->count())->toBe(1);

    $variable_product->restore();

    expect(TestProduct::count())->toBe(1)
        ->and(VariableProductDataType::count())->toBe(1);

    $variable_product->forceDelete();

    expect(TestProduct::count())->toBe(0)
        ->and(VariableProductDataType::count())->toBe(0)
        ->and(TestProduct::withTrashed()->count())->toBe(0)
        ->and(VariableProductDataType::withTrashed()->count())->toBe(0);

    $variable_product->restore();

    expect(TestProduct::count())->toBe(0)
        ->and(VariableProductDataType::count())->toBe(0);

});

it('will not allow soft deletion of a product type if the product is not soft deleted', function () {

    $variable_product_data = VariableProductDataType::create();

    $variable_product = TestProduct::create([
        'name' => 'A variable product'
    ]);

    $variable_product->productType()->associate($variable_product_data);
    $variable_product->save();

    expect(TestProduct::count())->toBe(1)
        ->and(VariableProductDataType::count())->toBe(1);

    $variable_product_data->delete();

    expect(TestProduct::count())->toBe(1)
        ->and(VariableProductDataType::count())->toBe(1);

    //bypass model events on product
    TestProduct::where('id', $variable_product->id)->update(['deleted_at' => now()]);

    $variable_product->refresh();

    expect($variable_product->trashed())->toBeTrue()
        ->and(TestProduct::count())->toBe(0)
        ->and(VariableProductDataType::count())->toBe(1);

    $variable_product_data->refresh();
    $variable_product_data->delete();

    expect(TestProduct::count())->toBe(0)
        ->and(VariableProductDataType::count())->toBe(0);

});

it('will not allow restoration of a product data type if the product is soft deleted', function() {

    $variable_product_data = VariableProductDataType::create();

    $variable_product = TestProduct::create([
        'name' => 'A variable product'
    ]);

    $variable_product->productType()->associate($variable_product_data);
    $variable_product->save();

    expect(TestProduct::count())->toBe(1)
        ->and(VariableProductDataType::count())->toBe(1);

    $variable_product_data->delete();

    expect(TestProduct::count())->toBe(1)
        ->and(VariableProductDataType::count())->toBe(1);
});

it('will defer an attribute to the product data type', function() {

    $product = TestProduct::factory()->asSimpleProduct()->create([
        'name' => 'This Product',
        'description' => 'This Description'
    ]);

    expect($product->getName())->toBe('A Simple Product')
        ->and($product->getDescription())->toBe('This Description');
});

it('will throw an exception if a defered attribute is not defined', function () {

    $product = TestProduct::factory()->asSimpleProduct()->create([
        'name' => 'This Product',
        'description' => 'This Description'
    ]);

    $product->getFoo();
})
->throws(Exception::class, "Define 'getFoo' on Antidote\LaravelCart\Tests\laravel\app\Models\ProductTypes\SimpleProductDataType");
