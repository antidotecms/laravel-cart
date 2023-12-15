<?php

use Antidote\LaravelCart\Models\Product;
use Antidote\LaravelCart\Tests\Fixtures\App\Models\Products\TestProduct;
use Antidote\LaravelCart\Tests\Fixtures\App\Models\ProductTypes\SimpleProductDataType;
use Antidote\LaravelCart\Tests\Fixtures\App\Models\ProductTypes\VariableProductDataType;
use Antidote\LaravelCartFilament\CartPanelPlugin;

test('a product type has a product', function()
{
    CartPanelPlugin::set('models.product', TestProduct::class);

    $product_data = SimpleProductDataType::create([
        'price' => 100
    ]);

    $product = TestProduct::create([
        'name' => 'Simple Product Type',
        'product_type_type' => SimpleProductDataType::class,
        'product_type_id' => $product_data->id
    ]);

    expect(get_class($product_data->product))->toBe(TestProduct::class);
    expect($product_data->product->id)->toBe($product->id);
})
->coversClass(Product::class);

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
})
->coversClass(Product::class);

it('can utilize product data to determine name, price and specification', function()
{
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

})
->coversClass(Product::class);

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
})
->coversClass(Product::class);

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

})
->coversClass(Product::class);

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

})
->coversClass(Product::class);

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
})
->coversClass(Product::class);

it('will defer an attribute to the product data type', function() {

    $product = TestProduct::factory()->asSimpleProduct()->create([
        'name' => 'This Product',
        'description' => 'This Description'
    ]);

    expect($product->getName())->toBe('A Simple Product')
        ->and($product->getDescription())->toBe('This Description');
})
->coversClass(Product::class);

it('will throw an exception if a deferred attribute is not defined', function () {

    $product = TestProduct::factory()->asSimpleProduct()->create([
        'name' => 'This Product',
        'description' => 'This Description'
    ]);

    $product->getFoo();
})
->coversClass(Product::class)
->throws(Exception::class, "Define 'getFoo' on ".SimpleProductDataType::class);

it('will check whether a product is valid from its product type', function () {

    $product = TestProduct::factory()->create([
        'name' => 'This Product',
        'description' => 'This Description'
    ]);

    $test_product_type = new class extends \Antidote\LaravelCart\Contracts\ProductType {
        public function isValid(?array $product_data = null): bool {
            return true;
        }
    };

    $spied_product_type = Mockery::spy($test_product_type::class);

    $product->productType()->associate($spied_product_type);
    $product->save();

    expect($product->productType)->not()->toBeNull();

    $product->checkValidity();

    $spied_product_type->shouldHaveReceived('isValid')->withAnyArgs()->once();
})
->coversClass(Product::class);
