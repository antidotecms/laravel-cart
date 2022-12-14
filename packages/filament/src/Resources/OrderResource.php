<?php

namespace Antidote\LaravelCartFilament\Resources;

use Antidote\LaravelCartFilament\Resources\OrderResource\Pages\CreateOrder;
use Antidote\LaravelCartFilament\Resources\OrderResource\Pages\EditOrder;
use Antidote\LaravelCartFilament\Resources\OrderResource\Pages\ListOrders;
use Antidote\LaravelCartFilament\Resources\OrderResource\RelationManagers\OrderItemRelationManager;
use Antidote\LaravelCartFilament\Resources\OrderResource\RelationManagers\OrderLogItemRelationManager;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables\Columns\TextColumn;

class OrderResource extends Resource
{
    protected static ?string $navigationIcon = 'heroicon-o-collection';

    protected static ?string $navigationGroup = 'Orders';

    protected static ?string $navigationLabel = 'Orders';

    public static function getModel(): string
    {
        return (string) getClassNameFor('order');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('id')
                    ->disabled(),
                Select::make('customer')
                    ->relationship('customer', 'name'),
                Fieldset::make('Totals')
                    ->columns(1)
                    ->schema([
                    TextInput::make('order_subtotal')
                        ->afterStateHydrated(fn($component, $record) => $component->state($record->getSubtotal()))
                        ->disabled(),
                    TextInput::make('tax')
                        ->afterStateHydrated(fn($component, $record) => $component->state($record->tax))
                        ->disabled(),
                    TextInput::make('order_total')
                        ->afterStateHydrated(fn($component, $record) => $component->state($record->total))
                        ->disabled(),
                ]),
                TextInput::make('status')

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id'),
                TextColumn::make('order_total')
                    ->getStateUsing(fn($record) => $record->total),
                TextColumn::make('customer.name')
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOrders::route('/'),
            'create' => CreateOrder::route('/create'),
            'edit' => EditOrder::route('/{record}/edit'),
        ];
    }

    public static function getRelations(): array
    {
        return [
            OrderItemRelationManager::class,
            OrderLogItemRelationManager::class
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
