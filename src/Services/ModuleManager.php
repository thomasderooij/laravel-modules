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

class ModuleManager implements Contract
{
    /**
     * @var Filesystem
     */
    protected $files;

    public function __construct(Filesystem $files)
    {
        $this->files = $files;
    }

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
        return $this->getModulesDirectory() . "/" . $module;
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

        $namespace = ucfirst(config("modules.root")) . "\\" . ucfirst($module);
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
     * Get the root modules directory
     *
     * @return string
     *
     * @throws ConfigFileNotFoundException
     */
    public function getModulesDirectory () : string
    {
        if (!$this->hasConfig()) {
            throw new ConfigFileNotFoundException("Could not locate modules file in the config directory.");
        }

        return base_path(config("modules.root"));
    }

    /**
     * Get the tracker file name
     *
     * @return string
     */
    public function getTrackerFileName () : string
    {
        return ".tracker";
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
     * Check if a module exists
     *
     * @param string $module
     * @return bool
     * @throws ConfigFileNotFoundException
     * @throws ModulesNotInitialisedException
     * @throws TrackerFileNotFoundException
     */
    public function hasModule (string $module) : bool
    {
        $sanitised = array_map(function (string $mod) { return $this->sanitiseModuleName($mod); }, $this->getModules());

        return array_search($this->sanitiseModuleName($module), $sanitised) !== false;
    }

    /**
     * Check if modules are initialised
     *
     * @return bool
     */
    public function isInitialised () : bool
    {
        return $this->hasConfig() && $this->hasTrackerFile();
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

        if (strtolower($this->getWorkbench()) === strtolower($module)) {
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

        $this->files->delete($this->getModuleDirectory($module));
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
     * Get the json options for storing json data to files
     *
     * @return array
     */
    protected function getJsonOptions () : array
    {
        return [
            JSON_PRETTY_PRINT,
            JSON_UNESCAPED_SLASHES,
        ];
    }

    /**
     * Get a collection of your modules
     *
     * @return array
     * @throws ConfigFileNotFoundException
     * @throws ModulesNotInitialisedException
     * @throws TrackerFileNotFoundException
     * @throws FileNotFoundException
     */
    protected function getModules(): array
    {
        if (!$this->isInitialised()) {
            throw new ModulesNotInitialisedException("The modules need to be initialised first. You can do this by running the module:init command.");
        }

        return $this->getTrackerContent()[$this->getModulesTrackerKey()];
    }

    /**
     * Get the module storage directory relative path
     *
     * @return string
     * @throws ConfigFileNotFoundException
     */
    protected function getModulesRoot () : string
    {
        if (!$this->hasConfig()) {
            throw new ConfigFileNotFoundException("Could not locate modules file in the config directory.");
        }

        return config("modules.root");
    }

    /**
     * Get the modules tracker key
     *
     * @return string
     */
    protected function getModulesTrackerKey () : string
    {
        return "modules";
    }

    /**
     * Get the contents of the tracker file
     *
     * @return array
     * @throws ConfigFileNotFoundException
     * @throws TrackerFileNotFoundException
     * @throws FileNotFoundException
     */
    protected function getTrackerContent () : array
    {
        if (!$this->hasTrackerFile()) {
            throw new TrackerFileNotFoundException("No tracker file has been located.");
        }

        $trackerFile = $this->getModulesDirectory() . "/" . $this->getTrackerFileName();

        return json_decode($this->files->get($trackerFile), true);
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

    protected function hasConfig () : bool
    {
        return config("modules.root") !== null;
    }

    /**
     * See if a tracker file exists
     *
     * @return bool
     */
    protected function hasTrackerFile () : bool
    {
        try {
            $trackerFile = $this->getModulesDirectory() . "/" . $this->getTrackerFileName();
        } catch (ConfigFileNotFoundException $e) {
            return false;
        }

        return $this->files->isFile($trackerFile);
    }

    /**
     * Returns the module name as it was first given
     *
     * @param string $module
     * @return string
     * @throws ConfigFileNotFoundException
     * @throws ModulesNotInitialisedException
     * @throws TrackerFileNotFoundException
     * @throws FileNotFoundException
     *
     */
    protected function sanitiseModuleName (string $module) : string
    {
        $modules = $this->getModules();
        $lower = array_map(function (string $mod) { return strtolower($mod); }, $modules);
        $key = array_search(strtolower($module), $lower);

        return $modules[$key];
    }

    /**
     * Save tracker content to the tracker file
     *
     * @param array $trackerContent
     * @throws ConfigFileNotFoundException
     */
    protected function save (array $trackerContent): void
    {
        // Get the qualified directory to store the tracker file in
        $storageDir = $this->getModulesDirectory();

        // Get the qualified file name
        $trackerFile = $storageDir . "/" . $this->getTrackerFileName();

        // If the directory does not exist, create it with rw rw r access
        if (!$this->files->isDirectory($storageDir)) {
            $this->files->makeDirectory($storageDir, 0755, true);
        }

        // store the tracker content as pretty print json
        $this->files->put($trackerFile, json_encode($trackerContent, array_sum($this->getJsonOptions())));
    }
}
