<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Contracts\Services;

use Thomasderooij\LaravelModules\Exceptions\InitExceptions\ModulesNotInitialisedException;

interface ModuleManager
{
    /**
     * Set a module from inactive to active
     */
    public function activateModule (string $module) : void;

    /**
     * Add a module to your tracker file
     */
    public function addModule (string $module) : void;

    /**
     * Clear your workbench
     *
     * @throws ModulesNotInitialisedException
     */
    public function clearWorkbench () : void;

    /**
     * Set a module to not-active
     */
    public function deactivateModule (string $module) : void;

    /**
     * Get a collection of your currently active modules
     */
    public function getActiveModules (bool $skipCheck = false) : array;

    /**
     * Get the module directory relative path
     */
    public function getModuleDirectory (string $module) : string;

    /**
     * Get the base namespace of a given module
     */
    public function getModuleNamespace (string $module, bool $includeBackslash = true) : string;

    public function getModuleRoot (string $module) : string;

    /**
     * Get the modules base directory
     */
    public function getModulesDirectory () : string;

    /**
     * Get the tracker file name
     */
    public function getTrackerFileName () : string;

    /**
     * Get the content of your workbench
     */
    public function getWorkbench () : ?string;

    /**
     * Check if a module exists
     */
    public function hasModule (string $module) : bool;

    /**
     * Check if modules are initialised
     *
     * @return bool
     */
    public function isInitialised () : bool;

    /**
     * Check if a module is active
     */
    public function moduleIsActive (string $module) : bool;

    /**
     * Remove a module and its content from your project
     */
    public function removeModule (string $module) : void;

    /**
     * Set a module to your workbench
     */
    public function setWorkbench (string $module) : void;
}
