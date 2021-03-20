<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\CompositeProviders;

use Illuminate\Support\AggregateServiceProvider;

class RouteCompositeServiceProvider extends AggregateServiceProvider
{
    use CompositeProviderTrait;

    protected string $name = "RouteServiceProvider";
}
