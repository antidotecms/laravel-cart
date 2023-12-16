<?php

namespace Antidote\LaravelCartFilament\Resources\ProductResource\Pages;

use Antidote\LaravelCartFilament\Resources\ProductResource\Pages\Concerns\ConfiguresProductResourcePages;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditProduct extends EditRecord
{
    use ConfiguresProductResourcePages;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->update($data['product']);

        if($record->productType && $data['product']['product_type_type'] == get_class($record->productType)) {
            //type has not changed, just update
            $record->productType->update($data['productType']);
        } else {
            // it has changed, create a new one
            $product_type = $data['product']['product_type_type']::create($data['productType']);
            $record->productType()->associate($product_type);
            //@todo here we just replace the association leading to unassociated productType. We shoudln't immediately delete these as they may be associated with orders etc - think about an archive strategy
        }

        $record->save();
        return $record;
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        return [
            'product' => [
                'id' => $data['id'],
                'name' => $data['name'],
                'product_type_type' => $data['product_type_type']
            ],
            'productType' => $this->getRecord()->productType->attributesToArray()
        ];
    }


}
