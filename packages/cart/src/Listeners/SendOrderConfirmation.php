<?php

namespace Antidote\LaravelCart\Listeners;

use Antidote\LaravelCart\Events\OrderCompleted;
use Antidote\LaravelCart\Mail\OrderComplete;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Mail;

class SendOrderConfirmation
{
    public function handle(OrderCompleted $event)
    {
        $order_complete_mail = config('laravel-cart.classes.mails.order_complete') ?? OrderComplete::class;

        Mail::to($event->order->customer)
            ->bcc('tcsmith1978@gmail.com')
            ->send(new $order_complete_mail($event->order));

        $event->order->log('Order complete mail sent to '.$event->order->customer->email);

        Notification::make()
            ->title('Order complete email resent')
            ->success()
            ->send();
    }
}
