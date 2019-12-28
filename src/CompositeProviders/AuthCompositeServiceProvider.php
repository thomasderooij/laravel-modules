<?php

namespace Thomasderooij\LaravelModules\CompositeProviders;

use Illuminate\Support\AggregateServiceProvider;

class AuthCompositeServiceProvider  extends AggregateServiceProvider
{
    use CompositeProviderTrait;

    protected $name = "AuthServiceProvider";
}
