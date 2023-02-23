<?php

it('will return the correct class', function() {

    Config::set('laravel-cart.classes.product', \Antidote\LaravelCart\Tests\Fixtures\App\Models\Products\TestProduct::class);

    $class_name = getClassNameFor('product');

    expect($class_name)->toBe(\Antidote\LaravelCart\Tests\Fixtures\App\Models\Products\TestProduct::class);
});

it('will return the correct id', function() {

    Config::set('laravel-cart.classes.product', \Antidote\LaravelCart\Tests\Fixtures\App\Models\Products\TestProduct::class);

    $class_name = getKeyFor('product');

    expect($class_name)->toBe('test_product_id');
});
