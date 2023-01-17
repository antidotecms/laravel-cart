<?php

namespace Antidote\LaravelCartFilament\Resources\CustomerResource\Pages;

use Filament\Resources\Pages\CreateRecord;

class CreateCustomer extends CreateRecord
{
    public static function getResource(): string
    {
        return config('laravel-cart.filament.customer');
    }
}
