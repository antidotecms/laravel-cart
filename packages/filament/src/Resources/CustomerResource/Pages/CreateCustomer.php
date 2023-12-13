<?php

namespace Antidote\LaravelCartFilament\Resources\CustomerResource\Pages;

use Antidote\LaravelCartFilament\Resources\CustomerResource\Pages\Concerns\ConfiguresCustomerResourcePages;
use Filament\Resources\Pages\CreateRecord;

class CreateCustomer extends CreateRecord
{
    use ConfiguresCustomerResourcePages;
}
