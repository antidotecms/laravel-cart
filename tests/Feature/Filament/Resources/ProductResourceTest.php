<?php

use Antidote\LaravelCart\Models\Products\SimpleProductType;
use Antidote\LaravelCartFilament\CartPanelPlugin;
use Antidote\LaravelCartFilament\Resources\ProductResource;
use Antidote\LaravelCartFilament\Resources\ProductResource\Pages\CreateProduct;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use function Pest\Livewire\livewire;

it('has the correct fields', function() {

    livewire(CreateProduct::class)
        ->assertFormFieldExists('product.id', function(TextInput $field) {
            return $field->isDisabled() &&
                !$field->isDehydrated();
        })
        ->assertFormFieldExists('product.name', function(TextInput $field) {
            return $field->isRequired();
        })
        ->assertFormFieldExists('product.product_type_type', function(Select $field) {
            return $field->isRequired() &&
                $field->getOptions() == CartPanelPlugin::get('productTypes');
        });
})
->coversClass(ProductResource::class);

it('will change the product type form when the product type changes', function () {

    livewire(CreateProduct::class)
        ->set('data.product.product_type_type', SimpleProductType::class)
        ->assertFormFieldExists('productType.price');

});

it('will create a product and product type', function () {

    livewire(CreateProduct::class)
        ->fillForm([
            'product.name' => 'test product',
            'product.product_type_type' => SimpleProductType::class,
            'productType.price' => 100
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(\Antidote\LaravelCart\Models\Product::count())->toBe(1);
    expect(SimpleProductType::count())->toBe(1);

    $product = \Antidote\LaravelCart\Models\Product::first();
    $simple_product_type = SimpleProductType::first();

    expect($product->productType->id)->toBe($simple_product_type->id);
});

it('will update a product and product type', function () {

    $product = \Antidote\LaravelCart\Models\Product::create([
        'name' => ' A Simple Product'
    ]);

    $product_type = SimpleProductType::create([
        'price' => 1000
    ]);

    $product->productType()->associate($product_type);
    $product->save();

    livewire(ProductResource\Pages\EditProduct::class, [
            'record' => $product->id
        ])
        ->assertFormFieldExists('product.id', fn(TextInput $field) => $field->getState() == $product->id)
        ->assertFormFieldExists('product.name', fn(TextInput $field) => $field->getState() == $product->name)
        ->assertFormFieldExists('product.product_type_type', fn() => get_class($product->productType) == SimpleProductType::class)
        ->assertFormFieldExists('productType.price', fn($field) => $field->getState() == $product->productType->price)
        ->fillForm([
            'product.name' => 'A Different Simple Product',
            'product.product_type_type' => SimpleProductType::class,
            'productType.price' => 999
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $product->refresh();

    expect($product->name)->toBe('A Different Simple Product');

});
