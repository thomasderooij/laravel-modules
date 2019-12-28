<?php

namespace Thomasderooij\LaravelModules\Contracts\Factories;

use Illuminate\Contracts\Filesystem\FileNotFoundException;

interface EventServiceProviderFactory
{
    /**
     * Create a new event service provider based on a stub
     *
     * @param string $module
     * @throws FileNotFoundException
     */
    public function create (string $module);
}
