<?php

namespace Antidote\LaravelCartFilament\Resources\OrderResource\RelationManagers;

use Antidote\LaravelCartStripe\Contracts\StripeOrderLogItem;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class OrderLogItemRelationManager extends \Filament\Resources\RelationManagers\RelationManager
{
    protected static string $relationship = 'logItems';

    protected static ?string $recordTitleAttribute = 'message';

    protected static ?string $title = 'Log';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at'),
                TextColumn::make('message')
            ]);
    }
}
