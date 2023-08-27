<?php

namespace Antidote\LaravelCart\Tests\Feature\Cart;

use Antidote\LaravelCart\CartServiceProvider;
use Illuminate\Config\Repository;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Antidote\LaravelCart\CartServiceProvider
 */
class ServiceProvider2Test extends TestCase
{
    /**
     * @test
     */
    public function it_will_throw_an_exception_if_order_complete_is_not_set()
    {
        $app_mock = \Mockery::mock(\Illuminate\Foundation\Application::class)->makePartial();

        $app_mock->shouldReceive('offsetGet')->zeroOrMoreTimes()->with('config')->andReturn(app(Repository::class));

        $app_mock['config']->set('laravel-cart.urls.order_complete', '');

        $dispatcher_mock = \Mockery::mock(\Illuminate\Events\Dispatcher::class);

        $app_mock->shouldReceive('offsetGet')
            ->zeroOrMoreTimes()
            ->with('router')
            ->andReturn([]);

        $service = new CartServiceProvider($app_mock);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('The order complete url has not been set in config');

        $service->boot();

        $this->assertTrue(true);
    }
}
