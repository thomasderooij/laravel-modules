<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\CompositeProviders;

use Illuminate\Support\ServiceProvider;

class BroadcastCompositeServiceProvider extends ServiceProvider
{
    use CompositeProviderTrait;

    protected string $name = "BroadcastServiceProvider";
}
