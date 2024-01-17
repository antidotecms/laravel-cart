<?php

namespace Antidote\LaravelCart\Listeners;

use Antidote\LaravelCart\Events\OrderCompleted;
use Filament\Notifications\Notification;

class SendOrderConfirmation
{
    public function handle(OrderCompleted $event)
    {
        $event->order->customer->notify(new \Antidote\LaravelCart\Notifications\OrderComplete($event->order));

        $event->order->log('Order complete mail sent to '.$event->order->customer->email);

        Notification::make()
            ->title('Order complete email resent')
            ->success()
            ->send();
    }
}
