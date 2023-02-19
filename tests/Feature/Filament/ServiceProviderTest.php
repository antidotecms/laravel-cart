<?php

namespace Tests\Feature\Filament;

use Antidote\LaravelCartFilament\Resources\CustomerResource;
use Antidote\LaravelCartFilament\Resources\OrderResource;

class ServiceProviderTest extends \Antidote\LaravelCart\Tests\TestCase
{
    public function overrideFilamentResourcesEnvironment($app)
    {
        $order_resource_class = new class extends \Filament\Resources\Resource {};
        $customer_resource_class = new class extends \Filament\Resources\Resource {};

        $app->config->set('laravel-cart.filament', $this->getResourceClasses());
    }

    public function getResourceClasses()
    {
        $order_resource_class = new class extends \Filament\Resources\Resource {};
        $customer_resource_class = new class extends \Filament\Resources\Resource {};

        return [
            'order' => $order_resource_class::class,
            'customer' => $customer_resource_class::class
        ];
    }

    /**
     * @test
     */
    public function it_will_provide_default_filament_resources()
    {
        expect(\Filament\Facades\Filament::getResources())->toBe([
            'order' => OrderResource::class,
            'customer' => CustomerResource::class
        ]);
    }

    /**
     * @test
     * @define-env overrideFilamentResourcesEnvironment
     */
    public function provides_the_ability_to_override_resources()
    {
        expect(\Filament\Facades\Filament::getResources())
            ->toBe($this->getResourceClasses());
    }
}

//it('will provide default filament resources', function() {
//
//    expect(\Filament\Facades\Filament::getResources())->toBe([
//        'order' => OrderResource::class,
//        'customer' => CustomerResource::class
//    ]);
//});
//
///**
// * @test
// * @define-env overrideFilamentResourcesEnvironment
// */
//it('provides the ability to override resources', function () {
//
//    expect(\Filament\Facades\Filament::getResources())->toBe($this->getResourceClasses());
//
//});
