<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;

uses(\Tests\TestCase::class)->in(__DIR__);

uses(RefreshDatabase::class)->in('Feature');

uses()->beforeEach(fn() => Config::set('laravel-cart.product_class', \Tests\Fixtures\app\Models\Products\TestProduct::class))->in('Feature');
