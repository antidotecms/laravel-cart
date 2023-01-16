<?php

namespace Antidote\LaravelCartFilament\Resources\OrderResource\Pages;

use Filament\Resources\Pages\ListRecords;

class ListOrders extends ListRecords
{
    public static function getResource(): string
    {
        return config('laravel-cart.filament.order');
    }

    protected static ?string $title = 'Orders';
}
