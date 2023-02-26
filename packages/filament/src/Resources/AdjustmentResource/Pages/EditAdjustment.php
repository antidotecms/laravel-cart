<?php

namespace Antidote\LaravelCartFilament\Resources\AdjustmentResource\Pages;

use Filament\Resources\Pages\EditRecord;

class EditAdjustment extends EditRecord
{
    public static function getResource(): string
    {
        return config('laravel-cart.filament.adjustment');
    }
}
