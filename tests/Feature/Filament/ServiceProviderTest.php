<?php

namespace Tests\Feature\Filament;

use Antidote\LaravelCartFilament\Resources\AdjustmentResource;
use Antidote\LaravelCartFilament\Resources\CustomerResource;
use Antidote\LaravelCartFilament\Resources\OrderResource;

/**
 * @covers \Antidote\LaravelCartFilament\FilamentServiceProvider
 */
class ServiceProviderTest extends \Antidote\LaravelCart\Tests\TestCase
{
    public function overrideFilamentResourcesEnvironment($app)
    {
        $app->config->set('laravel-cart.filament', $this->getResourceClasses());
    }

    public function getResourceClasses()
    {
        $order_resource_class = new class extends \Filament\Resources\Resource {};
        $customer_resource_class = new class extends \Filament\Resources\Resource {};
        $adjustment_resource_class = new class extends \Filament\Resources\Resource {};

        return [
            'order' => $order_resource_class::class,
            'customer' => $customer_resource_class::class,
            'adjustment' => $adjustment_resource_class::class
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
