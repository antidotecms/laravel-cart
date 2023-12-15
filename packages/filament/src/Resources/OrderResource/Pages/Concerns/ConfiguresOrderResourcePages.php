<?php

namespace Antidote\LaravelCartFilament\Resources\OrderResource\Pages\Concerns;

use Antidote\LaravelCartFilament\CartPanelPlugin;

trait ConfiguresOrderResourcePages
{
    public static function getResource(): string
    {
        return CartPanelPlugin::get('resources.order');
    }
}
