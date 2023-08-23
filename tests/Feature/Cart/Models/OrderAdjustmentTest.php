<?php

use Antidote\LaravelCart\Tests\Fixtures\App\Models\Products\TestProduct;

it('automatically populates the fillable fields', function () {

    Config::set('laravel-cart.classes.order', \Antidote\LaravelCart\Models\Order::class);
    $test_order_item = new \Antidote\LaravelCart\Models\OrderAdjustment;
    expect($test_order_item->getFillable())->toBe([
        'name',
        'order_id',
        'amount',
        'original_parameters',
        'class',
        'apply_to_subtotal'
    ]);

    class NewOrder extends \Antidote\LaravelCart\Models\Order {
        public function updateStatus() { return null; }
        public function isCompleted() {}
    };
    Config::set('laravel-cart.classes.order', NewOrder::class);
    $new_order_adjustment = new class extends \Antidote\LaravelCart\Models\OrderAdjustment {};
    expect($new_order_adjustment->getFillable())->toBe([
        'name',
        'order_id',
        'amount',
        'original_parameters',
        'class',
        'apply_to_subtotal'
    ]);

})
->coversClass(\Antidote\LaravelCart\Models\OrderAdjustment::class);

it('populates the casts', function () {

    $test_order_adjustment = new \Antidote\LaravelCart\Models\OrderAdjustment();

    expect($test_order_adjustment->getCasts())->toHaveKey('original_parameters');
    expect($test_order_adjustment->getCasts()['original_parameters'])->toBe('array');
})
->coversClass(\Antidote\LaravelCart\Models\OrderAdjustment::class);

it('will add a discount to the order', function () {

    $customer = \Antidote\LaravelCart\Models\Customer::factory()->create();

    $product = TestProduct::factory()->asSimpleProduct([
        'price' => 1000
    ])->create();

    \Antidote\LaravelCart\Facades\Cart::add($product);

    //10 percent discount
    //@todo add validity - so scope it to subtotal amount, items in cart etc
    //@todo add active
    //@todo add field for whether this can be added multiple times
//    $adjustment = \Antidote\LaravelCart\Tests\Fixtures\App\Models\TestAdjustment::create([
//        'name' => '10% for all orders',
//        'class' => \Antidote\LaravelCart\Tests\Fixtures\App\Models\Adjustments\DiscountAdjustmentCalculation::class,
//        'parameters' => [
//            'type' => 'percentage', //or fixed
//            'rate' => 10
//        ]
//    ]);

    //expect($adjustment->calculated_amount)->toBe(-100);
    //expect($discount->amount)->toBe(0); //or raise exception?

    //@todo valid adjustments are only shown in the cart if added to the order - mayeb look at automatically applying these?
    //expect(\Antidote\LaravelCart\Facades\Cart::getTotal())->toBe(1000);

    $order = \Antidote\LaravelCart\Facades\Cart::createOrder($customer);

    expect($order->subtotal)->toBe(1000);

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

//    $order_adjustment->adjustment->associate($adjustment);
//    $order->save();

    //$order->addAdjustment($adjustment);

    //expect(\Antidote\LaravelCart\Facades\Cart::getTotal())->toBe(900);

    //expect($discount->amount)->toBe(100);
    expect($order->adjustments->sum('amount'))->toBe(-100);
    expect($order->subtotal)->toBe(1000);
    expect($order->getAdjustmentTotal(true))->toBe(-100);
    expect($order->getAdjustmentTotal(false))->toBe(0);
    expect($order->total)->toBe(1080); // 1000 with 10% off plus tax at 20%

})
->coversClass(\Antidote\LaravelCart\Models\OrderAdjustment::class);

it('will remove a discount if there are no order items', function () {

    $customer = \Antidote\LaravelCart\Models\Customer::factory()->create();

    $product = TestProduct::factory()->asSimpleProduct([
        'price' => 1000
    ])->create();

    \Antidote\LaravelCart\Facades\Cart::add($product);

    $order = \Antidote\LaravelCart\Facades\Cart::createOrder($customer);

    $order_adjustment = \Antidote\LaravelCart\Models\OrderAdjustment::create([
        'name' => '10% off',
        'class' => \Antidote\LaravelCart\Tests\Fixtures\App\Models\Adjustments\DiscountAdjustmentCalculation::class,
        'amount' => -100,
        'order_id' => $order->id,
        'original_parameters' => [
            'type' => 'percentage',
            'rate' => 10
        ],
        'apply_to_subtotal' => true
    ]);

    expect($order->adjustments->count())->toBe(1);

    \Antidote\LaravelCart\Facades\Cart::remove($product);

    $order->refresh();

    expect($order->adjustments->count())->toBe(0);
})
->coversClass(\Antidote\LaravelCart\Models\OrderAdjustment::class);

it('belongs to an order', function () {

    $customer = \Antidote\LaravelCart\Models\Customer::factory()->create();

    $product = TestProduct::factory()->asSimpleProduct([
        'price' => 1000
    ])->create();

    \Antidote\LaravelCart\Facades\Cart::add($product);

    $order = \Antidote\LaravelCart\Facades\Cart::createOrder($customer);

    $order_adjustment = \Antidote\LaravelCart\Models\OrderAdjustment::create([
        'name' => '10% off',
        'class' => \Antidote\LaravelCart\Tests\Fixtures\App\Models\Adjustments\DiscountAdjustmentCalculation::class,
        'amount' => -100,
        'order_id' => $order->id,
        'original_parameters' => [
            'type' => 'percentage',
            'rate' => 10
        ],
        'apply_to_subtotal' => true
    ]);

    expect($order_adjustment->order->id)->toBe($order->id);
    expect($order_adjustment->order->customer->id)->toBe($order->customer->id);
})
->coversClass(\Antidote\LaravelCart\Models\OrderAdjustment::class);
