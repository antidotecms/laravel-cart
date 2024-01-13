<?php

namespace Antidote\LaravelCart\Notifications;

use Antidote\LaravelCart\Models\Order;
use Antidote\LaravelCart\Models\Product;
use Antidote\LaravelCartFilament\CartPanelPlugin;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use function Filament\Support\format_money;

class OrderComplete extends Notification
{
    private Order $order;
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function toMail($notifiable): MailMessage
    {
        $mail = (new MailMessage())
            ->subject('Order Complete');

        $validator = \Illuminate\Support\Facades\Validator::make(
            ['email' => CartPanelPlugin::get('email')],
            ['email' => 'email']
        );

        if($email = CartPanelPlugin::get('email') && $validator->passes()) {
            $mail->bcc(CartPanelPlugin::get('email'));
        }

            $mail->markdown('laravel-cart::emails.order-complete', [
                'items' => $this->formatItems(),
                'total' => format_money($this->order->total, 'GBP', 100),
                'tax' => format_money($this->order->tax, 'GBP', 100),
                'subtotal' => format_money($this->order->subtotal, 'GBP', 100),
                'address' => collect($this->order->customer->address->attributesToArray())->filter(fn($item, $key) => in_array($key, ['line_1', 'line_2', 'town_city', 'county', 'postcode']))->toArray()
            ]);

//        foreach($this->order->items as $item) {
//            //@todo add description to order item rather than take from product
//            $description = Product::find($item->product_id)->getDescription($item->product_data);
//            $price = format_money($item->price, 'GBP', 100);
//            $mail->line($item->name.' - '.$description.'-'.$item->quantity.'-'.$price);
//            $mail
//        }

        return $mail;
    }

    private function formatItems(): array
    {
        return $this->order->items()->get()->map(function($item) {
            return [
                'name' => $item->getName(),
                'description' => Product::find($item->product_id)->getDescription($item->product_data),
                'quantity' => $item->getQuantity(),
                'line_total' => format_money($item->getCost(), 'GBP', 100)
            ];
        })->toArray();
    }

    public function via()
    {
        return [
            'mail'
        ];
    }
}
