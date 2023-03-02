<?php

if(!function_exists('getClassNameFor')) {

    function getClassNameFor($item) {
        if(!in_array($item, array_keys(config('laravel-cart.classes') ?? []))) {
            throw new Exception("\'{$item}\' not allowed");
        }

        return config('laravel-cart.classes.'.$item);
    }
}
