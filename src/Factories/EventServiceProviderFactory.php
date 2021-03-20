<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Factories;

class EventServiceProviderFactory extends ServiceProviderFactory
{
    /**
     * Get the route service provider stub
     *
     * @return string
     */
    protected function getStub(): string
    {
        return __DIR__ . '/stubs/eventServiceProvider.stub';
    }

    /**
     * Get the route service provider classname
     *
     * @return string
     */
    protected function getClassName(): string
    {
        return "EventServiceProvider";
    }
}
