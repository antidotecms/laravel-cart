<?php

namespace Antidote\LaravelCartFilament\Resources\ProductResource\Pages;

use Antidote\LaravelCartFilament\Resources\ProductResource\Pages\Concerns\ConfiguresProductResourcePages;
use Filament\Resources\Pages\ListRecords;

class ListProducts extends ListRecords
{
    use ConfiguresProductResourcePages;
}
