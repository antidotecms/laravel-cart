<?php

namespace Antidote\LaravelCartFilament\Resources\OrderResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use NumberFormatter;

class OrderItemRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $title = 'Order Items';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product_name')
                    ->getStateUsing(fn($record) => $record->getName())
                    ->description(fn($record) => $record->product->getDescription($record->product_data)),
                TextColumn::make('quantity'),
                TextColumn::make('price')
                    ->formatStateUsing(fn($state) => NumberFormatter::create('en_GB', NumberFormatter::CURRENCY)->formatCurrency($state/100, 'GBP')),
                TextColumn::make('cost')
                    ->getStateUsing(fn($record) => $record->getCost())
                    ->formatStateUsing(fn($state) => NumberFormatter::create('en_GB', NumberFormatter::CURRENCY)->formatCurrency($state/100, 'GBP'))
            ]);
    }
}
