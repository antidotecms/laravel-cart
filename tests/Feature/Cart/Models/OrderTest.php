<?php

use Antidote\LaravelCart\Domain\Discount\PercentageDiscount;
use Antidote\LaravelCart\Facades\Cart;
use Antidote\LaravelCart\Models\CartAdjustment;
use Antidote\LaravelCart\Tests\Fixtures\App\Models\Products\TestProduct;
use Antidote\LaravelCart\Tests\Fixtures\App\Models\TestCustomer;
use Antidote\LaravelCart\Tests\Fixtures\App\Models\TestOrder;
use Antidote\LaravelCart\Tests\Fixtures\App\Models\TestOrderItem;

it('will create an order', function() {

    $product = TestProduct::factory()->asSimpleProduct()->create();

    Cart::add($product);

    $customer = TestCustomer::factory()->create();

    Cart::createOrder($customer);

    $order = TestOrder::first();

    expect(TestOrder::count())->toBe(1)
        ->and($order->items()->count())->toBe(1)
        ->and($order->items()->first()->id)->toBe($product->id)
        //->and(Cart::items())->toBeEmpty() // cart no longer cleared
        ->and($order->customer->id)->toBe($customer->id);
});

it('will create an order with discount', function () {

    Config::set('laravel-cart.classes.order_adjustment', \Antidote\LaravelCart\Tests\Fixtures\App\Models\TestOrderAdjustment::class);

    $product = TestProduct::factory()->asSimpleProduct([
        'price' => '1000'
    ])->create();

    Cart::add($product);

    $customer = TestCustomer::factory()->create();

    $cart_adjustment = CartAdjustment::create([
        'name' => '10% off',
        'class' => PercentageDiscount::class,
        'parameters' => [
            'percentage' => 10
        ],
        'active' => true
    ]);


    $order = Cart::createOrder($customer);

    //even disabled, because the order was created before, it is still active on the order
    $cart_adjustment->active = false;
    $cart_adjustment->save();

    expect($order->items()->count())->toBe(1);
    expect($order->adjustments()->count())->toBe(1);
    expect($order->getSubtotal())->toBe(1000);

    //subtotal with discount should be 900. Given tax rate of 20%, total should be 900 * 1.2 = 1080
    expect($order->total)->toBe(1080);

});

test('a customer has an order', function () {

    $product = TestProduct::factory()->asSimpleProduct()->create();

    Cart::add($product);

    $customer = TestCustomer::factory()->create();

    Cart::createOrder($customer);

    expect($customer->orders()->count())->toBe(1);

});

it('will detail an order item', function () {

    $simple_product = TestProduct::factory()->asSimpleProduct([
        'price' => 1999
    ])->create([
        'name' => 'A Simple Product'
    ]);

    $complex_product = TestProduct::factory()->asComplexProduct()->create([
        'name' => 'A Complex Product'
    ]);

    Cart::add($simple_product ,2);

    $product_data = [
        'width' => 10,
        'height' => 10
    ];

    Cart::add($complex_product, 1, $product_data);

    $customer = TestCustomer::factory()->create();

    $order = Cart::createOrder($customer);

    //change price of simple product and ensure it hasn't changed in cart
    $simple_product->productType->price = 2000;
    $simple_product->productType->save();

    expect($order->items()->first()->getName())->toBe('A Simple Product')
        ->and($order->items()->first()->getPrice())->toBe(1999)
        ->and($order->items()->first()->getQuantity())->toBe(2)
        ->and($order->items()->first()->getCost())->toBe(3998)
        ->and($order->items()->skip(1)->first()->getName())->toBe('10 x 10 object')
        ->and($order->items()->skip(1)->first()->getPrice())->toBe(100)
        ->and($order->items()->skip(1)->first()->getQuantity())->toBe(1)
        ->and($order->items()->skip(1)->first()->getCost())->toBe(100);

});

it('automatically populates the fillable fields', function () {

    $test_order = new TestOrder;
    expect($test_order->getFillable())->toBe([
        'test_customer_id'
    ]);

    class NewCustomer extends \Antidote\LaravelCart\Contracts\Customer {}
    Config::set('laravel-cart.classes.customer', NewCustomer::class);
    $new_order = new class extends \Antidote\LaravelCart\Contracts\Order {
        public function updateStatus() { return null; }
    };
    expect($new_order->getFillable())->toBe([
       'new_customer_id'
    ]);

});

it('has a payment method', function () {

    $order = TestOrder::factory()->create();

    expect(get_class($order->payment))->toBe(\Antidote\LaravelCart\Tests\Fixtures\App\Models\TestPayment::class);
});

it('will get the subtotal', function () {

    $simple_product = TestProduct::factory()->asSimpleProduct([
        'price' => 1999
    ])->create([
        'name' => 'A Simple Product'
    ]);

    $customer = TestCustomer::factory()->create();

    $order = TestOrder::factory()->forCustomer($customer)->create();

    TestOrderItem::factory()->withProduct($simple_product)->forOrder($order)->create();

    expect($order->getSubtotal())->toBe(1999);

});

it('will get the total with VAT', function () {

    $simple_product = TestProduct::factory()->asSimpleProduct([
        'price' => 2000
    ])->create([
        'name' => 'A Simple Product'
    ]);

    $customer = TestCustomer::factory()->create();

    $order = TestOrder::factory()->forCustomer($customer)->create();

    TestOrderItem::factory()->withProduct($simple_product)->forOrder($order)->create();

    expect($order->total)->toBe(2400);
});

it('will not create an order if an active order already exists', function () {

    $product = TestProduct::factory()->asSimpleProduct()->create();

    Cart::add($product);

    $customer = TestCustomer::factory()->create();

    Cart::createOrder($customer);

    $order = TestOrder::first();

    expect(TestOrder::count())->toBe(1)
        ->and($order->items()->count())->toBe(1)
        ->and($order->items()->first()->product->id)->toBe($product->id)
        //->and(Cart::items())->toBeEmpty() // cart no longer cleared
        ->and($order->customer->id)->toBe($customer->id);

    Cart::createOrder($customer);

    expect(TestOrder::count())->toBe(1)
        ->and($order->items()->count())->toBe(1)
        ->and($order->items()->first()->product->id)->toBe($product->id)
        ->and($order->customer->id)->toBe($customer->id);
});
