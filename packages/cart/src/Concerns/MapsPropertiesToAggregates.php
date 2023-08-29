<?php

namespace Antidote\LaravelCart\Concerns;

use Illuminate\Support\Str;

trait MapsPropertiesToAggregates
{
    private function mapToAggregate(string|object $aggregate, string $property_or_method, mixed $default = null, ?array $params = null) : mixed
    {
        //is_string($aggregate) & $aggregate = new $aggregate;

        if(is_string($aggregate)) {
            $aggregate = new $aggregate;
        }

        if(method_exists($aggregate, $property_or_method)) {
            return $params ? $aggregate->$property_or_method(...$params) : $aggregate->$property_or_method();
        }

        //$property_or_method = Str::of($property_or_method)->studly()->lcfirst()->value();
        $property_or_method = Str::of($property_or_method)->headline()->lower()->replace(" ", "_")->lcfirst()->value();

        if(property_exists($aggregate, $property_or_method)) {
            return $aggregate->$property_or_method;
        }

        return $default;
    }
}
