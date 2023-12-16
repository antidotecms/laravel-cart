<?php

use Antidote\LaravelCart\DataTransferObjects\CartItem;
use Antidote\LaravelCart\Facades\Cart;
use Antidote\LaravelCart\Models\Products\SimpleProductType;
use Antidote\LaravelCart\Tests\Fixtures\App\Models\Products\TestProduct;
use Antidote\LaravelCart\Tests\Fixtures\App\Models\ProductTypes\ComplexProductDataType;
use Antidote\LaravelCart\Tests\Fixtures\App\Models\ProductTypes\VariableProductDataType;

beforeEach(function() {
   $this->cart = app(\Antidote\LaravelCart\Domain\Cart::class);
});
/**
 * @covers \Antidote\LaravelCart\Domain\Cart
 */
it('can add a product to the cart', function() {

    $product_data = SimpleProductType::create([
        'price' => '2000'
    ]);

    $product = TestProduct::create([
        'description' => 'It\'s really very simple'
    ]);

    $product->productType()->associate($product_data);
    $product->save();

    $this->cart->add($product);

    $this->assertEquals(1, $this->cart->items()->count());

    $product_data = new CartItem([
        'product_id' => $product->id,
        'product_type' => SimpleProductType::class,
        'product' => SimpleProductType::find($product->id),
        'quantity' => 1,
        'specification' => null
    ]);

    $this->assertEquals($product_data, $this->cart->items()->first());

})
->coversClass(\Antidote\LaravelCart\Domain\Cart::class);

it('will get a cart items cost', function() {

    $simple_product = TestProduct::factory()->asSimpleProduct([
        'price' => 105
    ])->create();;

    $cart = app(\Antidote\LaravelCart\Domain\Cart::class);
    $cart->add($simple_product, 3);


    expect($cart->items()->first()->getCost())->toBe(315);

    $cart->clear();

    $complex_product = TestProduct::factory()->asComplexProduct([
        'width' => 10,
        'height' => 10
    ])->create();

    $cart->add($complex_product, 2);

    expect($cart->items()->first()->getCost())->toBe(200);

    $cart->clear();

    $variable_product = TestProduct::factory()->asVariableProduct()->create();

    $product_data = [
        'width' => 10,
        'height' => 20
    ];

    $cart->add($variable_product, 3 , $product_data);

    expect($cart->items()->first()->getCost())->toBe(600);

})
->coversClass(\Antidote\LaravelCart\Domain\Cart::class);

it('can add a product and specify quantity', function () {

    $product_data = SimpleProductType::create([
        'price' => '2000'
    ]);

    $product = TestProduct::create([
        'name' => 'A Simple Product'
    ]);

    $product->productType()->associate($product_data);
    $product->save();

    $cart = app(\Antidote\LaravelCart\Domain\Cart::class);
    $cart->add($product, 3);

    $this->assertEquals(1, $cart->items()->count());
    $this->assertEquals(3, $cart->items()->first()->quantity);
    $this->assertEquals(6000, $cart->items()->first()->getCost());

})
->coversClass(\Antidote\LaravelCart\Domain\Cart::class);

it('can remove a product by product id', function () {

    $customer = \Antidote\LaravelCart\Models\Customer::factory()->create();

    $product_data = SimpleProductType::create([
        'price' => '2000'
    ]);

    $product = TestProduct::create([
        'name' => 'A Simple Product',
    ]);

    $product->productType()->associate($product_data);
    $product->save();

    $this->be($customer, 'customer');

    $cart = app(\Antidote\LaravelCart\Domain\Cart::class);
    $cart->add($product);

    $this->assertEquals(1, $cart->items()->count());

    $cart->remove($product);
    //$customer->refresh();

    $this->assertEquals(0, $cart->items()->count());

})
->coversClass(\Antidote\LaravelCart\Domain\Cart::class);

