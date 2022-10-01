<?php

namespace Antidote\LaravelCartFilament\Resources\OrderResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables\Columns\TextColumn;

class OrderItemRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $title = 'Order Items';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product_name')
                    ->getStateUsing(fn($record) => $record->getName())
                ->description(fn($record) => $record->product->getDescription($record->product_data)),
                TextColumn::make('quantity'),
                TextColumn::make('price'),
                TextColumn::make('cost')
                    ->getStateUsing(fn($record) => $record->getCost())
            ]);
    }
}
