<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Contracts\Factories;

interface AppBootstrapFactory
{
    /**
     * Rename the bootstrap file and replace it with a new one
     */
    public function create(): void;

    /**
     * Revert the bootstrap file to its original
     */
    public function undo(): void;
}
