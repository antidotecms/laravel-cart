<?php

use Antidote\LaravelCart\DataTransferObjects\CartItem;
use Antidote\LaravelCart\Facades\Cart;
use Tests\Fixtures\app\Models\Products\Product;
use Tests\Fixtures\app\Models\ProductTypes\ComplexProductDataType;
use Tests\Fixtures\app\Models\ProductTypes\SimpleProductDataType;
use Tests\Fixtures\app\Models\ProductTypes\VariableProductDataType;

it('can add a product to the cart', function() {

    $product_data = SimpleProductDataType::create([
        'name' => 'A Simple Product',
        'description' => 'It\'s really very simple',
        'price' => '2000'
    ]);

    $product = Product::create();

    $product->productDataType()->associate($product_data);
    $product->save();

    Cart::add($product);

    $this->assertEquals(1, Cart::items()->count());

    $product_data = new CartItem([
        'product_id' => $product->id,
        'product_type' => SimpleProductDataType::class,
        'product' => SimpleProductDataType::find($product->id),
        'quantity' => 1,
        'specification' => null
    ]);

    $this->assertEquals($product_data, Cart::items()->first());

});

it('can add a product and specify quantity', function () {

    $product_data = SimpleProductDataType::create([
        'name' => 'A Simple Product',
        'price' => '2000'
    ]);

    $product = Product::create();

    $product->productDataType()->associate($product_data);
    $product->save();

    Cart::add($product, 3);

    $this->assertEquals(1, Cart::items()->count());
    $this->assertEquals(3, Cart::items()->first()->quantity);

});

it('can remove a product by product id', function () {

    $product_data = SimpleProductDataType::create([
        'name' => 'A Simple Product',
        'price' => '2000'
    ]);

    $product = Product::create();

    $product->productDataType()->associate($product_data);
    $product->save();

    Cart::add($product);

    $this->assertEquals(1, Cart::items()->count());

    Cart::remove($product);
    //$customer->refresh();

    $this->assertEquals(0, Cart::items()->count());

});

it('it can remove a product by product id and product data', function () {

    $variable_product_data_type1 = VariableProductDataType::create([
        'name' => 'A variable product'
    ]);

    $variable_product1 = Product::create();

    $variable_product1->productDataType()->associate($variable_product_data_type1);
    $variable_product1->save();

    $variable_product_data_type2 = VariableProductDataType::create([
        'name' => 'Another variable product'
    ]);

    $variable_product2 = Product::create();

    $variable_product2->productDataType()->associate($variable_product_data_type2);
    $variable_product2->save();

    Cart::add($variable_product1, 1, [
        'width' => 10,
        'height' => 10
    ]);

    Cart::add($variable_product2, 1, [
        'width' => 10,
        'height' => 20
    ]);

    $this->assertEquals(2, Cart::items()->count());

    Cart::remove($variable_product1);

    $this->assertEquals(1, Cart::items()->count());

    $expected_product = new CartItem([
        'product_id' => $variable_product2->id,
        'quantity' => 1,
        'product_data' => [
            'width' => 10,
            'height' => 20
        ]
    ]);

    $this->assertEquals($expected_product, Cart::items()->first());
});

it('it can remove a product by product id and product data specifying quantity', function () {

    $product_data = SimpleProductDataType::create([
        'name' => 'A Simple Product',
        'price' => '2000'
    ]);

    $product = Product::create();

    $product->productDataType()->associate($product_data);
    $product->save();

    Cart::add($product, 5);

    $this->assertEquals(1, Cart::items()->count());
    $this->assertEquals(5, Cart::items()->first()->quantity);

    Cart::remove($product, 2);
    //$customer->refresh();

    $this->assertEquals(1, Cart::items()->count());
    $this->assertEquals(3, Cart::items()->first()->quantity);

});

it('can clear the contents of the cart', function () {

    $product_data = SimpleProductDataType::create([
        'name' => 'A Simple Product',
        'price' => '2000'
    ]);

    $product = Product::create();

    $product->productDataType()->associate($product_data);

    $this->assertEquals(1, SimpleProductDataType::count());

    Cart::add($product, 5);

    $this->assertEquals(1, Cart::items()->count());
    $this->assertEquals(5, Cart::items()->first()->quantity);

    Cart::clear();

    $this->assertEquals(0, Cart::items()->count());

});

