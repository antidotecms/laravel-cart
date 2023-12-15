<?php

use Antidote\LaravelCartFilament\CartPanelPlugin;

it('can get and set a config value', function() {

    $cart_plugin = (new CartPanelPlugin());

    setUpCartPlugin($cart_plugin);

    CartPanelPlugin::set('hello', 'there');
    expect(CartPanelPlugin::get('hello'))->toBe('there');

    CartPanelPlugin::set('deeply.nested.key', 'value');
    expect(CartPanelPlugin::get('deeply.nested.key'))->toBe('value');

})
->coversClass(CartPanelPlugin::class);
