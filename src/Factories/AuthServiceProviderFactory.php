<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Factories;

class AuthServiceProviderFactory extends ServiceProviderFactory
{
    /**
     * Get the route service provider stub
     *
     * @return string
     */
    protected function getStub () : string
    {
        return __DIR__ . '/stubs/authServiceProvider.stub';
    }

    /**
     * Get the route service provider classname
     *
     * @return string
     */
    protected function getClassName () : string
    {
        return "AuthServiceProvider";
    }
}
