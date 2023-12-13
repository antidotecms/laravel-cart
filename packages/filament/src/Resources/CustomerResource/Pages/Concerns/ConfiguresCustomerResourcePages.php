<?php

namespace Antidote\LaravelCartFilament\Resources\CustomerResource\Pages\Concerns;

use Filament\FilamentManager;

trait ConfiguresCustomerResourcePages
{
    public static function getResource(): string
    {
        /** @var $filamentManager FilamentManager */
        $filamentManager = app('filament');
        $resource = $filamentManager->getPlugin('laravel-cart')->getCustomerResource();
        return $resource;
    }
}
