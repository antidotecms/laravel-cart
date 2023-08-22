<?php

namespace Antidote\LaravelCartStripe;


//use Antidote\LaravelCart\Commands\CreateMigrationCommand;
use Antidote\LaravelCart\Providers\EventServiceProvider;
use Antidote\LaravelCartStripe\Components\StripeCheckoutClientScriptComponent;
use Antidote\LaravelCartStripe\Domain\PaymentIntent;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Blade;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function register()
    {
        $this->routes();

        $this->app->bind(PaymentIntent::class, PaymentIntent::class);

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'laravel-cart-stripe');

        $this->app->register(EventServiceProvider::class);

        Blade::component('stripe-checkout-client-script', StripeCheckoutClientScriptComponent::class);

        //@see https://stackoverflow.com/a/20550845
        Arr::macro('mergeDeep', function(array $array2, array $array1) {
            foreach ($array2 as $k => $v) {

                if ( is_string($v) && ! in_array($v, $array1) ) {
                    /**
                     *  Preserve keys in n-dimension using $k
                     */
                    $array1[$k] = $v;
                } else if ( is_array($v) ) {
                    if ( isset($array1[$k]) ) {
                        $array1[$k] = Arr::mergeDeep($array1[$k], $v);
                    } else {
                        $array1[$k] = $v;
                    }
                }

            }
            return $array1;
        });
    }

    private function routes()
    {
        $this->loadRoutesFrom(__DIR__.'../../routes/web.php');
    }
}
