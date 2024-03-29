<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Contracts\Factories;

interface ModuleMigrationFactory
{
    /**
     * Create a new migration file
     */
    public function create(): void;

    /**
     * Remove the migration file
     */
    public function undo(): void;
}
