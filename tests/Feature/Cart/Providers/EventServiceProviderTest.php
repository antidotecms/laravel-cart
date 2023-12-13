<?php

namespace Antidote\LaravelCart\Tests\Feature\Cart\Providers;

use Antidote\LaravelCart\Events\OrderCompleted;
use Antidote\LaravelCart\Listeners\SendOrderConfirmation;
use Antidote\LaravelCart\Tests\TestCase;
use Illuminate\Support\Facades\Event;

/**
 * @covers \Antidote\LaravelCart\Providers\EventServiceProvider
 */
class EventServiceProviderTest extends TestCase
{
//    protected function getPackageProviders($app): array
//    {
//        return [
//            CartServiceProvider::class,
//            EventServiceProvider::class
//        ];
//    }

    /**
     * @test
     */
    public function it_uses_the_correct_class()
    {
        Event::fake();
        Event::assertListening(OrderCompleted::class, SendOrderConfirmation::class);
    }
}
