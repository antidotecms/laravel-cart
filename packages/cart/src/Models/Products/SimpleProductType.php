<?php

namespace Antidote\LaravelCart\Models\Products;

use Antidote\LaravelCart\Contracts\ProductType;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

class SimpleProductType extends ProductType
{
    protected $fillable = [
        'price'
    ];
    public static function form(): array
    {
        return [
            // @todo add localised currency symbol
            TextInput::make('price')
                ->numeric()
                ->default(0)
        ];
    }

    public function clientForm(): array
    {
        return [
            Select::make('colour')
                ->required()
                ->options([
                    'red',
                    'blue'
                ])
        ];
    }

    public function isValid(?array $product_data = null): bool
    {
        return true;
    }

    public function getPrice(?array $product_data = null): int
    {
        return $this->price;
    }

    public function getDescription(?array $product_data = null): string
    {
        return 'A description';
    }
}
