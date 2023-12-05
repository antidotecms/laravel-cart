<?php

namespace Tests\Feature\Filament;

use Antidote\LaravelCart\Tests\CustomResourcesTestCase;
use Antidote\LaravelCart\Tests\Fixtures\Filament\Resources\CustomAdjustmentResource;
use Antidote\LaravelCart\Tests\Fixtures\Filament\Resources\CustomCustomerResource;
use Antidote\LaravelCart\Tests\Fixtures\Filament\Resources\CustomOrderResource;

/**
 * @covers \Antidote\LaravelCartFilament\FilamentServiceProvider
 */
class ServiceProviderWithCustomResourcesTest extends CustomResourcesTestCase
{

    /**
     * @test
     */
    public function provides_the_ability_to_override_resources()
    {
        expect(app('filament')->getResources())
            ->toEqualCanonicalizing([
                'order' => CustomOrderResource::class,
                'adjustment' => CustomAdjustmentResource::class,
                'customer' => CustomCustomerResource::class
            ]);
    }
}
