<?php

namespace Antidote\LaravelCartFilament\Resources\AdjustmentResource\Pages;

use Antidote\LaravelCartFilament\Resources\AdjustmentResource\Pages\Concerns\ConfiguresAdjustmentResourcePages;
use Filament\Resources\Pages\CreateRecord;

class CreateAdjustment extends CreateRecord
{
    use ConfiguresAdjustmentResourcePages;

    protected function mutateFormDatabeforeCreate(array $data): array
    {
        if(!isset($data['parameters'])) {
            $data['parameters'] = [];
        }

        return $data;
    }
}
