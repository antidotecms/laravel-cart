<?php

namespace Antidote\LaravelCartFilament\Resources\CustomerResource\Pages;

use Antidote\LaravelCartFilament\Resources\CustomerResource\Pages\Concerns\ConfiguresCustomerResourcePages;
use Filament\Resources\Pages\EditRecord;

class EditCustomer extends EditRecord
{
    use ConfiguresCustomerResourcePages;
}
