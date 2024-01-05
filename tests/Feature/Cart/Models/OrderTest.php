<?php

use Antidote\LaravelCart\Facades\Cart;
use Antidote\LaravelCart\Models\Customer;
use Antidote\LaravelCart\Models\Order;
use Antidote\LaravelCart\Models\OrderAdjustment;
use Antidote\LaravelCart\Models\OrderItem;
use Antidote\LaravelCart\Models\OrderLogItem;
use Antidote\LaravelCart\Tests\Fixtures\App\Models\Adjustments\DiscountAdjustmentCalculation;
use Antidote\LaravelCart\Tests\Fixtures\App\Models\Products\TestProduct;

beforeEach(function() {
   $this->cart = app(\Antidote\LaravelCart\Domain\Cart::class);
});

it('will create an order', function() {

    $product = TestProduct::factory()->asSimpleProduct()->create();

    $this->cart->add($product);

    $customer = Customer::factory()->create();

    $this->cart->createOrder($customer);

    $order = Order::first();

    expect(Order::count())->toBe(1)
        ->and($order->items()->count())->toBe(1)
        ->and($order->items()->first()->id)->toBe($product->id)
        ->and($order->customer->id)->toBe($customer->id);
})
->coversClass(Order::class);

it('will create an order with discount', function () {

    $product = TestProduct::factory()->asSimpleProduct([
        'price' => '1000'
    ])->create();

    $this->cart->add($product);

    $customer = Customer::factory()->create();

    $order = $this->cart->createOrder($customer);

    $order_adjustment = OrderAdjustment::create([
        'name' => '10% off',
        'class' => DiscountAdjustmentCalculation::class,
        'amount' => -100,
        'order_id' => $order->id,
        'original_parameters' => [
            'type' => 'percentage',
            'rate' => 10
        ],
        'apply_to_subtotal' => true
    ]);


    expect($order->items()->count())->toBe(1);
    expect($order->adjustments()->count())->toBe(1);

    //subtotal with discount should be 900. Given tax rate of 20%, total should be 900 * 1.2 = 1080
    expect($order->total)->toBe(1080);

})
->coversClass(Order::class);

test('a customer has an order', function () {

    $product = TestProduct::factory()->asSimpleProduct()->create();

    $this->cart->add($product);

    $customer = Customer::factory()->create();

    $this->cart->createOrder($customer);

    expect($customer->orders()->count())->toBe(1);

})
->coversClass(Order::class);

it('will detail an order item', function () {

    $simple_product = TestProduct::factory()->asSimpleProduct([
        'price' => 1999
    ])->create([
        'name' => 'A Simple Product'
    ]);

    $complex_product = TestProduct::factory()->asComplexProduct()->create([
        'name' => 'A Complex Product'
    ]);

    $this->cart->add($simple_product ,2);

    $product_data = [
        'width' => 10,
        'height' => 10
    ];

    $this->cart->add($complex_product, 1, $product_data);

    $customer = Customer::factory()->create();

    $order = $this->cart->createOrder($customer);

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

})
->coversClass(Order::class);

it('has a payment method', function () {

    $this->markTestSkipped('to remove Payment class?');
    $order = Order::factory()->create();

    expect(get_class($order->payment))->toBe(\Antidote\LaravelCart\Tests\Fixtures\App\Models\TestPayment::class);
})
->coversClass(Order::class);

it('will get the subtotal', function () {

    $simple_product = TestProduct::factory()->asSimpleProduct([
        'price' => 1999
    ])->create([
        'name' => 'A Simple Product'
    ]);

    $customer = Customer::factory()->create();

    $order = Order::factory()->forCustomer($customer)->create();

    OrderItem::factory()->withProduct($simple_product)->forOrder($order)->create();

    expect($order->subtotal)->toBe(1999);

    OrderItem::factory()->withProduct($simple_product)->forOrder($order)->create();

    expect($order->subtotal)->toBe(3998);

})
->coversClass(Order::class);

it('will get the subtotal 2', function () {

    $simple_product = TestProduct::factory()->asSimpleProduct([
        'price' => 1999
    ])->create([
        'name' => 'A Simple Product'
    ]);

    $customer = Customer::factory()->create();

    $order = Order::factory()->forCustomer($customer)->create();

    OrderItem::factory()
        ->withProduct($simple_product)
        ->withQuantity(1)
        ->forOrder($order)
        ->create();

    expect($order->subtotal)->toBe(1999);

    OrderItem::factory()
        ->withProduct($simple_product)
        ->withQuantity(1)
        ->forOrder($order)
        ->create();

    expect($order->subtotal)->toBe(3998);

})
->coversClass(Order::class);

