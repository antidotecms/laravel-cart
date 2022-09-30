<?php

namespace Antidote\LaravelCartFilament\Resources\CustomerResource\Pages;

use Antidote\LaravelCartFilament\Resources\CustomerResource;
use Filament\Resources\Pages\ListRecords;

class ListCustomers extends ListRecords
{
    protected static string $resource = CustomerResource::class;
}
