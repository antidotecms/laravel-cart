<?php

it('will show potential discounts in the cart', function () {

    \Antidote\LaravelCartFilament\CartPanelPlugin::set('tax_rate', 0);

    $customer = \Antidote\LaravelCart\Models\Customer::factory()->create();

    $product = \Antidote\LaravelCart\Tests\Fixtures\App\Models\Products\TestProduct::factory()->asSimpleProduct([
        'price' => 1000
    ])->create();

    $cart = app(\Antidote\LaravelCart\Domain\Cart::class);

    $cart->add($product);

    $adjustment = \Antidote\LaravelCart\Models\Adjustment::factory()->create([
        'name' => '10% for all orders',
        'class' => \Antidote\LaravelCart\Tests\Fixtures\App\Models\Adjustments\DiscountAdjustmentCalculation::class,
        'parameters' => [
            'type' => 'percentage', //or fixed
            'rate' => 10
        ]
    ]);

    expect($cart->getTotal())->toBe(900);
})
    ->coversClass(\Antidote\LaravelCart\Models\Adjustment::class);
