<?php

namespace Antidote\LaravelCartFilament\Resources\CustomerResource\Pages\Concerns;

use Antidote\LaravelCartFilament\CartPanelPlugin;

trait ConfiguresCustomerResourcePages
{
    public static function getResource(): string
    {
        return CartPanelPlugin::get('resources.customer');
    }
}
