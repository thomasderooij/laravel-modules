<?php

namespace Thomasderooij\LaravelModules\Factories;

use Thomasderooij\LaravelModules\Contracts\Factories\AuthServiceProviderFactory as Contract;

class AuthServiceProviderFactory extends ServiceProviderFactory implements Contract
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
