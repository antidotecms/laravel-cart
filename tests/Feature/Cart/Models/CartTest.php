<?php

use Antidote\LaravelCart\DataTransferObjects\CartItem;
use Antidote\LaravelCart\Facades\Cart;
use Antidote\LaravelCart\Tests\Fixtures\App\Models\Products\TestProduct;
use Antidote\LaravelCart\Tests\Fixtures\App\Models\ProductTypes\ComplexProductDataType;
use Antidote\LaravelCart\Tests\Fixtures\App\Models\ProductTypes\SimpleProductDataType;
use Antidote\LaravelCart\Tests\Fixtures\App\Models\ProductTypes\VariableProductDataType;
use Antidote\LaravelCart\Tests\Fixtures\App\Models\TestOrder;

/**
 * @covers \Antidote\LaravelCart\Domain\Cart
 */
it('can add a product to the cart', function() {

    $product_data = SimpleProductDataType::create([
        'price' => '2000'
    ]);

    $product = TestProduct::create([
        'description' => 'It\'s really very simple'
    ]);

    $product->productType()->associate($product_data);
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

it('will get a cart items cost', function() {

    $simple_product = TestProduct::factory()->asSimpleProduct([
        'price' => 105
    ])->create();;

    Cart::add($simple_product, 3);


    expect(Cart::items()->first()->getCost())->toBe(315);

    Cart::clear();

    $complex_product = TestProduct::factory()->asComplexProduct([
        'width' => 10,
        'height' => 10
    ])->create();

    Cart::add($complex_product, 2);

    expect(Cart::items()->first()->getCost())->toBe(200);

    Cart::clear();

    $variable_product = TestProduct::factory()->asVariableProduct()->create();

    $product_data = [
        'width' => 10,
        'height' => 20
    ];

    Cart::add($variable_product, 3 , $product_data);

    expect(Cart::items()->first()->getCost())->toBe(600);

});

it('can add a product and specify quantity', function () {

    $product_data = SimpleProductDataType::create([
        'price' => '2000'
    ]);

    $product = TestProduct::create([
        'name' => 'A Simple Product'
    ]);

    $product->productType()->associate($product_data);
    $product->save();

    Cart::add($product, 3);

    $this->assertEquals(1, Cart::items()->count());
    $this->assertEquals(3, Cart::items()->first()->quantity);
    $this->assertEquals(6000, Cart::items()->first()->getCost());

});

it('can remove a product by product id', function () {

    $customer = \Antidote\LaravelCart\Models\Customer::factory()->create();

    $product_data = SimpleProductDataType::create([
        'price' => '2000'
    ]);

    $product = TestProduct::create([
        'name' => 'A Simple Product',
    ]);

    $product->productType()->associate($product_data);
    $product->save();

    $this->be($customer, 'customer');

    Cart::add($product);

    $this->assertEquals(1, Cart::items()->count());

    Cart::remove($product);
    //$customer->refresh();

    $this->assertEquals(0, Cart::items()->count());

});

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
        'price' => '2000'
    ]);

    $product = TestProduct::create([
        'name' => 'A Simple Product',
    ]);

    $product->productType()->associate($product_data);
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
        'price' => '2000'
    ]);

    $product = TestProduct::create([
        'name' => 'A Simple Product',
    ]);

    $product->productType()->associate($product_data);
    $product->save();

    $this->assertEquals(1, SimpleProductDataType::count());

    Cart::add($product, 5);

    $this->assertEquals(1, Cart::items()->count());
    $this->assertEquals(5, Cart::items()->first()->quantity);

    Cart::clear();

    $this->assertEquals(0, Cart::items()->count());

});

it('provide the subtotal', function () {

    $product_data1 = SimpleProductDataType::create([
        'price' => '2000'
    ]);

    $product1 = TestProduct::create([
        'name' => 'A Simple Product',
    ]);
    $product1->productType()->associate($product_data1);
    $product1->save();

    $product_data2 = SimpleProductDataType::create([
        'price' => '150'
    ]);

    $product2 = TestProduct::create([
        'name' => 'A Second Simple Product'
    ]);
    $product2->productType()->associate($product_data2);
    $product2->save();

    Cart::add($product1);
    Cart::add($product2, 2);

    $this->assertEquals('A Simple Product', $product1->getName());

    $this->assertTrue(Cart::isInCart($product1->id));
    $this->assertTrue(Cart::isInCart($product2->id));

    $this->assertEquals(2300, Cart::getSubtotal());

});

