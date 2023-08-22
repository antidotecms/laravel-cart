<?php

it('has an event field', function() {

    $model = new \Antidote\LaravelCart\Tests\Fixtures\App\Models\TestStripeOrderLogItem();
    expect($model->getFillable())->toContain('event');
})
->covers(\Antidote\LaravelCartStripe\Concerns\ConfiguresStripeOrderLogItem::class);

it('casts the event property to an array', function () {

    $model = new \Antidote\LaravelCart\Tests\Fixtures\App\Models\TestStripeOrderLogItem();
    expect($model->getCasts())->toHaveKey('event');
    expect($model->getCasts()['event'])->toBe('array');
})
->covers(\Antidote\LaravelCartStripe\Concerns\ConfiguresStripeOrderLogItem::class);
