<?php

namespace Antidote\LaravelCartFilament\Resources\OrderResource\RelationManagers;

use Antidote\LaravelCartStripe\Contracts\StripeOrderLogItem;
use Filament\Resources\Table;
use Filament\Tables\Actions\Action;
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
            ])
            ->actions(self::getActions());
    }

    private static function getActions() : array
    {
        //@todo better way to determine payment provider needed?
        if(is_a(getClassNameFor('order_log_item'), StripeOrderLogItem::class)) {
            return [
                Action::make('event')
                    ->action(fn($record) => $record)
                    ->modalContent(function($record) {

                        return view('laravel-cart-filament::stripe-event', [
                            'event_data' => \Illuminate\Support\Arr::dot($record->event)]);
                    })
            ];
        };

        return [];
    }
}
