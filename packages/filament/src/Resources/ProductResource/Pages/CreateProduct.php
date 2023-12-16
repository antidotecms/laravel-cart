<?php

namespace Antidote\LaravelCartFilament\Resources\ProductResource\Pages;

use Antidote\LaravelCartFilament\CartPanelPlugin;
use Antidote\LaravelCartFilament\Resources\ProductResource\Pages\Concerns\ConfiguresProductResourcePages;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateProduct extends CreateRecord
{
    use ConfiguresProductResourcePages;

    protected function handleRecordCreation(array $data): Model
    {
        $product_class = CartPanelPlugin::get('models.product');

        $product = $product_class::create($data['product']);

        $product_type = $data['product']['product_type_type']::create($data['productType']);

        $product->productType()->associate($product_type);
        $product->save();

        return $product;
    }
}
