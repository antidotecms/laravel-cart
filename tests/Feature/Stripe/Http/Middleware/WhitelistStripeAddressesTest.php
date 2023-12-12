<?php

beforeEach(fn() => TiMacDonald\Log\LogFake::bind());

it('will restrict access to the web hook controller', function() {

    $mockedRequest = $this->partialMock(\Illuminate\Http\Request::class, function(\Mockery\MockInterface $mock) {
        $mock->shouldReceive('getClientIp')
            ->andReturn('62.30.207.45');
    });

    /** @var \Illuminate\Http\Response $response */
    $response = (new \Antidote\LaravelCartStripe\Http\Middleware\WhitelistStripeIPAddresses())->handle($mockedRequest, function($request) {});
    expect($response->getStatusCode())->toBe(403);
})
//->expectExceptionMessage('Unauthorized Stripe Access')
->coversClass(\Antidote\LaravelCartStripe\Http\Middleware\WhitelistStripeIPAddresses::class);

it('will allow access to the web hook controller', function() {

    $mockedRequest = $this->partialMock(\Illuminate\Http\Request::class, function(\Mockery\MockInterface $mock) {
        $mock->shouldReceive('getClientIp')
            ->andReturn('3.18.12.63');
    });

    $callback = response('', 200);

    $response = (new \Antidote\LaravelCartStripe\Http\Middleware\WhitelistStripeIPAddresses())->handle($mockedRequest, fn() => $callback);

    expect($response)->toBe($callback);
})
->coversClass(\Antidote\LaravelCartStripe\Http\Middleware\WhitelistStripeIPAddresses::class);

it('will log an entry if a stripe event was sent from an un-whitelisted ip address', function () {

    \Illuminate\Support\Facades\Log::swap(new \TiMacDonald\Log\LogFake());

    $mockedRequest = $this->partialMock(\Illuminate\Http\Request::class, function(\Mockery\MockInterface $mock) {
        $mock->shouldReceive('getClientIp')
            ->andReturn('62.30.207.45');
    });

        $response = (new \Antidote\LaravelCartStripe\Http\Middleware\WhitelistStripeIPAddresses())->handle($mockedRequest, function ($request) {
        });

        \Illuminate\Support\Facades\Log::assertLogged(function(\TiMacDonald\Log\LogEntry $log) {
            return $log->message == 'Stripe Attempt from IP: 62.30.207.45';
        });
})
->coversClass(\Antidote\LaravelCartStripe\Http\Middleware\WhitelistStripeIPAddresses::class);

it('will log an entry if a stripe event was sent from an registered ip address as null', function () {

    \Illuminate\Support\Facades\Log::swap(new \TiMacDonald\Log\LogFake());

    $mockedRequest = $this->partialMock(\Illuminate\Http\Request::class, function(\Mockery\MockInterface $mock) {
        $mock->shouldReceive('getClientIp')
            ->andReturnNull();
    });


        $response = (new \Antidote\LaravelCartStripe\Http\Middleware\WhitelistStripeIPAddresses())->handle($mockedRequest, function ($request) {
        });

    \Illuminate\Support\Facades\Log::assertLogged(function(\TiMacDonald\Log\LogEntry $log) {
        return $log->message == 'Stripe Attempt from IP: Unknown';
    });

})
->coversClass(\Antidote\LaravelCartStripe\Http\Middleware\WhitelistStripeIPAddresses::class);

it('will log an entry if a stripe event was sent from an registered ip address as empty string', function () {

    \Illuminate\Support\Facades\Log::swap(new \TiMacDonald\Log\LogFake());

    $mockedRequest = $this->partialMock(\Illuminate\Http\Request::class, function(\Mockery\MockInterface $mock) {
        $mock->shouldReceive('getClientIp')
            ->andReturn('');
    });


    $response = (new \Antidote\LaravelCartStripe\Http\Middleware\WhitelistStripeIPAddresses())->handle($mockedRequest, function ($request) {
    });

    \Illuminate\Support\Facades\Log::assertLogged(function(\TiMacDonald\Log\LogEntry $log) {
        return $log->message == 'Stripe Attempt from IP: Unknown';
    });

})
    ->coversClass(\Antidote\LaravelCartStripe\Http\Middleware\WhitelistStripeIPAddresses::class);

it('will abort with a 403 response if a stripe event was sent from an un-whitelisted ip address', function () {

    $mockedRequest = $this->partialMock(\Illuminate\Http\Request::class, function(\Mockery\MockInterface $mock) {
        $mock->shouldReceive('getClientIp')
            ->andReturn('62.30.207.45');
    });

    //$this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
    //$this->expectExceptionMessage('Unauthorized Stripe Access');

    try {
        $response = (new \Antidote\LaravelCartStripe\Http\Middleware\WhitelistStripeIPAddresses())->handle($mockedRequest, function ($request) {
        });
    } catch(\Symfony\Component\HttpKernel\Exception\HttpException $e) {
        expect($e->getStatusCode())->toBe(403);
    }

    //expect($response->getStatusCode())->toBe(403);
})
->coversClass(\Antidote\LaravelCartStripe\Http\Middleware\WhitelistStripeIPAddresses::class);

it('will not add localhost as a trusted IP when developing', function () {

    //app()->runningUnitTests(fn() => true);

    app()->detectEnvironment(fn() => 'production');

    $mockedRequest = $this->partialMock(\Illuminate\Http\Request::class, function(\Mockery\MockInterface $mock) {

        $mock->shouldReceive('getClientIp')
            ->andReturn('62.30.207.45');
    });

    $middleware = new \Antidote\LaravelCartStripe\Http\Middleware\WhitelistStripeIPAddresses();

    //$middleware->handle($mockedRequest, function($request) {});

    expect($middleware->getWhitelistedIPAddresses())->not()->toContain('127.0.0.1');
})
->skip('unsure how to test this. Need to be able to change APP_ENV temporarily for this one test');
