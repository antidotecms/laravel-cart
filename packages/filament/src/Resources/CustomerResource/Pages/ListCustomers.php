<?php

namespace Antidote\LaravelCartFilament\Resources\CustomerResource\Pages;

use Antidote\LaravelCartFilament\Resources\CustomerResource\Pages\Concerns\ConfiguresCustomerResourcePages;
use Filament\Resources\Pages\ListRecords;

class ListCustomers extends ListRecords
{
    use ConfiguresCustomerResourcePages;
}
