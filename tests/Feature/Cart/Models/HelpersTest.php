<?php

it('will return the correct class', function() {

    $class_name = getClassNameFor('product');

    expect($class_name)->toBe(\Antidote\LaravelCart\Models\Product::class);
})
->coversFunction('getClassNameFor');

it('will throw an exception if the item does not exist', function () {

    $class_name = getClassNameFor('something');

})
->coversFunction('getClassNameFor')
->throws(Exception::class)
->expectExceptionMessage("Model key does not exist");
