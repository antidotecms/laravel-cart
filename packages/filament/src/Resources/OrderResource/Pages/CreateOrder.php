<?php

namespace Antidote\LaravelCartFilament\Resources\OrderResource\Pages;

use Antidote\LaravelCartFilament\Resources\OrderResource;
use Filament\Resources\Pages\CreateRecord;

class CreateOrder extends CreateRecord
{
    use OrderResource\Pages\Concerns\ConfiguresOrderResourcePages;
}
