<?php

use Antidote\LaravelCartFilament\CartPanelPlugin;
use Antidote\LaravelCartStripe\Http\Middleware\AllowStripeWebhooksDuringMaintenance;

it('will prevent access to stripe webhook path if in maintenance', function() {

    //@todo how to fake "post" with specific client id?
    app()->maintenanceMode()->activate([]);

    $response = $this->post(CartPanelPlugin::get('stripe.webhookHandler'));

    $response->assertStatus(503);

    app()->maintenanceMode()->deactivate();

})
->coversClass(AllowStripeWebhooksDuringMaintenance::class);

it('will allow access to the stripe webhook path if in maintenance', function () {

    app()->maintenanceMode()->activate([]);

    $mockedRequest = $this->partialMock(\Illuminate\Http\Request::class, function(\Mockery\MockInterface $mock) {

        $mock->shouldReceive('fullUrlIs')
            ->with(trim(CartPanelPlugin::get('stripe.webhookHandler'), '/'))
            ->andReturnTrue();

    });

    $response = (new AllowStripeWebhooksDuringMaintenance(app()))->handle($mockedRequest, function($request) { return true; });

    $this->assertTrue($response);

    app()->maintenanceMode()->deactivate();
})
->coversClass(AllowStripeWebhooksDuringMaintenance::class);
