<?php

it('will return those adjustments which should be applied to the subtotal', function() {

    expect(\Antidote\LaravelCart\Models\Adjustment::all()->appliedToSubtotal()->count())->toBe(0);

    \Antidote\LaravelCart\Models\Adjustment::factory()->create([
        'name' => 'Discount',
        'class' => \Antidote\LaravelCart\Tests\Fixtures\App\Models\Adjustments\SimpleAdjustmentCalculation::class,
        'parameters' => [],
        'apply_to_subtotal' => true
    ]);

    expect(\Antidote\LaravelCart\Models\Adjustment::all()->appliedToSubtotal()->count())->toBe(1);
});

it('will return those adjustments which should be applied to the total', function() {

    expect(\Antidote\LaravelCart\Models\Adjustment::all()->appliedToTotal()->count())->toBe(0);

    \Antidote\LaravelCart\Models\Adjustment::factory()->create([
        'name' => 'Discount',
        'class' => \Antidote\LaravelCart\Tests\Fixtures\App\Models\Adjustments\SimpleAdjustmentCalculation::class,
        'parameters' => [],
        'apply_to_subtotal' => false
    ]);

    expect(\Antidote\LaravelCart\Models\Adjustment::all()->appliedToTotal()->count())->toBe(1);

});

it('will return those adjustments which are valid', function() {

    expect(\Antidote\LaravelCart\Models\Adjustment::all()->valid()->count())->toBe(0);

    \Antidote\LaravelCart\Models\Adjustment::factory()->create([
        'name' => 'Discount',
        'class' => \Antidote\LaravelCart\Tests\Fixtures\App\Models\Adjustments\SimpleAdjustmentCalculation::class,
        'parameters' => [],
        'is_valid' => true
    ]);

    expect(\Antidote\LaravelCart\Models\Adjustment::all()->valid()->count())->toBe(1);

});

it('will return those adjustments which are active', function() {

    expect(\Antidote\LaravelCart\Models\Adjustment::all()->active()->count())->toBe(0);

    \Antidote\LaravelCart\Models\Adjustment::factory()->create([
        'name' => 'Discount',
        'class' => \Antidote\LaravelCart\Tests\Fixtures\App\Models\Adjustments\SimpleAdjustmentCalculation::class,
        'parameters' => [],
        'is_active' => true
    ]);

    expect(\Antidote\LaravelCart\Models\Adjustment::all()->active()->count())->toBe(1);

});
