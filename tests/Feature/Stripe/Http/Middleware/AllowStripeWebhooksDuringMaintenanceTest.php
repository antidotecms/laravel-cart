<?php

it('will prevent access to stripe webhook path if in maintenance', function() {

    //@todo how to fake "post" with specific client id?
    app()->maintenanceMode()->activate([]);

    $response = $this->post(config('laravel-cart.urls.stripe.webhook_handler'));

    $response->assertStatus(503);
    //$response->assertSuccessful();

    app()->maintenanceMode()->deactivate();

})
->coversClass(\Antidote\LaravelCartStripe\Http\Middleware\AllowStripeWebhooksDuringMaintenance::class);

it('will allow access to the stripe webhook path if in maintenance', function () {

    app()->maintenanceMode()->activate([]);

    $mockedRequest = $this->partialMock(\Illuminate\Http\Request::class, function(\Mockery\MockInterface $mock) {

        $mock->shouldReceive('fullUrlIs')
            ->with(trim(config('laravel-cart.urls.stripe.webhook_handler'), '/'))
            ->andReturnTrue();

    });

    $response = (new \Antidote\LaravelCartStripe\Http\Middleware\AllowStripeWebhooksDuringMaintenance(app()))->handle($mockedRequest, function($request) { return true; });

    $this->assertTrue($response);

    app()->maintenanceMode()->deactivate();
})
->coversClass(\Antidote\LaravelCartStripe\Http\Middleware\AllowStripeWebhooksDuringMaintenance::class);
