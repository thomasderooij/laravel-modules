<?php

namespace Thomasderooij\LaravelModules\Contracts\Factories;

use Illuminate\Contracts\Filesystem\FileNotFoundException;

interface RouteServiceProviderFactory
{
    /**
     * Create a route service provider for your module
     *
     * @param string $module
     * @throws FileNotFoundException
     */
    public function create (string $module) : void;
}
