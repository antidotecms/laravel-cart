<?php

it('will allow overriding of the related resource class', function () {

    $config = app('config');

    $override_class = get_class(new class extends \Antidote\LaravelCartFilament\Resources\OrderResource {});

    expect(\Antidote\LaravelCartFilament\Resources\OrderResource\Pages\CreateOrder::getResource())
        ->toEqual(\Antidote\LaravelCartFilament\Resources\OrderResource::class);

    $config->set('laravel-cart.filament.order', $override_class);

    expect(\Antidote\LaravelCartFilament\Resources\OrderResource\Pages\CreateOrder::getResource())
        ->toEqual($override_class);
})
->coversClass(\Antidote\LaravelCartFilament\Resources\OrderResource\Pages\CreateOrder::class);
