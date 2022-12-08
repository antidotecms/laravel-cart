<?php

it('will restrict access to the web hook controller', function() {

    $mockedRequest = $this->partialMock(\Illuminate\Http\Request::class, function(\Mockery\MockInterface $mock) {
        $mock->shouldReceive('getClientIp')
            ->andReturn('62.30.207.45');
    });

    $this->expectErrorMessage('Unauthorized Access Stripe');

    (new \Antidote\LaravelCartStripe\Http\Middleware\WhitelistStripeIPAddresses())->handle($mockedRequest, function($request) {});
});

it('will allow access to the web hook controller', function() {

    $mockedRequest = $this->partialMock(\Illuminate\Http\Request::class, function(\Mockery\MockInterface $mock) {
        $mock->shouldReceive('getClientIp')
            ->andReturn('3.18.12.63');
    });

    $response = (new \Antidote\LaravelCartStripe\Http\Middleware\WhitelistStripeIPAddresses())->handle($mockedRequest, function($request) {  return true; });

    $this->assertTrue($response);
});
