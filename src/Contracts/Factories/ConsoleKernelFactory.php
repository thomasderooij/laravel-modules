<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Contracts\Factories;

interface ConsoleKernelFactory
{
    /**
     * Create a console kernel
     */
    public function create (string $module) : void;
}
