<?php

beforeAll(function() {

    if(!class_exists(InlineTestAdjustmentClass::class)) {
        class InlineTestAdjustment extends \Illuminate\Database\Eloquent\Model {
            use \Antidote\LaravelCart\Concerns\ConfiguresAdjustment;

            public function calculatedAmount(): int
            {
                return 100;
            }

            public function isValid(): bool
            {
                return true;
            }
        }
    }

});

it('adds fillable fields', function () {

    $adj = new InlineTestAdjustment();
    expect($adj->getFillable())->toEqualCanonicalizing([
        'name',
        'class',
        'parameters',
        'apply_to_subtotal',
        'is_active'
    ]);
})
->covers(\Antidote\LaravelCart\Concerns\ConfiguresAdjustment::class);

it('adds a cast for parameters', function () {

    $adj = new InlineTestAdjustment();
    expect($adj->getCasts())->toEqualCanonicalizing([
        'id' => 'int',
        'parameters' => 'array'
    ]);
})
->covers(\Antidote\LaravelCart\Concerns\ConfiguresAdjustment::class);

it('will defer the calculated amount to its adjustment class', function() {

    $adjustment = \Antidote\LaravelCart\Models\Adjustment::factory()->create([
        'class' => InlineTestAdjustment::class
    ]);

    expect($adjustment->calculated_amount)->toBe(100);
})
->covers(\Antidote\LaravelCart\Concerns\ConfiguresAdjustment::class);

it('will defer the validity to its adjustment class', function() {

    $adjustment = \Antidote\LaravelCart\Models\Adjustment::factory()->create([
        'class' => InlineTestAdjustment::class
    ]);

    expect($adjustment->is_valid)->toBe(true);
})
->covers(\Antidote\LaravelCart\Concerns\ConfiguresAdjustment::class);