it('provide the subtotal', function () {

    $product_data1 = SimpleProductDataType::create([
        'name' => 'A Simple Product',
        'price' => '2000'
    ]);

    $product1 = Product::create();
    $product1->productDataType()->associate($product_data1);
    $product1->save();

    $product_data2 = SimpleProductDataType::create([
        'name' => 'A Second Simple Product',
        'price' => '150'
    ]);

    $product2 = Product::create();
    $product2->productDataType()->associate($product_data2);
    $product2->save();

    Cart::add($product1);
    Cart::add($product2, 2);

    $this->assertEquals('A Simple Product', $product1->getName());

    $this->assertTrue(Cart::isInCart($product1->id));
    $this->assertTrue(Cart::isInCart($product2->id));

    $this->assertEquals(2300, Cart::getSubtotal());

});

it('provide the correct subtotal when a price of a product is calculated', function () {

    $simple_product_data = SimpleProductDataType::create([
        'name' => 'A Simple Product',
        'price' => '2000'
    ]);

    $simple_product = Product::create();

    $simple_product->productDataType()->associate($simple_product_data);
    $simple_product->save();

    $complex_product_data = ComplexProductDataType::create([
        'name' => 'A Simple Product',
        'width' => '10',
        'height' => '20'
    ]);

    $complex_product = Product::create();

    $complex_product->productDataType()->associate($complex_product_data);
    $complex_product->save();

    Cart::add($simple_product);
    Cart::add($complex_product, 2);

    $this->assertEquals(2400, Cart::getSubtotal());

});

it('will state whether a product is in the cart', function () {

    $simple_product_data = SimpleProductDataType::create([
        'name' => 'A Simple Product',
        'price' => '2000'
    ]);

    $simple_product = Product::create();
    $simple_product->productDataType()->associate($simple_product_data);
    $simple_product->save();

    $complex_product_data = ComplexProductDataType::create([
        'name' => 'A Simple Product',
        'width' => '10',
        'height' => '20'
    ]);

    $complex_product = Product::create();
    $complex_product->productDataType()->associate($complex_product_data);
    $complex_product->save();

    Cart::add($complex_product, 2);
    //Cart::add($product);

    $this->assertTrue(Cart::isInCart($complex_product->id));
    $this->assertFalse(Cart::isInCart($simple_product->id));

});

it('can add a product with product data', function () {

    $variable_product_data = VariableProductDataType::create([
        'name' => 'A variable product'
    ]);

    $variable_product = Product::create();

    $variable_product->productDataType()->associate($variable_product_data);
    $variable_product->save();

    $product_data = [
        'width' => 10,
        'height' => 10
    ];

    $this->assertEquals(100, $variable_product->getPrice($product_data));
    $this->assertEquals(120, $variable_product->getPrice());

    Cart::add($variable_product, 1, [
        'width' => 10,
        'height' => 10
    ]);

    $this->assertTrue(Cart::isInCart($variable_product->id));
    $this->assertEquals(100, Cart::getSubtotal());

});

it('will increment the quantity of a cart item if the cart already has a product with the same data', function()
{
    $variable_product_data = VariableProductDataType::create([
        'name' => 'A variable product'
    ]);

    $variable_product = Product::create();

    $variable_product->productDataType()->associate($variable_product_data);
    $variable_product->save();

    $product_data = [
        'width' => 10,
        'height' => 10
    ];

    Cart::add($variable_product, 1, [
        'width' => 10,
        'height' => 10
    ]);

    $this->assertEquals(1, Cart::items()->first()->quantity);

    Cart::add($variable_product, 1, [
        'width' => 10,
        'height' => 10
    ]);

    $this->assertEquals(2, Cart::items()->first()->quantity);
});

test('the facade will throw an error if the method does not exist', function () {

    $this->expectException(\BadMethodCallException::class);

    Cart::nonExistantMethod();

});
