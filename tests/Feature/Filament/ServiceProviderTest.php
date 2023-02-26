<?php

namespace Tests\Feature\Filament;

use Antidote\LaravelCart\Contracts\Customer;
use Antidote\LaravelCart\Contracts\Order;
use Antidote\LaravelCartFilament\Resources\AdjustmentResource;
use Antidote\LaravelCartFilament\Resources\CustomerResource;
use Antidote\LaravelCartFilament\Resources\OrderResource;

class ServiceProviderTest extends \Antidote\LaravelCart\Tests\TestCase
{
    public function defaultFilamentResourcesEnvironment($app)
    {
        $this->configureModels($app);
    }

    public function overrideFilamentResourcesEnvironment($app)
    {
        $this->configureModels($app);
        $app->config->set('laravel-cart.filament', $this->getResourceClasses());
    }

    public function configureModels($app)
    {
        $customer_class = new class extends Customer {};

        $app->config->set('laravel-cart.classes.customer', $customer_class::class);

        $order_class = new class extends Order {
            public function updateStatus() {}
        };

        $app->config->set('laravel-cart.classes.order', $order_class::class);
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
     * @define-env defaultFilamentResourcesEnvironment
     */
    public function it_will_provide_default_filament_resources()
    {
        expect(\Filament\Facades\Filament::getResources())->toBe([
            'order' => OrderResource::class,
            'customer' => CustomerResource::class,
            'adjustment' => AdjustmentResource::class
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
