<?php

if(!function_exists('getClassNameFor')) {

    function getClassNameFor($item) {
        return app('filament')->getPlugin('laravel-cart')->getModel($item);
    }
}
