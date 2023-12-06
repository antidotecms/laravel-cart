<?php

namespace Antidote\LaravelCartFilament\Resources\CustomerResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class OrderRelationManager extends RelationManager
{
    protected static string $relationship = 'orders';

    protected static ?string $recordTitleAttribute = 'id';

    protected static ?string $title = 'Orders';

    public function table(Table $table): Table
    {
        return $table
            ->recordUrl(fn(Model $record): string => \Antidote\LaravelCartFilament\Resources\OrderResource::getUrl('edit', ['record' => $record]))
            ->columns([
                TextColumn::make('id'),
                TextColumn::make('total')
            ]);
    }

    //@codeCoverageIgnoreStart
//    protected function getTableRecordUrlUsing(): Closure
//    {
//        return fn (Model $record): string => OrderResource::getUrl('edit', ['record' => $record]);
//    }
    //@codeCoverageIgnoreEnd
}
