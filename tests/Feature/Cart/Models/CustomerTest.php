<?php

//@todo prevent orders being created if no order items?
it('has orders', function() {

    $customer = \Antidote\LaravelCart\Models\Customer::factory()->create();

    $order = \Antidote\LaravelCart\Models\Order::factory()
        ->for($customer)
        ->create();

    expect($customer->orders->count())->toBe(1);
})
->coversClass(\Antidote\LaravelCart\Models\Customer::class);
