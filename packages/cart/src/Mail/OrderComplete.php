<?php

namespace Antidote\LaravelCart\Mail;

use Antidote\LaravelCart\Models\Order;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class OrderComplete extends \Illuminate\Mail\Mailable
{
    public Order $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function envelope()
    {
        return new Envelope(
            subject: 'Order Complete'
        );
    }

    public function content()
    {
        //@todo create a very basic order email
        return new Content(
            view: 'laravel-cart::emails.order-complete'
        );
    }
}
