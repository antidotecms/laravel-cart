<?php

namespace Antidote\LaravelCart\Tests\Fixtures\App\Mails;

use Antidote\LaravelCart\Contracts\Order;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class TestOrderCompleteMail extends \Illuminate\Mail\Mailable
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
        return new Content(
            view: 'emails.order-complete'
        );
    }
}