it('provides the correct subtotal when a price of a product is calculated', function () {

    $simple_product_data = SimpleProductDataType::create([
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

    Cart::add($simple_product);
    Cart::add($complex_product, 2);

    $this->assertEquals(2400, Cart::getSubtotal());

});

it('will state whether a product is in the cart', function () {

    $simple_product_data = SimpleProductDataType::create([
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

    Cart::add($complex_product, 2);
    //Cart::add($product);

    $this->assertTrue(Cart::isInCart($complex_product->id));
    $this->assertFalse(Cart::isInCart($simple_product->id));

});

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

    Cart::add($variable_product, 1, [
        'width' => 10,
        'height' => 10
    ]);

    $this->assertTrue(Cart::isInCart($variable_product->id));
    $this->assertEquals(100, Cart::getSubtotal());

});

it('will increment the quantity of a cart item if the cart already has a product with the same data', function()
{
    $variable_product_data = VariableProductDataType::create();

    $simple_product_data = SimpleProductDataType::create([
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

    Cart::add($variable_product, 1, $product_data);

    Cart::add($variable_product, 1, $product_data2);

    Cart::add($simple_product, 1, $product_data3);

    $this->assertEquals(1, Cart::items()->first()->quantity);
    $this->assertEquals(1, Cart::items()->skip(1)->first()->quantity);
    $this->assertEquals(1, Cart::items()->skip(2)->first()->quantity);

    Cart::add($variable_product, 1, [
        'width' => 10,
        'height' => 10
    ]);

    $this->assertEquals(2, Cart::items()->first()->quantity);
    $this->assertEquals(1, Cart::items()->skip(1)->first()->quantity);
    $this->assertEquals(1, Cart::items()->skip(2)->first()->quantity);
});

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

    Cart::add($variable_product, 1, $product_data);
    Cart::add($variable_product, 1, $product_data2);
    Cart::add($variable_product, 2, $product_data);

    $this->assertEquals(3, Cart::items()->first()->quantity);
    $this->assertEquals(1, Cart::items()->skip(1)->first()->quantity);
});

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

    Cart::add($variable_product, 1, [
        'width' => 10,
        'height' => 10
    ]);

    Cart::remove($variable_product, 2, $product_data);

    $this->assertEquals(0, Cart::items()->count());
});

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

    Cart::add($variable_product, 1, $product_data);

    expect(Cart::items()->count())->toBe(1);

    Cart::add($variable_product, 1, $product_data);

    expect(Cart::items()->count())->toBe(1)
        ->and(Cart::items()->first()->quantity)->toBe(2);

    Cart::add($variable_product, 1, $product_data2);

    expect(Cart::items()->count())->toBe(2);

});

test('the facade will throw an error if the method does not exist', function () {

    $this->expectException(\BadMethodCallException::class);

    Cart::nonExistantMethod();

});

it('will not allow adding a product with a negative quantity', function() {

    $product = TestProduct::factory()->asSimpleProduct()->create();

    Cart::add($product, -1);
})
->throws(InvalidArgumentException::class, 'Quantity must be greater than or equal to one');

it('will not allow adding a product without a product type', function() {

    $product = TestProduct::factory()->create();

    Cart::add($product, 1);
})
->throws(InvalidArgumentException::class, 'Product has no product data type associated');

it('will not allow a product to be added if it is invalid', function () {

    $product = TestProduct::factory()->asComplexProduct([
        'width' => 20,
        'height' => 20
    ])->create();

    Cart::add($product);

    expect(Cart::items()->count())->toBe(0);
})
->throws(InvalidArgumentException::class, 'The cart item is invalid');

