<?php

use Antidote\LaravelCart\Facades\Cart;
use Antidote\LaravelCart\Tests\Fixtures\App\Models\Products\TestProduct;

beforeEach(function() {
   $this->cart = app(\Antidote\LaravelCart\Domain\Cart::class);
});

it('will create an order', function() {

    $product = TestProduct::factory()->asSimpleProduct()->create();

    $this->cart->add($product);

    $customer = \Antidote\LaravelCart\Models\Customer::factory()->create();

    $this->cart->createOrder($customer);

    $order = \Antidote\LaravelCart\Models\Order::first();

    expect(\Antidote\LaravelCart\Models\Order::count())->toBe(1)
        ->and($order->items()->count())->toBe(1)
        ->and($order->items()->first()->id)->toBe($product->id)
        //->and($this->cart->items())->toBeEmpty() // cart no longer cleared
        ->and($order->customer->id)->toBe($customer->id);
})
->coversClass(\Antidote\LaravelCart\Models\Order::class);

it('will create an order with discount', function () {

    Config::set('laravel-cart.classes.order_adjustment', \Antidote\LaravelCart\Models\OrderAdjustment::class);

    $product = TestProduct::factory()->asSimpleProduct([
        'price' => '1000'
    ])->create();

    $this->cart->add($product);

    $customer = \Antidote\LaravelCart\Models\Customer::factory()->create();

//    $adjustment = \Antidote\LaravelCart\Tests\Fixtures\App\Models\TestAdjustment::create([
//        'name' => '10% for all orders',
//        'class' => \Antidote\LaravelCart\Tests\Fixtures\App\Models\Adjustments\DiscountAdjustmentCalculation::class,
//        'parameters' => [
//            'type' => 'percentage', //or fixed
//            'rate' => 10
//        ]
//    ]);


    $order = $this->cart->createOrder($customer);



    $order_adjustment = \Antidote\LaravelCart\Models\OrderAdjustment::create([
        'name' => '10% off',
        'class' => \Antidote\LaravelCart\Tests\Fixtures\App\Models\Adjustments\DiscountAdjustmentCalculation::class,
        //'test_adjustment_id' => $adjustment->id,
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
    //expect($order->getSubtotal())->toBe(900);

    //subtotal with discount should be 900. Given tax rate of 20%, total should be 900 * 1.2 = 1080
    expect($order->total)->toBe(1080);

})
->coversClass(\Antidote\LaravelCart\Models\Order::class);

test('a customer has an order', function () {

    $product = TestProduct::factory()->asSimpleProduct()->create();

    $this->cart->add($product);

    $customer = \Antidote\LaravelCart\Models\Customer::factory()->create();

    $this->cart->createOrder($customer);

    expect($customer->orders()->count())->toBe(1);

})
->coversClass(\Antidote\LaravelCart\Models\Order::class);

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

    $customer = \Antidote\LaravelCart\Models\Customer::factory()->create();

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
->coversClass(\Antidote\LaravelCart\Models\Order::class);

//@todo this really tests trait - maybe move test?
it('automatically populates the fillable fields', function () {

    $test_order = new \Antidote\LaravelCart\Models\Order;
    expect($test_order->getFillable())->toBe([
        'customer_id'
    ]);

    class NewCustomer extends \Antidote\LaravelCart\Models\Customer {}
    Config::set('laravel-cart.classes.customer', NewCustomer::class);
    $new_order = new class extends \Antidote\LaravelCart\Models\Order {
        public function updateStatus() { return null; }
        public function isCompleted() {}
    };
    expect($new_order->getFillable())->toBe([
       'new_customer_id'
    ]);

})
->coversClass(\Antidote\LaravelCart\Models\Order::class);

it('has a payment method', function () {

    $this->markTestSkipped('to remove Payment class?');
    $order = \Antidote\LaravelCart\Models\Order::factory()->create();

    expect(get_class($order->payment))->toBe(\Antidote\LaravelCart\Tests\Fixtures\App\Models\TestPayment::class);
})
->coversClass(\Antidote\LaravelCart\Models\Order::class);

it('will get the subtotal', function () {

    $simple_product = TestProduct::factory()->asSimpleProduct([
        'price' => 1999
    ])->create([
        'name' => 'A Simple Product'
    ]);

    $customer = \Antidote\LaravelCart\Models\Customer::factory()->create();

    $order = \Antidote\LaravelCart\Models\Order::factory()->forCustomer($customer)->create();

    \Antidote\LaravelCart\Models\OrderItem::factory()->withProduct($simple_product)->forOrder($order)->create();

    expect($order->subtotal)->toBe(1999);

    \Antidote\LaravelCart\Models\OrderItem::factory()->withProduct($simple_product)->forOrder($order)->create();

    expect($order->subtotal)->toBe(3998);

})
->coversClass(\Antidote\LaravelCart\Models\Order::class);

it('will get the subtotal 2', function () {

    $simple_product = TestProduct::factory()->asSimpleProduct([
        'price' => 1999
    ])->create([
        'name' => 'A Simple Product'
    ]);

    $customer = \Antidote\LaravelCart\Models\Customer::factory()->create();

    $order = \Antidote\LaravelCart\Models\Order::factory()->forCustomer($customer)->create();

    \Antidote\LaravelCart\Models\OrderItem::factory()
        ->withProduct($simple_product)
        ->withQuantity(1)
        ->forOrder($order)
        ->create();

    expect($order->subtotal)->toBe(1999);

    \Antidote\LaravelCart\Models\OrderItem::factory()
        ->withProduct($simple_product)
        ->withQuantity(1)
        ->forOrder($order)
        ->create();

    expect($order->subtotal)->toBe(3998);

})
->coversClass(\Antidote\LaravelCart\Models\Order::class);

it('will get the total with VAT', function () {

    $simple_product = TestProduct::factory()->asSimpleProduct([
        'price' => 2000
    ])->create([
        'name' => 'A Simple Product'
    ]);

    $customer = \Antidote\LaravelCart\Models\Customer::factory()->create();

    $order = \Antidote\LaravelCart\Models\Order::factory()->forCustomer($customer)->create();

    \Antidote\LaravelCart\Models\OrderItem::factory()->withProduct($simple_product)->forOrder($order)->create();

    expect($order->total)->toBe(2400);
})
->coversClass(\Antidote\LaravelCart\Models\Order::class);

it('will not create an order if an active order already exists', function () {

    $product = TestProduct::factory()->asSimpleProduct()->create();

    $this->cart->add($product);

    $customer = \Antidote\LaravelCart\Models\Customer::factory()->create();

    $this->cart->createOrder($customer);

    $order = \Antidote\LaravelCart\Models\Order::first();

    expect(\Antidote\LaravelCart\Models\Order::count())->toBe(1)
        ->and($order->items()->count())->toBe(1)
        ->and($order->items()->first()->product->id)->toBe($product->id)
        //->and($this->cart->items())->toBeEmpty() // cart no longer cleared
        ->and($order->customer->id)->toBe($customer->id);

    $this->cart->createOrder($customer);

    expect(\Antidote\LaravelCart\Models\Order::count())->toBe(1)
        ->and($order->items()->count())->toBe(1)
        ->and($order->items()->first()->product->id)->toBe($product->id)
        ->and($order->customer->id)->toBe($customer->id);
})
->coversClass(\Antidote\LaravelCart\Models\Order::class);

it('has log items', function () {

    $order = \Antidote\LaravelCart\Models\Order::factory()
        ->for(\Antidote\LaravelCart\Models\Customer::factory())
        ->create();

    //@todo create factory
    $order_log_item = \Antidote\LaravelCart\Models\OrderLogItem::create([
        'message' => 'a log item',
        'order_id' => $order->id
    ]);

    expect($order->logItems()->count())->toBe(1);
})
->coversClass(\Antidote\LaravelCart\Models\Order::class);

it('will log an item', function () {

    $order = \Antidote\LaravelCart\Models\Order::factory()
        ->for(\Antidote\LaravelCart\Models\Customer::factory())
        ->create();

    $order->log('this is a note');

    expect($order->logItems()->count())->toBe(1);

    expect($order->logItems->first()->message)->toBe('this is a note');

})
->coversClass(\Antidote\LaravelCart\Models\Order::class);

it('has data', function () {

    $order = \Antidote\LaravelCart\Models\Order::factory()
        ->for(\Antidote\LaravelCart\Models\Customer::factory())
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
->coversClass(\Antidote\LaravelCart\Models\Order::class);

it('will set data', function () {

    $order = \Antidote\LaravelCart\Models\Order::factory()
        ->for(\Antidote\LaravelCart\Models\Customer::factory())
        ->create();

    $order->setData('some_data', 'some_value');

    expect($order->data()->count())->toBe(1);
    expect($order->data->first()->key)->toBe('some_data');
    expect($order->data->first()->value)->toBe('"some_value"'); //json encoded so need quotes
})
->coversClass(\Antidote\LaravelCart\Models\Order::class);

it('will get data', function () {

    $order = \Antidote\LaravelCart\Models\Order::factory()
        ->for(\Antidote\LaravelCart\Models\Customer::factory())
        ->create();

    $order->setData('some_data', 'some_value');

    expect($order->getData('some_data'))->toBe('some_value');
})
->coversClass(\Antidote\LaravelCart\Models\Order::class);

it('will set data as array', function () {

    $order = \Antidote\LaravelCart\Models\Order::factory()
        ->for(\Antidote\LaravelCart\Models\Customer::factory())
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
->coversClass(\Antidote\LaravelCart\Models\Order::class);

it('will get data as array', function () {

    $order = \Antidote\LaravelCart\Models\Order::factory()
        ->for(\Antidote\LaravelCart\Models\Customer::factory())
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
->coversClass(\Antidote\LaravelCart\Models\Order::class);

it('will return null of the data does not exist', function () {

    $order = \Antidote\LaravelCart\Models\Order::factory()
        ->for(\Antidote\LaravelCart\Models\Customer::factory())
        ->create();

    expect($order->getData('non_existant'))->toBeNull();
})
->coversClass(\Antidote\LaravelCart\Models\Order::class);

it('will overwrite data with the same key', function () {

    $order = \Antidote\LaravelCart\Models\Order::factory()->create();

    $order->setData('some_data', 'a value');

    expect($order->data()->where('key', 'some_data')->count())->toBe(1);
    expect($order->getData('some_data'))->toBe('a value');

    $order->setData('some_data', 'a new value');

    expect($order->data()->where('key', 'some_data')->count())->toBe(1);
    expect($order->getData('some_data'))->toBe('a new value');
})
->coversClass(\Antidote\LaravelCart\Models\Order::class);

it('will throw an exception when attempting to update status', function () {

    $order = \Antidote\LaravelCart\Models\Order::factory()->create();
    $order->updateStatus();
})
->coversClass(\Antidote\LaravelCart\Models\Order::class)
->expectExceptionMessage('Order should be overriden and implement updateStatus');

it('will throw an exception when attempting to determine if the order is completed', function () {

    $order = \Antidote\LaravelCart\Models\Order::factory()->create();
    $order->isCompleted();
})
->coversClass(\Antidote\LaravelCart\Models\Order::class)
->expectExceptionMessage('Order should be overriden and implement isCompleted');
