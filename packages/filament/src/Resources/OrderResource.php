<?php

namespace Antidote\LaravelCartFilament\Resources;

use Antidote\LaravelCartFilament\Resources\OrderResource\Pages\CreateOrder;
use Antidote\LaravelCartFilament\Resources\OrderResource\Pages\EditOrder;
use Antidote\LaravelCartFilament\Resources\OrderResource\Pages\ListOrders;
use Antidote\LaravelCartFilament\Resources\OrderResource\RelationManagers\OrderItemRelationManager;
use Antidote\LaravelCartFilament\Resources\OrderResource\RelationManagers\OrderLogItemRelationManager;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Str;

class OrderResource extends Resource
{
    protected static ?string $navigationIcon = 'heroicon-o-collection';

    protected static ?string $navigationGroup = 'Orders';

    public static function getModel(): string
    {
        return (string) Str::of(class_basename(getClassNameFor('order')))
                ->beforeLast('Resource')
                ->prepend('App\\Models\\');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('id')
                    ->disabled(),
                TextInput::make('order_total')
                    ->afterStateHydrated(fn($component, $record) => $component->state($record->getTotal()))
                    ->disabled()

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id'),
                TextColumn::make('order_total')
                    ->getStateUsing(fn($record) => $record->getTotal()),
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
}
