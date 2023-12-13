<?php

namespace Antidote\LaravelCartFilament\Resources\OrderResource\Pages\Concerns;

use Filament\FilamentManager;

trait ConfiguresOrderResourcePages
{
    public static function getResource(): string
    {
        /** @var $filamentManager FilamentManager */
        $filamentManager = app('filament');
        $resource = $filamentManager->getPlugin('laravel-cart')->getOrderResource();
        return $resource;
    }
}
