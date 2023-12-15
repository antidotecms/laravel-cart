<?php

namespace Tests\Feature\Filament;

use Antidote\LaravelCart\Tests\Fixtures\Filament\Resources\CustomAdjustmentResource;
use Antidote\LaravelCart\Tests\Fixtures\Filament\Resources\CustomCustomerResource;
use Antidote\LaravelCart\Tests\Fixtures\Filament\Resources\CustomOrderResource;
use Antidote\LaravelCart\Tests\Fixtures\Filament\Resources\CustomProductResource;
use Antidote\LaravelCartFilament\CartPanelPlugin;
use Antidote\LaravelCartFilament\Resources\AdjustmentResource;
use Antidote\LaravelCartFilament\Resources\AdjustmentResource\Pages\Concerns\ConfiguresAdjustmentResourcePages;
use Antidote\LaravelCartFilament\Resources\AdjustmentResource\Pages\CreateAdjustment;
use Antidote\LaravelCartFilament\Resources\AdjustmentResource\Pages\EditAdjustment;
use Antidote\LaravelCartFilament\Resources\AdjustmentResource\Pages\ListAdjustments;
use Antidote\LaravelCartFilament\Resources\CustomerResource;
use Antidote\LaravelCartFilament\Resources\CustomerResource\Pages\Concerns\ConfiguresCustomerResourcePages;
use Antidote\LaravelCartFilament\Resources\CustomerResource\Pages\CreateCustomer;
use Antidote\LaravelCartFilament\Resources\CustomerResource\Pages\EditCustomer;
use Antidote\LaravelCartFilament\Resources\CustomerResource\Pages\ListCustomers;
use Antidote\LaravelCartFilament\Resources\OrderResource;
use Antidote\LaravelCartFilament\Resources\OrderResource\Pages\Concerns\ConfiguresOrderResourcePages;
use Antidote\LaravelCartFilament\Resources\OrderResource\Pages\CreateOrder;
use Antidote\LaravelCartFilament\Resources\OrderResource\Pages\EditOrder;
use Antidote\LaravelCartFilament\Resources\OrderResource\Pages\ListOrders;
use Antidote\LaravelCartFilament\Resources\ProductResource;
use Antidote\LaravelCartFilament\Resources\ProductResource\Pages\CreateProduct;
use Antidote\LaravelCartFilament\Resources\ProductResource\Pages\EditProduct;
use Antidote\LaravelCartFilament\Resources\ProductResource\Pages\ListProducts;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

//use PHPUnit\Framework\TestCase;

#[CoversClass(CartPanelPlugin::class)]
#[CoversClass(ConfiguresAdjustmentResourcePages::class)]
#[CoversClass(ConfiguresCustomerResourcePages::class)]
#[CoversClass(ConfiguresOrderResourcePages::class)]
class ServiceProviderWithCustomResourcesTest extends TestCase //extends CustomResourcesTestCase
{

    /**
     * @test
     * @group filament-resources
     */
    public function it_provides_default_resources()
    {
        $cart_plugin = new CartPanelPlugin(app());

        setUpCartPlugin($cart_plugin);

        expect(CartPanelPlugin::get('resources'))
            ->toEqualCanonicalizing([
                'order' => OrderResource::class,
                'adjustment' => AdjustmentResource::class,
                'customer' => CustomerResource::class,
                'product' => ProductResource::class
            ]);
    }
    /**
     * @test
     * @group filament-resources
     */
    public function provides_the_ability_to_override_resources()
    {
        $cart_plugin = new CartPanelPlugin(app());
        setUpCartPlugin($cart_plugin);

        $cart_plugin
            ->config([
                'resources' => [
                    'adjustment' => CustomAdjustmentResource::class,
                    'customer' => CustomCustomerResource::class,
                    'order' => CustomOrderResource::class,
                    'product' => CustomProductResource::class
                ]
            ]);


        expect(CartPanelPlugin::get('resources'))
            ->toEqualCanonicalizing([
                'order' => CustomOrderResource::class,
                'adjustment' => CustomAdjustmentResource::class,
                'customer' => CustomCustomerResource::class,
                'product' => CustomProductResource::class
            ]);
    }

    /**
     * @test
     * @group filament-resources
     */
    public function related_resource_pages_have_correct_resources()
    {
        $cart_plugin = new CartPanelPlugin(app());
        setUpCartPlugin($cart_plugin);
        $cart_plugin
            ->config([
                'resources' => [
                    'adjustment' => CustomAdjustmentResource::class,
                    'customer' => CustomCustomerResource::class,
                    'order' => CustomOrderResource::class,
                    'product' => CustomProductResource::class
                ]
            ]);


        expect(EditOrder::getResource())->toEqual(CustomOrderResource::class);
        expect(CreateOrder::getResource())->toEqual(CustomOrderResource::class);
        expect(ListOrders::getResource())->toEqual(CustomOrderResource::class);

        expect(EditAdjustment::getResource())->toEqual(CustomAdjustmentResource::class);
        expect(CreateAdjustment::getResource())->toEqual(CustomAdjustmentResource::class);
        expect(ListAdjustments::getResource())->toEqual(CustomAdjustmentResource::class);

        expect(EditCustomer::getResource())->toEqual(CustomCustomerResource::class);
        expect(CreateCustomer::getResource())->toEqual(CustomCustomerResource::class);
        expect(ListCustomers::getResource())->toEqual(CustomCustomerResource::class);

        expect(EditProduct::getResource())->toEqual(CustomProductResource::class);
        expect(CreateProduct::getResource())->toEqual(CustomProductResource::class);
        expect(ListProducts::getResource())->toEqual(CustomProductResource::class);
    }

    /**
     * @test
     * @group filament-resources
     */
    public function it_provides_default_models()
    {
        $cart_plugin = new CartPanelPlugin(app());

        setUpCartPlugin($cart_plugin);
    }

    /**
     * @test
     * @group filament-resources
     */
    public function it_will_allow_overriding_default_models()
    {

    }

    /**
     * @test
     * @group filament-resources
     */
    public function it_will_provide_a_default_order_complete_url()
    {
        $cart_plugin = new CartPanelPlugin(app());

        setUpCartPlugin($cart_plugin);

        expect(CartPanelPlugin::get('urls.orderComplete'))->toBe('order-complete');
    }

    /**
     * @test
     * @group filament-resources
     */
    public function it_will_override_the_order_complete_url()
    {
        $cart_plugin = new CartPanelPlugin(app());

        setUpCartPlugin($cart_plugin);

        $cart_plugin->config([
            'urls' => [
                'orderComplete' => 'a-different-url'
            ]
        ]);

        expect(CartPanelPlugin::get('urls.orderComplete'))->toBe('a-different-url');
    }
}
