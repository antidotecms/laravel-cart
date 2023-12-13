<?php

namespace Antidote\LaravelCartFilament\Resources\AdjustmentResource\Pages;

use Antidote\LaravelCartFilament\Resources\AdjustmentResource\Pages\Concerns\ConfiguresAdjustmentResourcePages;
use Filament\Resources\Pages\ListRecords;

class ListAdjustments extends ListRecords
{
    use ConfiguresAdjustmentResourcePages;
}
