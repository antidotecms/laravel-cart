<?php

namespace Antidote\LaravelCartFilament\Resources\OrderResource\Pages;

use Antidote\LaravelCart\Events\OrderCompleted;
use Filament\Pages\Actions\Action;
use Filament\Pages\Actions\ActionGroup;
use Filament\Pages\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditOrder extends EditRecord
{
    public static function getResource(): string
    {
        return config('laravel-cart.filament.order');
    }

    protected static ?string $title = 'Edit Order';

    protected function getActions(): array
    {
        return [
            ActionGroup::make([
                Action::make('resend_order_complete_notification')
                    ->action(fn() => event(new OrderCompleted($this->record)))
            ]),
            DeleteAction::make('delete')
        ];
    }
}
