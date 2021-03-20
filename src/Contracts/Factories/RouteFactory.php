<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Contracts\Factories;

interface RouteFactory
{
    /**
     * Create route files
     */
    public function create (string $moduleName) : void;
}
