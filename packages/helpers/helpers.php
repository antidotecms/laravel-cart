<?php

if(!function_exists('getClassNameFor')) {

    /** @todo remove - get config directly from CartPanelPlugin */
    function getClassNameFor($item) {
        return \Antidote\LaravelCartFilament\CartPanelPlugin::get('models.'.$item);
    }
}