it('can remove a product by product id specifying quantity', function () {

    $customer = \Antidote\LaravelCart\Models\Customer::factory()->create();

    $product_data = SimpleProductType::create([
        'price' => '2000'
    ]);

    $product = TestProduct::create([
        'name' => 'A Simple Product',
    ]);

    $product->productType()->associate($product_data);
    $product->save();

    $this->be($customer, 'customer');

    $cart = app(\Antidote\LaravelCart\Domain\Cart::class);
    $cart->add($product, 3);

    $this->assertEquals(1, $cart->items()->count());
    $this->assertEquals(3, $cart->items()->first()->quantity);

    $cart->remove($product, 2);
    //$customer->refresh();

    $this->assertEquals(1, $cart->items()->first()->quantity);

})
->coversClass(\Antidote\LaravelCart\Domain\Cart::class);

it('it can remove a product by product id and product data', function () {

    $variable_product_data_type1 = VariableProductDataType::create();

    $variable_product1 = TestProduct::create([
        'name' => 'A variable product'
    ]);

    $variable_product1->productType()->associate($variable_product_data_type1);
    $variable_product1->save();

    $variable_product_data_type2 = VariableProductDataType::create();

    $variable_product2 = TestProduct::create([
        'name' => 'Another variable product'
    ]);

    $variable_product2->productType()->associate($variable_product_data_type2);
    $variable_product2->save();

    $this->cart->add($variable_product1, 1, [
        'width' => 10,
        'height' => 10
    ]);

    $this->cart->add($variable_product2, 1, [
        'width' => 10,
        'height' => 20
    ]);

    $this->assertEquals(2, $this->cart->items()->count());

    $this->cart->remove($variable_product1);

    $this->assertEquals(1, $this->cart->items()->count());

    $expected_product = new CartItem([
        'product_id' => $variable_product2->id,
        'quantity' => 1,
        'product_data' => [
            'width' => 10,
            'height' => 20
        ]
    ]);

    $this->assertEquals($expected_product, $this->cart->items()->first());
})
->coversClass(\Antidote\LaravelCart\Domain\Cart::class);

it('it can remove a product by product id and product data specifying quantity', function () {

    $product_data = SimpleProductType::create([
        'price' => '2000'
    ]);

    $product = TestProduct::create([
        'name' => 'A Simple Product',
    ]);

    $product->productType()->associate($product_data);
    $product->save();

    $this->cart->add($product, 5);

    $this->assertEquals(1, $this->cart->items()->count());
    $this->assertEquals(5, $this->cart->items()->first()->quantity);

    $this->cart->remove($product, 2);
    //$customer->refresh();

    $this->assertEquals(1, $this->cart->items()->count());
    $this->assertEquals(3, $this->cart->items()->first()->quantity);

})
->coversClass(\Antidote\LaravelCart\Domain\Cart::class);

it('will remove an item completely with just product data', function () {

    $variable_product_data_type1 = VariableProductDataType::create();

    $variable_product1 = TestProduct::create([
        'name' => 'A variable product'
    ]);

    $variable_product1->productType()->associate($variable_product_data_type1);
    $variable_product1->save();

    $this->cart->add($variable_product1, product_data: [
        'width' => 10,
        'height' => 10
    ]);

    expect($this->cart->items()->count())->toBe(1);

    $this->cart->remove($variable_product1, product_data: [
        'width' => 10,
        'height' => 20
    ]);

    expect($this->cart->items()->count())->toBe(1);

    $this->cart->remove($variable_product1, product_data: [
        'width' => 10,
        'height' => 10
    ]);

    expect($this->cart->items()->count())->toBe(0);
});

it('will not remove an item if it is not in the cart', function () {

    $customer = \Antidote\LaravelCart\Models\Customer::factory()->create();

    $product_data = SimpleProductType::create([
        'price' => '2000'
    ]);

    $product = TestProduct::create([
        'name' => 'A Simple Product',
    ]);

    $product2 = TestProduct::create([
        'name' => 'Another Simple Product',
    ]);

    $product->productType()->associate($product_data);
    $product->save();

    $this->be($customer, 'customer');

    $this->cart->add($product, 3);

    $this->assertEquals(1, $this->cart->items()->count());
    $this->assertEquals(3, $this->cart->items()->first()->quantity);

    $this->cart->remove($product2, 2);
    //$customer->refresh();

    $this->assertEquals(3, $this->cart->items()->first()->quantity);
});

