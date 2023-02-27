<?php

namespace Antidote\LaravelCartFilament\Resources\AdjustmentResource\Pages;

use Filament\Resources\Pages\CreateRecord;

class CreateAdjustment extends CreateRecord
{
    public static function getResource(): string
    {
        return config('laravel-cart.filament.adjustment');
    }

    protected function mutateFormDatabeforeCreate(array $data): array
    {
        if(!isset($data['parameters'])) {
            $data['parameters'] = [];
        }

        return $data;
    }
}
