<?php

namespace Antidote\LaravelCartFilament\Resources\OrderResource\RelationManagers;

use Filament\Resources\Table;
use Filament\Tables\Columns\TextColumn;

class OrderLogItemRelationManager extends \Filament\Resources\RelationManagers\RelationManager
{
    protected static string $relationship = 'logItems';

    protected static ?string $recordTitleAttribute = 'message';

    protected static ?string $title = 'Log';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at'),
                    //->getStateUsing(fn($record) => $record->created_at),
                TextColumn::make('message')
            ]);
    }
}