it('will not remove an item if the cart is empty', function () {

    $product = TestProduct::create([
        'name' => 'A Simple Product',
    ]);

    expect($this->cart->items()->count())->toBe(0);

    $this->cart->remove($product);

    expect($this->cart->items()->count())->toBe(0);
});

it('can clear the contents of the cart', function () {

    $product_data = SimpleProductType::create([
        'price' => '2000'
    ]);

    $product = TestProduct::create([
        'name' => 'A Simple Product',
    ]);

    $product->productType()->associate($product_data);
    $product->save();

    $this->assertEquals(1, SimpleProductType::count());

    $this->cart->add($product, 5);

    $this->assertEquals(1, $this->cart->items()->count());
    $this->assertEquals(5, $this->cart->items()->first()->quantity);

    $this->cart->clear();

    $this->assertEquals(0, $this->cart->items()->count());

})
->coversClass(\Antidote\LaravelCart\Domain\Cart::class);

it('provide the subtotal', function () {

    $product_data1 = SimpleProductType::create([
        'price' => '2000'
    ]);

    $product1 = TestProduct::create([
        'name' => 'A Simple Product',
    ]);
    $product1->productType()->associate($product_data1);
    $product1->save();

    $product_data2 = SimpleProductType::create([
        'price' => '150'
    ]);

    $product2 = TestProduct::create([
        'name' => 'A Second Simple Product'
    ]);
    $product2->productType()->associate($product_data2);
    $product2->save();

    $this->cart->add($product1);
    $this->cart->add($product2, 2);

    $this->assertEquals('A Simple Product', $product1->getName());

    $this->assertTrue($this->cart->isInCart($product1->id));
    $this->assertTrue($this->cart->isInCart($product2->id));

    $this->assertEquals(2300, $this->cart->getSubtotal());

})
->coversClass(\Antidote\LaravelCart\Domain\Cart::class);

it('provides the correct subtotal when a price of a product is calculated', function () {

    $simple_product_data = SimpleProductType::create([
        'price' => '2000'
    ]);

    $simple_product = TestProduct::create([
        'name' => 'A Simple Product'
    ]);

    $simple_product->productType()->associate($simple_product_data);
    $simple_product->save();

    $complex_product_data = ComplexProductDataType::create([
        'width' => '10',
        'height' => '20'
    ]);

    $complex_product = TestProduct::create([
        'name' => 'A Complex Product'
    ]);

    $complex_product->productType()->associate($complex_product_data);
    $complex_product->save();

    $this->cart->add($simple_product);
    $this->cart->add($complex_product, 2);

    $this->assertEquals(2400, $this->cart->getSubtotal());

})
->coversClass(\Antidote\LaravelCart\Domain\Cart::class);

it('will state whether a product is in the cart', function () {

    $simple_product_data = SimpleProductType::create([
        'price' => '2000'
    ]);

    $simple_product = TestProduct::create([
        'name' => 'A Simple Product'
    ]);
    $simple_product->productType()->associate($simple_product_data);
    $simple_product->save();

    $complex_product_data = ComplexProductDataType::create([
        'width' => '10',
        'height' => '20'
    ]);

    $complex_product = TestProduct::create([
        'name' => 'A Simple Product'
    ]);
    $complex_product->productType()->associate($complex_product_data);
    $complex_product->save();

    $this->cart->add($complex_product, 2);
    //$this->cart->add($product);

    $this->assertTrue($this->cart->isInCart($complex_product->id));
    $this->assertFalse($this->cart->isInCart($simple_product->id));

})
->coversClass(\Antidote\LaravelCart\Domain\Cart::class);

