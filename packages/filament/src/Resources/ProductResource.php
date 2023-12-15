<?php

namespace Antidote\LaravelCartFilament\Resources;

use Antidote\LaravelCartFilament\CartPanelPlugin;
use Antidote\LaravelCartFilament\Resources\ProductResource\Pages\CreateProduct;
use Antidote\LaravelCartFilament\Resources\ProductResource\Pages\EditProduct;
use Antidote\LaravelCartFilament\Resources\ProductResource\Pages\ListProducts;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;

class ProductResource extends Resource
{
    protected static ?string $navigationIcon = 'heroicon-o-circle-stack';

    protected static ?string $navigationGroup = 'Orders';

    protected static ?string $navigationLabel = 'Products';

    public static function getModel(): string
    {
        return CartPanelPlugin::get('models.product');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('id')
                    ->disabled()
                    ->dehydrated(false),
                TextInput::make('name')
                    ->required()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProducts::route('/'),
            'create' => CreateProduct::route('/create'),
            'edit' => EditProduct::route('/{record}/edit'),
        ];
    }
}
