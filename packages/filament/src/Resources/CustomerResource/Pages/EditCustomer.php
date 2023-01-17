<?php

namespace Antidote\LaravelCartFilament\Resources\CustomerResource\Pages;

use Filament\Resources\Pages\EditRecord;

class EditCustomer extends EditRecord
{
    public static function getResource(): string
    {
        return config('laravel-cart.filament.customer');
    }
}
