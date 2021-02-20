<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Services;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Cache;
use Thomasderooij\LaravelModules\Contracts\Services\ModuleManager as Contract;
use Thomasderooij\LaravelModules\Exceptions\InitExceptions\ConfigFileNotFoundException;
use Thomasderooij\LaravelModules\Exceptions\InitExceptions\ModulesNotInitialisedException;
use Thomasderooij\LaravelModules\Exceptions\InitExceptions\TrackerFileNotFoundException;
use Thomasderooij\LaravelModules\Exceptions\ModuleAlreadyActiveException;
use Thomasderooij\LaravelModules\Exceptions\ModuleCreationException;
use Thomasderooij\LaravelModules\Exceptions\ModuleNotActiveException;
use Thomasderooij\LaravelModules\Exceptions\ModuleNotFoundException;

class ModuleManager extends ModuleStateRepository implements Contract
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
     * @throws FileNotFoundException
     */
    public function activateModule (string $module) : void
    {
        if (!$this->hasModule($module)) {
            throw new ModuleNotFoundException("There is no module named \"$module\".");
        }
        if ($this->moduleIsActive($module)) {
            throw new ModuleAlreadyActiveException("The module \"$module\" is already active.");
        }

        $module = $this->sanitiseModuleName($module);
        $content = $this->getTrackerContent();
        $modules = $content[$this->getActiveModulesTrackerKey()];
        $modules[] = $module;
        $content[$this->getActiveModulesTrackerKey()] = array_values($modules);

        $this->save($content);
    }

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
     * @throws FileNotFoundException
     */
    public function addModule (string $module) : void
    {
        if ($this->hasModule($module)) {
            throw new ModuleCreationException("The module \"$module\" already exists.");
        }

        $content = $this->getTrackerContent();

        $modules = $content[$this->getModulesTrackerKey()];
        $modules[] = $module;
        $content[$this->getModulesTrackerKey()] = array_values($modules);
        $this->save($content);

        $this->activateModule($module);
    }

    /**
     * Clear your workbench
     *
     * @throws ModulesNotInitialisedException
     */
    public function clearWorkbench () : void
    {
        if (!$this->isInitialised()) {
            throw new ModulesNotInitialisedException("The modules need to be initialised first. You can do this by running the module:init command.");
        }

        if (($content = Cache::get($this->getCacheKey())) === null) {
            $content = [$this->getWorkbenchKey() => null];
        }

        $content[$this->getWorkbenchKey()] = null;

        Cache::put($this->getCacheKey(), $content, $this->getCacheValidity());
    }

    /**
     * Set a module to not-active
     *
     * @param string $module
     * @throws ModuleNotFoundException
     * @throws ModulesNotInitialisedException
     * @throws TrackerFileNotFoundException
     * @throws ConfigFileNotFoundException
     * @throws ModuleNotActiveException
     * @throws FileNotFoundException
     */
    public function deactivateModule (string $module) : void
    {
        if (!$this->isInitialised()) {
            throw new ModulesNotInitialisedException("The modules need to be initialised first. You can do this by running the module:init command.");
        }

        if (!$this->hasModule($module)) {
            throw new ModuleNotFoundException("There is no module named \"$module\".");
        }
        if (!$this->moduleIsActive($module)) {
            throw new ModuleNotActiveException("The module \"$module\" is already inactive.");
        }

        if ($this->sanitiseModuleName($module) === $this->getWorkbench()) {
            $this->clearWorkbench();
        }

        $content = $this->getTrackerContent();
        $modules = $content[$this->getActiveModulesTrackerKey()];
        $activeKey = array_search($this->sanitiseModuleName($module), array_map(function (string $mod) { return $this->sanitiseModuleName($mod); }, $modules));
        unset($modules[$activeKey]);
        $content[$this->getActiveModulesTrackerKey()] = array_values($modules);

        $this->save($content);
    }

    /**
     * Get a collection of your currently active modules
     *
     * @param bool $skipCheck
     * @return array
     * @throws ConfigFileNotFoundException
     * @throws ModulesNotInitialisedException
     * @throws TrackerFileNotFoundException
     */
    public function getActiveModules (bool $skipCheck = false) : array
    {
        if (!$skipCheck && !$this->isInitialised()) {
            throw new ModulesNotInitialisedException("The modules need to be initialised first. You can do this by running the module:init command.");
        } elseif ($skipCheck && !$this->hasTrackerFile()) {
            return [];
        }

        return $this->getTrackerContent()[$this->getActiveModulesTrackerKey()];
    }

    /**
     * Get the module directory path
     *
     * @param string $module
     * @return string
     * @throws ConfigFileNotFoundException
     */
    public function getModuleDirectory (string $module) : string
    {
        return $this->getModulesDirectory() . "/" . $this->sanitiseModuleName($module);
    }

    /**
     * Get the base namespace of a given module
     *
     * @param string $module
     * @param bool $includeBackslash
     * @return string
     * @throws ConfigFileNotFoundException
     */
    public function getModuleNamespace (string $module, bool $includeBackslash = true) : string
    {
        if (!$this->hasConfig()) {
            throw new ConfigFileNotFoundException("Could not locate modules file in the config directory.");
        }

        $namespace = ucfirst(config("modules.root")) . "\\" . ucfirst($this->sanitiseModuleName($module));
        if ($includeBackslash) {
            $namespace.= "\\";
        }

        return $namespace;
    }

    /**
     * @param string $module
     * @return string
     * @throws ConfigFileNotFoundException
     * @throws FileNotFoundException
     * @throws ModulesNotInitialisedException
     * @throws TrackerFileNotFoundException
     */
    public function getModuleRoot(string $module): string
    {
        return config("modules.root") . "/" . $this->sanitiseModuleName($module);
    }

    /**
     * Get the content of your workbench
     *
     * @return string|null
     */
    public function getWorkbench () : ?string
    {
        $cache = Cache::get($this->getCacheKey());

        if ($cache === null) {
            return null;
        }

        $content = $cache[$this->getWorkbenchKey()];

        return $content;
    }

    /**
     * Check if a module is active
     *
     * @param string $module
     * @return bool
     * @throws ConfigFileNotFoundException
     * @throws ModulesNotInitialisedException
     * @throws TrackerFileNotFoundException
     */
    public function moduleIsActive (string $module) : bool
    {
        $modules = array_map(function (string $mod) { return $this->sanitiseModuleName($mod); }, $this->getActiveModules());

        return array_search($this->sanitiseModuleName($module), $modules) !== false;
    }

    /**
     * Remove a module and its content from your project
     *
     * @param string $module
     * @throws ConfigFileNotFoundException
     * @throws ModuleNotFoundException
     * @throws ModulesNotInitialisedException
     * @throws TrackerFileNotFoundException
     * @throws FileNotFoundException
     */
    public function removeModule (string $module) : void
    {
        if (!$this->hasModule($module)) {
            throw new ModuleNotFoundException("There is no module named \"$module\".");
        }

        try {
            $this->deactivateModule($module);
        } catch (ModuleNotActiveException $e) {
            // Do nothing
        }

        $workbench = $this->getWorkbench();
        if ($workbench !== null && strtolower($workbench) === strtolower($module)) {
            $this->clearWorkbench();
        }

        $module = $this->sanitiseModuleName($module);
        $content = $this->getTrackerContent();
        $modules = $content[$this->getModulesTrackerKey()];
        $moduleKey = array_search($module, array_map(function ($mod) {
            return $this->sanitiseModuleName($mod); }, $modules)
        );
        unset($modules[$moduleKey]);
        $content[$this->getModulesTrackerKey()] = array_values($modules);
        $this->save($content);

        $this->files->deleteDirectories($this->getModuleDirectory($module));
    }

    /**
     * Set a module to your workbench
     *
     * @param string $module
     * @throws ConfigFileNotFoundException
     * @throws ModuleNotFoundException
     * @throws ModulesNotInitialisedException
     * @throws TrackerFileNotFoundException
     */
    public function setWorkbench (string $module) : void
    {
        if (!$this->isInitialised()) {
            throw new ModulesNotInitialisedException("The modules need to be initialised first. You can do this by running the module:init command.");
        }
        if (!$this->hasModule($module)) {
            throw new ModuleNotFoundException("There is no module named $module.");
        }
        $module = $this->sanitiseModuleName($module);

        $cacheKey = $this->getCacheKey();
        if (($content = Cache::get($cacheKey)) === null) {
            $content = [$this->getWorkbenchKey() => null];
        }

        $content[$this->getWorkbenchKey()] = $module;

        Cache::put($cacheKey, $content, $this->getCacheValidity());
    }

    /**
     * Get the active modules tracker key
     *
     * @return string
     */
    protected function getActiveModulesTrackerKey () : string
    {
        return "activeModules";
    }

    /**
     * Get the modules cache key
     *
     * @return string
     */
    protected function getCacheKey () : string
    {
        return "modules-cache";
    }

    /**
     * Get the cache validity in seconds
     *
     * @return int
     */
    protected function getCacheValidity () : int
    {
        return 60 * 60 * 24 * 7;
    }

    /**
     * Get the workbench cache key
     *
     * @return string
     */
    protected function getWorkbenchKey () : string
    {
        return "workbench";
    }
}
