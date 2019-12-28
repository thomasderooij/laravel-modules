<?php

namespace Thomasderooij\LaravelModules\Contracts\Services;

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
     * Check if modules are initialised
     *
     * @return bool
     */
    public static function isInitialised () : bool;

    /**
     * Get the content of your workbench
     *
     * @return string|null
     */
    public function getWorkBench ();

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

    /**
     * Clear your workbench
     *
     * @throws ModulesNotInitialisedException
     */
    public function clearWorkbench () : void;

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
     * Get a collection of your currently active modules
     *
     * @param bool $skipCheck
     * @return Collection
     * @throws ConfigFileNotFoundException
     * @throws ModulesNotInitialisedException
     * @throws TrackerFileNotFoundException
     */
    public static function getActiveModules (bool $skipCheck = false) : Collection;

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
     * Get the base namespace of a given module
     *
     * @param string $module
     * @param bool $includeBackslash
     * @return string
     * @throws ConfigFileNotFoundException
     */
    public static function getModuleNameSpace (string $module, bool $includeBackslash = true) : string;

    /**
     * Get the module directory relative path
     *
     * @param string $module
     * @return string
     */
    public function getModuleDirectory (string $module) : string;

    /**
     * Get the tracker file name
     *
     * @return string
     */
    public static function getTrackerFileName () : string;
}
