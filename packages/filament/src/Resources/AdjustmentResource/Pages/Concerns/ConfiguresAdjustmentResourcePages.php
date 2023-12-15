<?php

namespace Antidote\LaravelCartFilament\Resources\AdjustmentResource\Pages\Concerns;

use Antidote\LaravelCartFilament\CartPanelPlugin;

trait ConfiguresAdjustmentResourcePages
{
    public static function getResource(): string
    {
        return CartPanelPlugin::get('resources.adjustment');
    }
}
