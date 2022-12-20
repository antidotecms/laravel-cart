<?php

namespace Antidote\LaravelCart\Listeners;

use Antidote\LaravelCart\Events\OrderCompleted;
use Antidote\LaravelCart\Mail\OrderComplete;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class SendOrderConfirmation
{
    public function handle(OrderCompleted $event)
    {
        $order_complete_mail = config('laravel-cart.classes.mails.order_complete') ?? OrderComplete::class;
        $order_complete_recipient_email =  config('laravel-cart.emails.order_complete');

        $validator = Validator::make(['email' => $order_complete_recipient_email], [
            'email' => 'email|nullable'
        ]);

        if($validator->fails()) {
            $order_complete_recipient_email = null;
        }

        Mail::to($event->order->customer)
            ->bcc($order_complete_recipient_email)
            ->send(new $order_complete_mail($event->order));

        $event->order->log('Order complete mail sent to '.$event->order->customer->email);

        Notification::make()
            ->title('Order complete email resent')
            ->success()
            ->send();
    }
}