it('will set up a payment', function () {

    $simple_product = TestProduct::factory()->asSimpleProduct([
        'price' => 1000
    ])->create();

    $customer = \Antidote\LaravelCart\Models\Customer::factory()->create();

    Cart::add($simple_product);

    Cart::createOrder($customer);

    Cart::initializePayment($customer->orders()->first());
    //PaymentIntent::create($customer->orders()->first());

    expect(\Antidote\LaravelCart\Models\Order::count())->toBe(1);
    expect(get_class(\Antidote\LaravelCart\Models\Order::first()->payment))->toBe(\Antidote\LaravelCart\Tests\Fixtures\App\Models\TestPayment::class);
});

it('will not create an order if the amount is out of bounds', function () {

    $simple_product = TestProduct::factory()->asSimpleProduct([
        'price' => 1
    ])->create();

    $customer = \Antidote\LaravelCart\Models\Customer::factory()->create();

    Cart::add($simple_product);

    $result = Cart::createOrder($customer);

    expect($result)->toBeFalse();
    expect(TestOrder::count())->toBe(0);
});

it('can add a note to the cart', function () {

    $simple_product = TestProduct::factory()->asSimpleProduct([
        'price' => 1
    ])->create();

    $customer = \Antidote\LaravelCart\Models\Customer::factory()->create();

    Cart::add($simple_product);

    Cart::addData('note', 'this is a note');

    expect(Cart::getData('note'))->toBe('this is a note');

    expect(Cart::getData('non_existant'))->toBe('');
});

it('can return all data', function () {

    $simple_product = TestProduct::factory()->asSimpleProduct([
        'price' => 100
    ])->create();

    $customer = \Antidote\LaravelCart\Models\Customer::factory()->create();

    Cart::add($simple_product);

    Cart::addData('note', 'this is a note');
    Cart::addData('another_note', 'this is another note');

    expect(count(Cart::getData()))->toBe(2);
});

it('can add a note to the order', function () {

    $this->markTestIncomplete('Requires extending base class - need tests for this?');

    $simple_product = TestProduct::factory()->asSimpleProduct([
        'price' => 100
    ])->create();

    $customer = Customer::factory()->create();

    Cart::add($simple_product);

    Cart::addData('additional_field', 'this is a note');

    Cart::createOrder($customer);

    expect(TestOrder::count())->toBe(1);
    expect(TestOrder::first()->additional_field)->toBe('this is a note');

});

it('will add order adjustments to the order when creating an order', function () {

    $simple_product = TestProduct::factory()->asSimpleProduct([
        'price' => 1000
    ])->create();

    $customer = \Antidote\LaravelCart\Models\Customer::factory()->create();

    $adjustment = \Antidote\LaravelCart\Tests\Fixtures\App\Models\TestAdjustment::create([
        'name' => '10 percent off',
        'class' => \Antidote\LaravelCart\Tests\Fixtures\App\Models\Adjustments\DiscountAdjustmentCalculation::class,
        'parameters' => [
            'type' => 'percentage',
            'rate' => 10
        ],
        'apply_to_subtotal' => true
    ]);

    Cart::add($simple_product);

    expect(Cart::getSubtotal())->toBe(1000);
    expect(Cart::getTotal())->toBe(900);
    expect(Cart::getValidAdjustments(true)->count())->toBe(1);
    expect(Cart::getValidAdjustments(false)->count())->toBe(0);

    $order = Cart::createOrder($customer);

    expect(TestOrder::count())->toBe(1);

    $order = TestOrder::first();

    expect($order->adjustments->count())->toBe(1);

    $order_adjustment = \Antidote\LaravelCart\Tests\Fixtures\App\Models\TestOrderAdjustment::first();

    expect($order_adjustment->name)->toBe('10 percent off');
    expect($order_adjustment->class)->toBe(\Antidote\LaravelCart\Tests\Fixtures\App\Models\Adjustments\DiscountAdjustmentCalculation::class);
    expect($order_adjustment->original_parameters)->toBe([
        'type' => 'percentage',
        'rate' => 10
    ]);
    expect($order_adjustment->apply_to_subtotal)->toBeTruthy();
    expect($order->getSubtotal())->toBe(1000);
    expect($order->total)->toBe(1080); //900 * 1.2 - 0.2 tax rate
});

it('will update adjustments if an order has not been completed and items added back into the cart', function () {

});
