<?php

namespace Antidote\LaravelCartFilament\Resources\OrderResource\Pages;

use Antidote\LaravelCartFilament\Resources\OrderResource;
use Filament\Resources\Pages\CreateRecord;

class CreateOrder extends CreateRecord
{
    public static function getResource(): string
    {
        return config('laravel-cart.filament.order') ?? OrderResource::class;
    }
}
