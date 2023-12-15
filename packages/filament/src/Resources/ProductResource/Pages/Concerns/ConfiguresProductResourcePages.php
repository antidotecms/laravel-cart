<?php

namespace Antidote\LaravelCartFilament\Resources\ProductResource\Pages\Concerns;

use Antidote\LaravelCartFilament\CartPanelPlugin;

trait ConfiguresProductResourcePages
{
    public static function getResource(): string
    {
        return CartPanelPlugin::get('resources.product');
    }
}
