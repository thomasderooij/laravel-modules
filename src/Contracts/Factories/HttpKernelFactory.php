<?php

namespace Thomasderooij\LaravelModules\Contracts\Factories;

use Illuminate\Contracts\Filesystem\FileNotFoundException;

interface HttpKernelFactory
{
    /**
     * Create a console kernel
     *
     * @param string $module
     * @throws FileNotFoundException
     */
    public function create (string $module) : void;
}
