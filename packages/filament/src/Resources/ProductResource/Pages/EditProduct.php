<?php

namespace Antidote\LaravelCartFilament\Resources\ProductResource\Pages;

use Antidote\LaravelCartFilament\Resources\ProductResource\Pages\Concerns\ConfiguresProductResourcePages;
use Filament\Resources\Pages\EditRecord;

class EditProduct extends EditRecord
{
    use ConfiguresProductResourcePages;
}
