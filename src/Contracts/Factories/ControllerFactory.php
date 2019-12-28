<?php

namespace Thomasderooij\LaravelModules\Contracts\Factories;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Thomasderooij\LaravelModules\Exceptions\InitExceptions\ModulesNotInitialisedException;

interface ControllerFactory
{
    /**
     * Create a base controller for a new module
     *
     * @param string $module
     * @throws FileNotFoundException
     * @throws ModulesNotInitialisedException
     */
    public function create (string $module) : void;

    /**
     * Get the qualified classname of the base controller for a given module
     *
     * @param string $module
     * @return string
     */
    public function getQualifiedClassName (string $module) : string;
}
