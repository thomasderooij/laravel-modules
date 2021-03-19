<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Contracts\Factories;

interface AuthServiceProviderFactory
{
    /**
     * Create a new auth service provider based on a stub
     */
    public function create (string $module) : void;
}
