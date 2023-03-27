<?php

it('will return the correct class', function() {

    Config::set('laravel-cart.classes.product', \Antidote\LaravelCart\Tests\Fixtures\App\Models\Products\TestProduct::class);

    $class_name = getClassNameFor('product');

    expect($class_name)->toBe(\Antidote\LaravelCart\Tests\Fixtures\App\Models\Products\TestProduct::class);
})
->coversFunction('getClassNameFor');

it('will throw an exception if the item does not exist', function () {

    Config::set('laravel-cart.classes', []);

    $class_name = getClassNameFor('something');

})
->coversFunction('getClassNameFor')
->throws(Exception::class)
->expectExceptionMessage("\'something\' not allowed");
