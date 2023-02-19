<?php

namespace Antidote\LaravelCart\Tests;

class ServiceProviderTestCase extends TestCase
{
    protected function getEnvironmentSetUp($app)
    {
        \Config::set('laravel-cart.filament', $this->getResourceClasses());
    }
}
