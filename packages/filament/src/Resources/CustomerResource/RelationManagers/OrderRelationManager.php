<?php

namespace Antidote\LaravelCartFilament\Resources\CustomerResource\RelationManagers;

use Antidote\LaravelCartFilament\Resources\OrderResource;
use Closure;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables\Columns\TextColumn;

class OrderRelationManager extends RelationManager
{
    protected static string $relationship = 'orders';

    protected static ?string $recordTitleAttribute = 'id';

    protected static ?string $title = 'Orders';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id'),
                TextColumn::make('total')
                    ->getStateUsing(fn($record) => $record->total)
            ]);
    }

    protected function getTableRecordUrlUsing(): Closure
    {
        return fn ($record): string => OrderResource::getUrl('edit', ['record' => $record]);
    }
}
