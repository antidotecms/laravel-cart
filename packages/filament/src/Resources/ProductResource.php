<?php

namespace Antidote\LaravelCartFilament\Resources;

use Antidote\LaravelCartFilament\CartPanelPlugin;
use Antidote\LaravelCartFilament\Resources\ProductResource\Pages\CreateProduct;
use Antidote\LaravelCartFilament\Resources\ProductResource\Pages\EditProduct;
use Antidote\LaravelCartFilament\Resources\ProductResource\Pages\ListProducts;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

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
                Group::make()
                    ->statePath('product')
                    ->schema([
                        TextInput::make('id')
                            ->disabled()
                            ->dehydrated(false),
                        TextInput::make('name')
                            ->required(),
                        Select::make('product_type_type')
                            ->label('Type')
                            ->required()
                            ->live()
                            ->options(CartPanelPlugin::get('productTypes')),
                    ]),

                Fieldset::make()
                    ->schema(function($get) {
                        if($get('product.product_type_type')) {
                            return [Group::make()
                                ->statePath('productType')
                                ->schema($get('product.product_type_type')::form())];
                        }

                        return [];
                    })
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->bulkActions([
                DeleteBulkAction::make()
            ])
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('product_type_type')
                    ->label('Type')
                    ->formatStateUsing(fn($state) => CartPanelPlugin::get('productTypes')[$state])
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProducts::route('/'),
            'create' => CreateProduct::route('/create'),
            'edit' => EditProduct::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('productType');
    }
}