it('will get the total with VAT', function () {

    $simple_product = TestProduct::factory()->asSimpleProduct([
        'price' => 2000
    ])->create([
        'name' => 'A Simple Product'
    ]);

    $customer = Customer::factory()->create();

    $order = Order::factory()->forCustomer($customer)->create();

    OrderItem::factory()->withProduct($simple_product)->forOrder($order)->create();

    expect($order->total)->toBe(2400);
})
->coversClass(Order::class);

it('will not create an order if an active order already exists', function () {

    $product = TestProduct::factory()->asSimpleProduct()->create();

    $this->cart->add($product);

    $customer = Customer::factory()->create();

    $this->cart->createOrder($customer);

    $order = Order::first();

    expect(Order::count())->toBe(1)
        ->and($order->items()->count())->toBe(1)
        ->and($order->items()->first()->product->id)->toBe($product->id)
        ->and($order->customer->id)->toBe($customer->id);

    $this->cart->createOrder($customer);

    expect(Order::count())->toBe(1)
        ->and($order->items()->count())->toBe(1)
        ->and($order->items()->first()->product->id)->toBe($product->id)
        ->and($order->customer->id)->toBe($customer->id);
})
->coversClass(Order::class);

it('has log items', function () {

    $order = Order::factory()
        ->for(Customer::factory())
        ->create();

    //@todo create factory
    $order_log_item = OrderLogItem::create([
        'message' => 'a log item',
        'order_id' => $order->id
    ]);

    expect($order->logItems()->count())->toBe(1);
})
->coversClass(Order::class);

it('will log an item', function () {

    $order = Order::factory()
        ->for(Customer::factory())
        ->create();

    $order->log('this is a note');

    expect($order->logItems()->count())->toBe(1);

    expect($order->logItems->first()->message)->toBe('this is a note');

})
->coversClass(Order::class);

it('has data', function () {

    $order = Order::factory()
        ->for(Customer::factory())
        ->create();

    $order_data = \Antidote\LaravelCart\Models\OrderData::factory()
        ->for($order)
        ->create([
            'key' => 'note',
            'value' => 'some notes'
        ]);

    expect($order->data()->count())->toBe(1);
    expect($order->data()->first()->key)->toBe('note');
    expect($order->data()->first()->value)->toBe('some notes');
})
->coversClass(Order::class);

it('will set data', function () {

    $order = Order::factory()
        ->for(Customer::factory())
        ->create();

    $order->setData('some_data', 'some_value');

    expect($order->data()->count())->toBe(1);
    expect($order->data->first()->key)->toBe('some_data');
    expect($order->data->first()->value)->toBe('"some_value"'); //json encoded so need quotes
})
->coversClass(Order::class);

it('will get data', function () {

    $order = Order::factory()
        ->for(Customer::factory())
        ->create();

    $order->setData('some_data', 'some_value');

    expect($order->getData('some_data'))->toBe('some_value');
})
->coversClass(Order::class);

it('will set data as array', function () {

    $order = Order::factory()
        ->for(Customer::factory())
        ->create();

    $order->setData('some_data', [
        'array',
        'of',
        'data'
    ]);

    expect($order->data()->count())->toBe(1);
    expect($order->data->first()->value)->toBe(json_encode([
        'array',
        'of',
        'data'
    ])); //json encoded
})
->coversClass(Order::class);

it('will get data as array', function () {

    $order = Order::factory()
        ->for(Customer::factory())
        ->create();

    $order->setData('some_data', [
        'array',
        'of',
        'data'
    ]);

    expect($order->getData('some_data'))->toBe([
        'array',
        'of',
        'data'
    ]);
})
->coversClass(Order::class);

it('will return null of the data does not exist', function () {

    $order = Order::factory()
        ->for(Customer::factory())
        ->create();

    expect($order->getData('non_existant'))->toBeNull();
})
->coversClass(Order::class);

it('will overwrite data with the same key', function () {

    $order = Order::factory()->create();

    $order->setData('some_data', 'a value');

    expect($order->data()->where('key', 'some_data')->count())->toBe(1);
    expect($order->getData('some_data'))->toBe('a value');

    $order->setData('some_data', 'a new value');

    expect($order->data()->where('key', 'some_data')->count())->toBe(1);
    expect($order->getData('some_data'))->toBe('a new value');
})
->coversClass(Order::class);

it('will throw an exception when attempting to update status', function () {

    $this->markTestSkipped('status obtained via related payment model');
    $order = Order::factory()->create();
    $order->updateStatus();
})
->coversClass(Order::class)
->expectExceptionMessage('Order should be overriden and implement updateStatus');

it('will throw an exception when attempting to determine if the order is completed', function () {

    $this->markTestSkipped('status obtained via related payment model');
    $order = Order::factory()->create();
    $order->isCompleted();
})
->coversClass(Order::class)
->expectExceptionMessage('Order should be overriden and implement isCompleted');
