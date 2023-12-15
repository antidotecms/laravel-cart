<?php

namespace Antidote\LaravelCartFilament\Resources\OrderResource\Pages;

use Antidote\LaravelCartFilament\Resources\OrderResource;
use Filament\Resources\Pages\ListRecords;

class ListOrders extends ListRecords
{
    use OrderResource\Pages\Concerns\ConfiguresOrderResourcePages;
}
