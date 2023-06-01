<?php

namespace Antidote\LaravelCartStripe;


//use Antidote\LaravelCart\Commands\CreateMigrationCommand;
use Antidote\LaravelCart\Providers\EventServiceProvider;
use Antidote\LaravelCartStripe\Components\StripeCheckoutClientScriptComponent;
use Illuminate\Foundation\Http\Kernel;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Blade;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function boot(Kernel $kernel)
    {
//        if($this->app->runningInConsole()) {
//            $this->commands([
//                CreateMigrationCommand::class
//            ]);
//        }

        $this->loadRoutesFrom(__DIR__.'../../routes/web.php');

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'laravel-cart-stripe');

        $this->app->register(EventServiceProvider::class);

        Blade::component('stripe-checkout-client-script', StripeCheckoutClientScriptComponent::class);

//        $router = $this->app->make(Router::class);
        //$router->pushMiddlewareToGroup('stripe_webhook', WhitelistStripeIPAddresses::class);
        //$router->pushMiddlewareToGroup('stripe_webhook', AllowStripeWebhooksDuringMaintenance::class);


        //@see https://stackoverflow.com/a/20550845
        Arr::macro('mergeDeep', function($array2, $array1) {
            foreach ($array2 as $k => $v) {
                if ( is_array($array1) ) {
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
                } else {
                    $array1 = array($v);
                }
            }
            return $array1;
        });
    }
}
