<?php

use Antidote\LaravelCart\Models\Adjustment;

it('will return those adjustments which should be applied to the subtotal', function() {

    expect(Adjustment::all()->appliedToSubtotal()->count())->toBe(0);

    Adjustment::factory()->create([
        'name' => 'Discount',
        'class' => \Antidote\LaravelCart\Tests\Fixtures\App\Models\Adjustments\SimpleAdjustmentCalculation::class,
        'parameters' => [],
        'apply_to_subtotal' => true
    ]);

    expect(Adjustment::all()->appliedToSubtotal()->count())->toBe(1);
})
->coversClass(\Antidote\LaravelCart\Collections\AdjustmentCollection::class);

it('will return those adjustments which should be applied to the total', function() {

    expect(Adjustment::all()->appliedToTotal()->count())->toBe(0);

    Adjustment::factory()->create([
        'name' => 'Discount',
        'class' => \Antidote\LaravelCart\Tests\Fixtures\App\Models\Adjustments\SimpleAdjustmentCalculation::class,
        'parameters' => [],
        'apply_to_subtotal' => false
    ]);

    expect(Adjustment::all()->appliedToTotal()->count())->toBe(1);

})
->coversClass(\Antidote\LaravelCart\Collections\AdjustmentCollection::class);

it('will return those adjustments which are valid', function() {

    expect(Adjustment::all()->valid()->count())->toBe(0);

    Adjustment::factory()->create([
        'name' => 'Discount',
        'class' => \Antidote\LaravelCart\Tests\Fixtures\App\Models\Adjustments\SimpleAdjustmentCalculation::class,
        'parameters' => [],
        'is_valid' => true
    ]);

    expect(Adjustment::all()->valid()->count())->toBe(1);

})
->coversClass(\Antidote\LaravelCart\Collections\AdjustmentCollection::class);

it('will return those adjustments which are active', function() {

    expect(Adjustment::all()->active()->count())->toBe(0);

    Adjustment::factory()->create([
        'name' => 'Discount',
        'class' => \Antidote\LaravelCart\Tests\Fixtures\App\Models\Adjustments\SimpleAdjustmentCalculation::class,
        'parameters' => [],
        'is_active' => true
    ]);

    expect(Adjustment::all()->active()->count())->toBe(1);

})
->coversClass(\Antidote\LaravelCart\Collections\AdjustmentCollection::class);
