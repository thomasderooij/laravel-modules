<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Contracts\Services;

interface DependencyHandler
{
    /**
     * Add a dependency between modules
     */
    public function addDependency (string $downstream, string $upstream) : void;

    /**
     * Get a list of available modules for adding to the upstream list
     */
    public function getAvailableModules (string $module) : array;

    /**
     * Fetch all the modules downstream of the one provided
     */
    public function getDownstreamModules (string $module, array $dependencies = null) : array;

    /**
     * Get the modules in an order that is safe to migrate, as indicated by their dependencies
     */
    public function getModulesInMigrationOrder () : array;

    /**
     * Fetch all the modules upstream of the one provided
     */
    public function getUpstreamModules (string $module, array $dependencies = null) : array;
}
