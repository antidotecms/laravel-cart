<?php

namespace Antidote\LaravelCartFilament\Resources\CustomerResource\Pages;

use Filament\Resources\Pages\ListRecords;

class ListCustomers extends ListRecords
{
    public static function getResource(): string
    {
        return config('laravel-cart.filament.customer');
    }
}
