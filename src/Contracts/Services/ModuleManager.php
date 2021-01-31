<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Contracts\Services;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Collection;
use Thomasderooij\LaravelModules\Exceptions\InitExceptions\ConfigFileNotFoundException;
use Thomasderooij\LaravelModules\Exceptions\InitExceptions\ModulesNotInitialisedException;
use Thomasderooij\LaravelModules\Exceptions\InitExceptions\TrackerFileNotFoundException;
use Thomasderooij\LaravelModules\Exceptions\ModuleAlreadyActiveException;
use Thomasderooij\LaravelModules\Exceptions\ModuleCreationException;
use Thomasderooij\LaravelModules\Exceptions\ModuleNotActiveException;
use Thomasderooij\LaravelModules\Exceptions\ModuleNotFoundException;

interface ModuleManager
{
    /**
     * Set a module from inactive to active
     *
     * @param string $module
     * @throws ConfigFileNotFoundException
     * @throws ModuleAlreadyActiveException
     * @throws ModuleNotFoundException
     * @throws TrackerFileNotFoundException
     * @throws ModulesNotInitialisedException
     */
    public function activateModule (string $module) : void;

    /**
     * Add a module to your tracker file
     *
     * @param string $module
     * @throws ConfigFileNotFoundException
     * @throws ModuleAlreadyActiveException
     * @throws ModuleCreationException
     * @throws ModuleNotFoundException
     * @throws TrackerFileNotFoundException
     * @throws ModulesNotInitialisedException
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
     *
     * @param string $module
     * @throws ModuleNotFoundException
     * @throws ModulesNotInitialisedException
     * @throws TrackerFileNotFoundException
     * @throws ConfigFileNotFoundException
     * @throws ModuleNotActiveException
     */
    public function deactivateModule (string $module) : void;

    /**
     * Get a collection of your currently active modules
     *
     * @param bool $skipCheck
     * @return array
     * @throws ConfigFileNotFoundException
     * @throws ModulesNotInitialisedException
     * @throws TrackerFileNotFoundException
     */
    public function getActiveModules (bool $skipCheck = false) : array;

    /**
     * Get the module directory relative path
     *
     * @param string $module
     * @return string
     */
    public function getModuleDirectory (string $module) : string;

    /**
     * Get the base namespace of a given module
     *
     * @param string $module
     * @param bool $includeBackslash
     * @return string
     * @throws ConfigFileNotFoundException
     */
    public function getModuleNamespace (string $module, bool $includeBackslash = true) : string;

    /**
     * @param string $module
     * @return string
     * @throws ConfigFileNotFoundException
     * @throws FileNotFoundException
     * @throws ModulesNotInitialisedException
     * @throws TrackerFileNotFoundException
     */
    public function getModuleRoot (string $module) : string;

    /**
     * Get the modules base directory
     *
     * @return string
     */
    public function getModulesDirectory () : string;

    /**
     * Get the tracker file name
     *
     * @return string
     */
    public function getTrackerFileName () : string;

    /**
     * Get the content of your workbench
     *
     * @return string|null
     */
    public function getWorkbench () : ?string;

    /**
     * Check if a module exists
     *
     * @param string $module
     * @return bool
     * @throws ConfigFileNotFoundException
     * @throws ModulesNotInitialisedException
     * @throws TrackerFileNotFoundException
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
     *
     * @param string $module
     * @return bool
     * @throws ConfigFileNotFoundException
     * @throws ModulesNotInitialisedException
     * @throws TrackerFileNotFoundException
     */
    public function moduleIsActive (string $module) : bool;

    /**
     * Remove a module and its content from your project
     *
     * @param string $module
     * @throws ConfigFileNotFoundException
     * @throws ModuleNotFoundException
     * @throws ModulesNotInitialisedException
     * @throws TrackerFileNotFoundException
     */
    public function removeModule (string $module) : void;

    /**
     * Set a module to your workbench
     *
     * @param string $module
     * @throws ConfigFileNotFoundException
     * @throws ModuleNotFoundException
     * @throws ModulesNotInitialisedException
     * @throws TrackerFileNotFoundException
     */
    public function setWorkbench (string $module) : void;
}
