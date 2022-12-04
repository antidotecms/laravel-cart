<?php

namespace Antidote\LaravelCart\Providers;

use Antidote\LaravelCart\Events\OrderCompleted;
use Antidote\LaravelCart\Listeners\SendOrderConfirmation;

class EventServiceProvider extends \Illuminate\Foundation\Support\Providers\EventServiceProvider
{
    public function listens()
    {
        return [
            OrderCompleted::class => [
                SendOrderConfirmation::class
            ]
        ];
    }
}
