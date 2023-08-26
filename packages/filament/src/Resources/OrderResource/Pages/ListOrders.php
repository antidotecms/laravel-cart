<?php

namespace Antidote\LaravelCartFilament\Resources\OrderResource\Pages;

use Antidote\LaravelCartFilament\Resources\OrderResource;
use Filament\Resources\Pages\ListRecords;

class ListOrders extends ListRecords
{
    public static function getResource(): string
    {
        return config('laravel-cart.filament.order') ?? OrderResource::class;
    }

    protected static ?string $title = 'Orders';
}