it('can add a product with product data', function () {

    $variable_product_data = VariableProductDataType::create();

    $variable_product = TestProduct::create([
        'name' => 'A variable product'
    ]);

    $variable_product->productType()->associate($variable_product_data);
    $variable_product->save();

    $product_data = [
        'width' => 10,
        'height' => 10
    ];

    $this->assertEquals(100, $variable_product->getPrice($product_data));

    $this->cart->add($variable_product, 1, [
        'width' => 10,
        'height' => 10
    ]);

    $this->assertTrue($this->cart->isInCart($variable_product->id));
    $this->assertEquals(100, $this->cart->getSubtotal());

})
->coversClass(\Antidote\LaravelCart\Domain\Cart::class);

it('will increment the quantity of a cart item if the cart already has a product with the same data', function()
{
    $variable_product_data = VariableProductDataType::create();

    $simple_product_data = SimpleProductType::create([
        'price' => 100
    ]);

    $variable_product = TestProduct::create([
        'name' => 'A variable product'
    ]);

    $variable_product->productType()->associate($variable_product_data);
    $variable_product->save();

    $simple_product = TestProduct::create([
        'name' => 'A simple product'
    ]);

    $simple_product->productType()->associate($simple_product_data);
    $simple_product->save();

    $product_data = [
        'width' => 10,
        'height' => 10
    ];

    $product_data2 = [
        'width' => 20,
        'height' => 20
    ];

    $product_data3 = [
        'width' => 30,
        'height' => 30
    ];

    $this->cart->add($variable_product, 1, $product_data);

    $this->cart->add($variable_product, 1, $product_data2);

    $this->cart->add($simple_product, 1, $product_data3);

    $this->assertEquals(1, $this->cart->items()->first()->quantity);
    $this->assertEquals(1, $this->cart->items()->skip(1)->first()->quantity);
    $this->assertEquals(1, $this->cart->items()->skip(2)->first()->quantity);

    $this->cart->add($variable_product, 1, [
        'width' => 10,
        'height' => 10
    ]);

    $this->assertEquals(2, $this->cart->items()->first()->quantity);
    $this->assertEquals(1, $this->cart->items()->skip(1)->first()->quantity);
    $this->assertEquals(1, $this->cart->items()->skip(2)->first()->quantity);
})
->coversClass(\Antidote\LaravelCart\Domain\Cart::class);

it('will amend the quantity of a cart item', function () {

    $variable_product_data = VariableProductDataType::create();

    $variable_product = TestProduct::create([
        'name' => 'A variable product'
    ]);

    $variable_product->productType()->associate($variable_product_data);
    $variable_product->save();

    $product_data = [
        'width' => 10,
        'height' => 10
    ];

    $product_data2 = [
        'width' => 20,
        'height' => 20
    ];

    $this->cart->add($variable_product, 1, $product_data);
    $this->cart->add($variable_product, 1, $product_data2);
    $this->cart->add($variable_product, 2, $product_data);

    $this->assertEquals(3, $this->cart->items()->first()->quantity);
    $this->assertEquals(1, $this->cart->items()->skip(1)->first()->quantity);
})
->coversClass(\Antidote\LaravelCart\Domain\Cart::class);

it('will remove a product if it is asked to remove more than is in the cart', function () {

    $variable_product_data = VariableProductDataType::create();

    $variable_product = TestProduct::create([
        'name' => 'A variable product'
    ]);

    $variable_product->productType()->associate($variable_product_data);
    $variable_product->save();

    $product_data = [
        'width' => 10,
        'height' => 10
    ];

    $this->cart->add($variable_product, 1, [
        'width' => 10,
        'height' => 10
    ]);

    $this->cart->remove($variable_product, 2, $product_data);

    $this->assertEquals(0, $this->cart->items()->count());
})
->coversClass(\Antidote\LaravelCart\Domain\Cart::class);

