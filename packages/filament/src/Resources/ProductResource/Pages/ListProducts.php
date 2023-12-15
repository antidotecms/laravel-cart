<?php

namespace Antidote\LaravelCartFilament\Resources\ProductResource\Pages;

use Antidote\LaravelCartFilament\Resources\ProductResource\Pages\Concerns\ConfiguresProductResourcePages;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProducts extends ListRecords
{
    use ConfiguresProductResourcePages;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
        ];
    }
}
