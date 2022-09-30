<?php

namespace Antidote\LaravelCartFilament\Resources\OrderResource\Pages;

use Antidote\LaravelCartFilament\Resources\OrderResource;
use Filament\Resources\Pages\EditRecord;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;
}