it('will add two cart items if the product data is different', function () {

    $variable_product_data = VariableProductDataType::create();

    $variable_product = TestProduct::create([
        'name' => 'A variable product'
    ]);

    $variable_product->productType()->associate($variable_product_data);
    $variable_product->save();

    $product_data = [
        'width' => 10,
        'height' => 10
    ];

    $product_data2 = [
        'width' => 20,
        'height' => 20
    ];

    $this->cart->add($variable_product, 1, $product_data);

    expect($this->cart->items()->count())->toBe(1);

    $this->cart->add($variable_product, 1, $product_data);

    expect($this->cart->items()->count())->toBe(1)
        ->and($this->cart->items()->first()->quantity)->toBe(2);

    $this->cart->add($variable_product, 1, $product_data2);

    expect($this->cart->items()->count())->toBe(2);

})
->coversClass(\Antidote\LaravelCart\Domain\Cart::class);

//test('the facade will throw an error if the method does not exist', function () {
//
//    $this->expectException(\BadMethodCallException::class);
//
//    $this->cart->nonExistantMethod();
//
//})
//->coversClass(\Antidote\LaravelCart\Domain\Cart::class);

it('will not allow adding a product with a negative quantity', function() {

    $product = TestProduct::factory()->asSimpleProduct()->create();

    $this->cart->add($product, -1);
})
->coversClass(\Antidote\LaravelCart\Domain\Cart::class)
->throws(InvalidArgumentException::class, 'Quantity must be greater than or equal to one');

it('will not allow adding a product without a product type', function() {

    $product = TestProduct::factory()->create();

    $this->cart->add($product, 1);
})
->coversClass(\Antidote\LaravelCart\Domain\Cart::class)
->throws(InvalidArgumentException::class, 'Product has no product data type associated');

it('will not allow a product to be added if it is invalid', function () {

    $product = TestProduct::factory()->asComplexProduct([
        'width' => 20,
        'height' => 20
    ])->create();

    $this->cart->add($product);

    expect($this->cart->items()->count())->toBe(0);
})
->coversClass(\Antidote\LaravelCart\Domain\Cart::class)
->throws(InvalidArgumentException::class, 'The cart item is invalid');

it('will throw an exception if the amount is too low', function () {

    $simple_product = TestProduct::factory()->asSimpleProduct([
        'price' => 1
    ])->create();

    $customer = \Antidote\LaravelCart\Models\Customer::factory()->create();

    $this->cart->add($simple_product);

    $result = $this->cart->createOrder($customer);

    //expect($result)->toBeFalse();
    //below doesnt get fired as exception hit first
    //expect(\Antidote\LaravelCart\Models\Order::count())->toBe(0);
})
->coversClass(\Antidote\LaravelCart\Domain\Cart::class)
->throws(\Exception::class, 'The order total must be greater than £0.30 and less that £999,999.99');

it('will throw an exception if the amount is too high', function () {

    $simple_product = TestProduct::factory()->asSimpleProduct([
        'price' => 100000000
    ])->create();

    $customer = \Antidote\LaravelCart\Models\Customer::factory()->create();

    $this->cart->add($simple_product);

    $result = $this->cart->createOrder($customer);

    //expect($result)->toBeFalse();
    //below doesnt get fired as exception hit first
    //expect(\Antidote\LaravelCart\Models\Order::count())->toBe(0);
})
    ->coversClass(\Antidote\LaravelCart\Domain\Cart::class)
    ->throws(\Exception::class, 'The order total must be greater than £0.30 and less that £999,999.99');

it('can add a note to the cart', function () {

    $simple_product = TestProduct::factory()->asSimpleProduct([
        'price' => 1
    ])->create();

    $customer = \Antidote\LaravelCart\Models\Customer::factory()->create();

    $this->cart->add($simple_product);

    $this->cart->addData('note', 'this is a note');

    expect($this->cart->getData('note'))->toBe('this is a note');

    expect($this->cart->getData('non_existant'))->toBe('');
})
->coversClass(\Antidote\LaravelCart\Domain\Cart::class);

it('can return all data', function () {

    $simple_product = TestProduct::factory()->asSimpleProduct([
        'price' => 100
    ])->create();

    $customer = \Antidote\LaravelCart\Models\Customer::factory()->create();

    $this->cart->add($simple_product);

    $this->cart->addData('note', 'this is a note');
    $this->cart->addData('another_note', 'this is another note');

    expect(count($this->cart->getData()))->toBe(2);
})
->coversClass(\Antidote\LaravelCart\Domain\Cart::class);

