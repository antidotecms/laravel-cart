<?php

namespace Antidote\LaravelCartFilament\Resources\OrderResource\Pages;

use Filament\Resources\Pages\CreateRecord;

class CreateOrder extends CreateRecord
{
    public static function getResource(): string
    {
        return config('laravel-cart.filament.order');
    }
}
