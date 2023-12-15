<?php

namespace Antidote\LaravelCartFilament\Resources\ProductResource\Pages;

use Antidote\LaravelCartFilament\Resources\ProductResource\Pages\Concerns\ConfiguresProductResourcePages;
use Filament\Resources\Pages\CreateRecord;

class CreateProduct extends CreateRecord
{
    use ConfiguresProductResourcePages;
}
