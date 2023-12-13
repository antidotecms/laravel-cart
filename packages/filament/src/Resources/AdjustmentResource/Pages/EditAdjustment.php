<?php

namespace Antidote\LaravelCartFilament\Resources\AdjustmentResource\Pages;

use Antidote\LaravelCartFilament\Resources\AdjustmentResource\Pages\Concerns\ConfiguresAdjustmentResourcePages;
use Filament\Resources\Pages\EditRecord;

class EditAdjustment extends EditRecord
{
    use ConfiguresAdjustmentResourcePages;
}
