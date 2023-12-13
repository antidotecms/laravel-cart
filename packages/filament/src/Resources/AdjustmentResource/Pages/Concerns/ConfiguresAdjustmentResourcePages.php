<?php

namespace Antidote\LaravelCartFilament\Resources\AdjustmentResource\Pages\Concerns;

use Filament\FilamentManager;

trait ConfiguresAdjustmentResourcePages
{
    public static function getResource(): string
    {
        /** @var $filamentManager FilamentManager */
        $filamentManager = app('filament');
        $resource = $filamentManager->getPlugin('laravel-cart')->getAdjustmentResource();
        return $resource;
    }
}
