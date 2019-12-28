<?php

namespace Thomasderooij\LaravelModules\Contracts\Factories;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Thomasderooij\LaravelModules\Exceptions\ModuleCreationException;

interface ModuleFactory
{
    /**
     * Create a new module
     *
     * @param string $module
     * @throws ModuleCreationException
     * @throws FileNotFoundException
     */
    public function create (string $module) : void;
}
