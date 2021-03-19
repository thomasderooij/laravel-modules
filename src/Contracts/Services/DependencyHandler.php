<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Contracts\Services;

interface DependencyHandler
{
    /**
     * Add a dependency between modules
     *
     * @param string $downstream
     * @param string $upstream
     */
    public function addDependency (string $downstream, string $upstream) : void;

    /**
     * Get a list of available modules for adding to the upstream list
     *
     * @param string $module
     * @return array
     */
    public function getAvailableModules (string $module) : array;

    /**
     * Fetch all the modules downstream of the one provided
     *
     * @param string $module
     * @param array|null $dependencies
     * @return array
     */
    public function getDownstreamModules (string $module, array $dependencies = null) : array;

    /**
     * Get the modules in an order that is safe to migrate, as indicated by their dependencies
     *
     * @return array
     */
    public function getModulesInMigrationOrder () : array;

    /**
     * Fetch all the modules upstream of the one provided
     *
     * @param string $module
     * @param array|null $dependencies
     * @return array
     */
    public function getUpstreamModules (string $module, array $dependencies = null) : array;
}
