<?php

namespace Antidote\LaravelCartFilament\Resources\AdjustmentResource\Pages;

use Filament\Resources\Pages\ListRecords;

class ListAdjustments extends ListRecords
{
    public static function getResource(): string
    {
        return config('laravel-cart.filament.adjustment');
    }
}
