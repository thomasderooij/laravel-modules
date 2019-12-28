<?php

namespace Thomasderooij\LaravelModules\CompositeProviders;

use Illuminate\Support\AggregateServiceProvider;

class RouteCompositeServiceProvider extends AggregateServiceProvider
{
    use CompositeProviderTrait;

    protected $name = "RouteServiceProvider";
}
