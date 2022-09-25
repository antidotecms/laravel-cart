<?php

if(!function_exists('getClassNameFor')) {

    function getClassNameFor($item) {
        if(!in_array($item, array_keys(config('laravel-cart.classes')))) {
            throw new Exception('item not allowed');
        }

        return config('laravel-cart.classes.'.$item);
    }
}

if(!function_exists('getKeyFor')) {

    function getKeyFor($item) {
        if(!in_array($item, array_keys(config('laravel-cart.classes')))) {
            throw new Exception('item not allowed');
        }

        return \Illuminate\Support\Str::snake(class_basename(config('laravel-cart.classes.'.$item))).'_id';
    }
}
