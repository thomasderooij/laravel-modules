<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Contracts\Factories;

interface ModuleFactory
{
    /**
     * Create a new module
     */
    public function create(string $module): void;
}
