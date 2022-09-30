<?php

namespace Antidote\LaravelCartFilament\Resources\CustomerResource\Pages;

use Antidote\LaravelCartFilament\Resources\CustomerResource;
use Filament\Resources\Pages\EditRecord;

class EditCustomer extends EditRecord
{
    protected static string $resource = CustomerResource::class;
}
