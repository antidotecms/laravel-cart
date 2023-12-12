<?php

namespace Tests\Feature\Filament;

use Antidote\LaravelCart\Tests\Fixtures\Filament\Resources\CustomAdjustmentResource;
use Antidote\LaravelCart\Tests\Fixtures\Filament\Resources\CustomCustomerResource;
use Antidote\LaravelCart\Tests\Fixtures\Filament\Resources\CustomOrderResource;
use Antidote\LaravelCartFilament\CartPanelPlugin;
use Filament\FilamentServiceProvider;
use Filament\Panel;
use Filament\Support\SupportServiceProvider;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(CartPanelPlugin::class)]
class ServiceProviderWithCustomResourcesTest extends TestCase //extends CustomResourcesTestCase
{

    /**
     * @test
     */
    public function provides_the_ability_to_override_resources()
    {
        //$app_mock = \Mockery::mock(\Illuminate\Foundation\Application::class)->makePartial();



        $cart_plugin = new CartPanelPlugin(app());
        $cart_plugin->adjustmentResource(CustomAdjustmentResource::class)
            ->customerResource(CustomCustomerResource::class)
            ->orderResource(CustomOrderResource::class);

        $fsp = new FilamentServiceProvider(app());
        $ssp = new SupportServiceProvider(app());

        app()->register(new LivewireServiceProvider(app()));
        app()->register($fsp);
        app()->register($ssp);
        $p = new Panel();
        $p->plugin($cart_plugin);
        app()->get('filament')->setCurrentPanel($p);

        $fsp->boot();

        //dump(array_keys(app()->getBindings()));
        //dump(app()->get('filament'));

        //dump(app()->getLoadedProviders());

//        $app_mock->shouldAllowMockingProtectedMethods()
//            ->shouldReceive('bootProvider')
//            ->with($cart_plugin);

        expect(app()->get('filament')->getResources())
            ->toEqualCanonicalizing([
                'order' => CustomOrderResource::class,
                'adjustment' => CustomAdjustmentResource::class,
                'customer' => CustomCustomerResource::class
            ]);
    }
}
