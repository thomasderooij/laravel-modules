<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Contracts\Factories;

interface ControllerFactory
{
    /**
     * Create a base controller for a new module
     */
    public function create (string $module) : void;

    /**
     * Get the qualified classname of the base controller for a given module
     */
    public function getQualifiedClassName (string $module) : string;
}