it('can add a note to the order', function () {

    $simple_product = TestProduct::factory()->asSimpleProduct([
        'price' => 100
    ])->create();

    $customer = \Antidote\LaravelCart\Models\Customer::factory()->create();

    $this->cart->add($simple_product);

    $this->cart->addData('additional_field', 'this is a note');

    $this->cart->createOrder($customer);

    expect(\Antidote\LaravelCart\Models\Order::count())->toBe(1);
    expect(\Antidote\LaravelCart\Models\Order::first()->getData('additional_field'))->toBe('this is a note');

})
->coversClass(\Antidote\LaravelCart\Domain\Cart::class);

it('will add order adjustments to the order when creating an order', function () {

    $simple_product = TestProduct::factory()->asSimpleProduct([
        'price' => 1000
    ])->create();

    $customer = \Antidote\LaravelCart\Models\Customer::factory()->create();

    $adjustment = \Antidote\LaravelCart\Models\Adjustment::factory()->create([
        'name' => '10 percent off',
        'class' => \Antidote\LaravelCart\Tests\Fixtures\App\Models\Adjustments\DiscountAdjustmentCalculation::class,
        'parameters' => [
            'type' => 'percentage',
            'rate' => 10
        ],
        'apply_to_subtotal' => true
    ]);

    $this->cart->add($simple_product);

    expect($this->cart->getSubtotal())->toBe(1000);
    expect($this->cart->getTotal())->toBe(900);
    expect($this->cart->getValidAdjustments(true)->count())->toBe(1);
    expect($this->cart->getValidAdjustments(false)->count())->toBe(0);

    $order = $this->cart->createOrder($customer);

    expect(\Antidote\LaravelCart\Models\Order::count())->toBe(1);

    $order = \Antidote\LaravelCart\Models\Order::first();

    expect($order->adjustments->count())->toBe(1);

    $order_adjustment = \Antidote\LaravelCart\Models\OrderAdjustment::first();

    expect($order_adjustment->name)->toBe('10 percent off');
    expect($order_adjustment->class)->toBe(\Antidote\LaravelCart\Tests\Fixtures\App\Models\Adjustments\DiscountAdjustmentCalculation::class);
    expect($order_adjustment->original_parameters)->toBe([
        'type' => 'percentage',
        'rate' => 10
    ]);
    expect($order_adjustment->apply_to_subtotal)->toBeTruthy();
    expect($order->subtotal)->toBe(1000);
    expect($order->total)->toBe(1080); //900 * 1.2 - 0.2 tax rate
})
->coversClass(\Antidote\LaravelCart\Domain\Cart::class);

it('will clear the order_adjustments from order if cart is empty', function () {

    $simple_product = TestProduct::factory()->asSimpleProduct([
        'price' => 1000
    ])->create();

    $customer = \Antidote\LaravelCart\Models\Customer::factory()->create();

    $adjustment = \Antidote\LaravelCart\Models\Adjustment::factory()->create([
        'name' => '10 percent off',
        'class' => \Antidote\LaravelCart\Tests\Fixtures\App\Models\Adjustments\DiscountAdjustmentCalculation::class,
        'parameters' => [
            'type' => 'percentage',
            'rate' => 10
        ],
        'apply_to_subtotal' => true
    ]);

    $this->cart->add($simple_product);

    expect($this->cart->getSubtotal())->toBe(1000);
    expect($this->cart->getTotal())->toBe(900);
    expect($this->cart->getValidAdjustments(true)->count())->toBe(1);
    expect($this->cart->getValidAdjustments(false)->count())->toBe(0);

    $order = $this->cart->createOrder($customer);

    expect(\Antidote\LaravelCart\Models\Order::count())->toBe(1);

    $order = \Antidote\LaravelCart\Models\Order::first();

    expect($order->adjustments->count())->toBe(1);

    $this->cart->remove($simple_product);

    $order->refresh();

    expect($order->adjustments->count())->toBe(0);
})
->coversClass(\Antidote\LaravelCart\Domain\Cart::class);

