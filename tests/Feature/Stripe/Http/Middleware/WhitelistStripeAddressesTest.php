<?php

it('will restrict access to the web hook controller', function() {

    $mockedRequest = $this->partialMock(\Illuminate\Http\Request::class, function(\Mockery\MockInterface $mock) {
        $mock->shouldReceive('getClientIp')
            ->andReturn('62.30.207.45');
    });

    (new \Antidote\LaravelCartStripe\Http\Middleware\WhitelistStripeIPAddresses())->handle($mockedRequest, function($request) {});
})
->expectExceptionMessage('Unauthorized Access Stripe')
->coversClass(\Antidote\LaravelCartStripe\Http\Middleware\WhitelistStripeIPAddresses::class);

it('will allow access to the web hook controller', function() {

    $mockedRequest = $this->partialMock(\Illuminate\Http\Request::class, function(\Mockery\MockInterface $mock) {
        $mock->shouldReceive('getClientIp')
            ->andReturn('3.18.12.63');
    });

    $response = (new \Antidote\LaravelCartStripe\Http\Middleware\WhitelistStripeIPAddresses())->handle($mockedRequest, function($request) {  return true; });

    $this->assertTrue($response);
})
->coversClass(\Antidote\LaravelCartStripe\Http\Middleware\WhitelistStripeIPAddresses::class);
