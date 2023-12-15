<?php

namespace Antidote\LaravelCart\Tests\Feature\Filament;

use Antidote\LaravelCart\Tests\TestCase;
use Antidote\LaravelCartFilament\Resources\AdjustmentResource;
use Antidote\LaravelCartFilament\Resources\CustomerResource;
use Antidote\LaravelCartFilament\Resources\OrderResource;
use Antidote\LaravelCartFilament\Resources\ProductResource;

/**
 * @covers \Antidote\LaravelCartFilament\FilamentServiceProvider
 */
class ServiceProviderTest extends TestCase
{
    /**
     * @test
     */
    public function provides_the_ability_to_provide_default_resources()
    {
        expect(app('filament')->getResources())
            ->toEqualCanonicalizing([
                'order' => OrderResource::class,
                'adjustment' => AdjustmentResource::class,
                'customer' => CustomerResource::class,
                'product' => ProductResource::class
            ]);
    }
}
