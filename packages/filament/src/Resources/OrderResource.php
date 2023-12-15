<?php

namespace Antidote\LaravelCartFilament\Resources;

use Antidote\LaravelCart\Models\Order;
use Antidote\LaravelCartFilament\CartPanelPlugin;
use Antidote\LaravelCartFilament\Resources\OrderResource\Pages\CreateOrder;
use Antidote\LaravelCartFilament\Resources\OrderResource\Pages\EditOrder;
use Antidote\LaravelCartFilament\Resources\OrderResource\Pages\ListOrders;
use Antidote\LaravelCartFilament\Resources\OrderResource\RelationManagers\OrderItemRelationManager;
use Antidote\LaravelCartFilament\Resources\OrderResource\RelationManagers\OrderLogItemRelationManager;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use NumberFormatter;

class OrderResource extends Resource
{
    protected static ?string $navigationIcon = 'heroicon-o-circle-stack';

    protected static ?string $navigationGroup = 'Orders';

    protected static ?string $navigationLabel = 'Orders';

    public static function getModel(): string
    {
        return CartPanelPlugin::get('models.order');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('id')
                    ->disabled()
                    ->dehydrated(false),
                Select::make('customer')
                    ->relationship('customer', 'name')
                    ->disabled()
                    ->dehydrated(false),
                Fieldset::make('Totals')
                    ->columns(1)
                    ->schema([
                    TextInput::make('order_subtotal')
                        /** @var Order $record */
                        ->afterStateHydrated(fn($component, $record) => static::formatCurrency($component, $record->subtotal))                        //->formatStateUsing(fn($state) => NumberFormatter::create('en_GB', NumberFormatter::CURRENCY)->formatCurrency($state/100, 'GBP'))
                        ->disabled()
                        ->dehydrated(false),
                    TextInput::make('tax')
                        /** @var Order $record */
                        ->afterStateHydrated(fn($component, $record) => static::formatCurrency($component, $record->tax))
                        ->disabled()
                        ->dehydrated(false),
                    TextInput::make('order_total')
                        /** @var Order $record */
                        ->afterStateHydrated(fn($component, $record) => static::formatCurrency($component, $record->total))
                        ->disabled()
                        ->dehydrated(false),
                ]),
                TextInput::make('status')
                    ->disabled()
                    ->dehydrated(false)

            ]);
    }

    private static function formatCurrency($component, $number)
    {
        return $component->state(NumberFormatter::create('en_GB', NumberFormatter::CURRENCY)->formatCurrency($number/100, 'GBP'));
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id'),
                TextColumn::make('order_total')
                    ->getStateUsing(fn($record) => $record->total)
                    ->formatStateUsing(fn($state) => NumberFormatter::create('en_GB', NumberFormatter::CURRENCY)->formatCurrency($state/100, 'GBP')),
                TextColumn::make('status'),
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