it('will update adjustments if an order has not been completed and items added back into the cart', function () {

    $this->markTestIncomplete('To Do');
})
->coversClass(\Antidote\LaravelCart\Domain\Cart::class);

it('will set the active order with the id', function () {

    $product = TestProduct::factory()->asSimpleProduct([
        'price' => 1000
    ])->create();

    $customer = \Antidote\LaravelCart\Models\Customer::factory()->create();

    $order = \Antidote\LaravelCart\Models\Order::factory()
        ->for($customer)
        ->withProduct($product)
        ->create();

    expect($order->customer->name)->toBe($customer->name);

    expect($this->cart->getActiveOrder())->toBeNull();

    $this->cart->setActiveOrder($order->id);

    expect($this->cart->getActiveOrder())->not()->toBeNull();

    expect($this->cart->getActiveOrder())->toBeInstanceOf(\Antidote\LaravelCart\Models\Order::class);

    expect($this->cart->getActiveOrder()->id)->toBe($order->id);
});

it('will set the active order with the order object', function () {

    $product = TestProduct::factory()->asSimpleProduct([
        'price' => 1000
    ])->create();

    $customer = \Antidote\LaravelCart\Models\Customer::factory()->create();

    $order = \Antidote\LaravelCart\Models\Order::factory()
        ->for($customer)
        ->withProduct($product)
        ->create();

    expect($order->customer->name)->toBe($customer->name);

    expect($this->cart->getActiveOrder())->toBeNull();

    $this->cart->setActiveOrder($order);

    expect($this->cart->getActiveOrder())->not()->toBeNull();

    expect($this->cart->getActiveOrder())->toBeInstanceOf(\Antidote\LaravelCart\Models\Order::class);

    expect($this->cart->getActiveOrder()->id)->toBe($order->id);
});

it('will clear the active order', function () {

    $product = TestProduct::factory()->asSimpleProduct([
        'price' => 1000
    ])->create();

    $customer = \Antidote\LaravelCart\Models\Customer::factory()->create();

    $order = \Antidote\LaravelCart\Models\Order::factory()
        ->for($customer)
        ->withProduct($product)
        ->create();

    expect($order->customer->name)->toBe($customer->name);

    expect($this->cart->getActiveOrder())->toBeNull();

    $this->cart->setActiveOrder($order);

    expect($this->cart->getActiveOrder())->not()->toBeNull();

    expect($this->cart->getActiveOrder())->toBeInstanceOf(\Antidote\LaravelCart\Models\Order::class);

    expect($this->cart->getActiveOrder()->id)->toBe($order->id);

    $this->cart->setActiveOrder(null);

    expect($this->cart->getActiveOrder())->toBeNull();
});

it('will filter the valid adjustments', function () {

    $simple_product = TestProduct::factory()->asSimpleProduct([
        'price' => 1000
    ])->create();

    $customer = \Antidote\LaravelCart\Models\Customer::factory()->create();

    $adjustment = \Antidote\LaravelCart\Models\Adjustment::factory()->create([
        'name' => '10 percent off',
        'class' => \Antidote\LaravelCart\Tests\Fixtures\App\Models\Adjustments\DiscountAdjustmentCalculation::class,
        'parameters' => [
            'type' => 'percentage',
            'rate' => 10
        ],
        'apply_to_subtotal' => true
    ]);

    $this->cart->add($simple_product);

    expect($this->cart->getSubtotal())->toBe(1000);
    expect($this->cart->getTotal())->toBe(900);
    expect($this->cart->getValidAdjustments(true)->count())->toBe(1);
    expect($this->cart->getValidAdjustments(
        true,
        [
            \Antidote\LaravelCart\Tests\Fixtures\App\Models\Adjustments\DiscountAdjustmentCalculation::class
        ])->count())->toBe(0);
});
