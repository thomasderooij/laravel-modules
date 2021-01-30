<?php

namespace Thomasderooij\LaravelModules\Services;

use DirectoryIterator;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
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
     * Check if modules are initialised
     *
     * @return bool
     */
    public function isInitialised () : bool
    {
        return $this->hasConfig() && $this->hasTrackerFile();
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
    public function addModule (string $module) : void
    {
        $content = $this->getTrackerContent();

        if ($this->hasModule($module)) {
            throw new ModuleCreationException("The module \"$module\" already exists.");
        }

        $modules = $content->get($this->getModulesTrackerKey());
        $modules[] = $module;
        $content->put($this->getModulesTrackerKey(), $modules);
        $this->save($content);

        $this->activateModule($module);
    }

    /**
     * Remove a module and its content from your project
     *
     * @param string $module
     * @throws ConfigFileNotFoundException
     * @throws ModuleNotFoundException
     * @throws ModulesNotInitialisedException
     * @throws TrackerFileNotFoundException
     */
    public function removeModule (string $module) : void
    {
        $module = $this->sanitiseModuleName($module);
        if (!$this->hasModule($module)) {
            throw new ModuleNotFoundException("There is no module named $module.");
        }

        try {
            $this->deactivateModule($module);
        } catch (ModuleNotActiveException $e) {
            // Do nothing
        }

        if (strtoupper($this->getWorkbench()) === strtolower($module)) {
            $this->clearWorkbench();
        }

        $content = $this->getTrackerContent();
        $modules = $content->get($this->getModulesTrackerKey());
        $moduleKey = array_search(strtolower($module), array_map(function ($mod) { return strtolower($mod); }, $modules));
        unset($modules[$moduleKey]);
        $content->put($this->getModulesTrackerKey(), $modules);
        $this->save($content);

        $this->deleteDirectory($this->getModuleDirectory($module));
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
        $lower = $this->getModules()->map(function (string $mod) { return strtolower($mod); });
        return $lower->contains(strtolower($module));
    }

    /**
     * Get a collection of your currently active modules
     *
     * @param bool $skipCheck
     * @return Collection
     * @throws ConfigFileNotFoundException
     * @throws ModulesNotInitialisedException
     * @throws TrackerFileNotFoundException
     */
    public function getActiveModules (bool $skipCheck = false) : Collection
    {
        if (!$skipCheck && !$this->isInitialised()) {
            throw new ModulesNotInitialisedException("The modules need to be initialised first. You can do this by running the module:init command.");
        } elseif ($skipCheck && !$this->hasTrackerFile()) {
            return collect([]);
        }

        return collect($this->getTrackerContent()[$this->getActiveModulesTrackerKey()]);
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
     */
    public function activateModule (string $module) : void
    {
        $content = $this->getTrackerContent();

        if (!$this->hasModule($module)) {
            throw new ModuleNotFoundException("There is no module named $module.");
        }
        if ($this->moduleIsActive($module)) {
            throw new ModuleAlreadyActiveException("The specified module is already active.");
        }

        $modules = $content->get($this->getActiveModulesTrackerKey());
        $modules[] = $module;
        $content->put($this->getActiveModulesTrackerKey(), $modules);

        $this->save($content);
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
     */
    public function deactivateModule (string $module) : void
    {
        if (!$this->isInitialised()) {
            throw new ModulesNotInitialisedException("The modules need to be initialised first. You can do this by running the module:init command.");
        }

        if (!$this->hasModule($module)) {
            throw new ModuleNotFoundException("There is no module named $module.");
        }
        if (!$this->moduleIsActive($module)) {
            throw new ModuleNotActiveException("The specified module is not active.");
        }

        if (strtolower($module) === strtolower($this->getWorkbench())) {
            $this->clearWorkbench();
        }

        $content = $this->getTrackerContent();
        $modules = $content->get($this->getActiveModulesTrackerKey());
        $activeKey = array_search(strtolower($module), array_map(function (string $mod) { return strtolower($mod); }, $modules));
        unset($modules[$activeKey]);
        $content->put($this->getActiveModulesTrackerKey(), $modules);

        $this->save($content);
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
        $lower = $this->getActiveModules()->map(function (string $mod) { return strtolower($mod); });
        return $lower->contains(strtolower($module));
    }

    /**
     * Get the base namespace of a given module
     *
     * @param string $module
     * @param bool $includeBackslash
     * @return string
     * @throws ConfigFileNotFoundException
     */
    public function getModuleNameSpace (string $module, bool $includeBackslash = true) : string
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
     * Get the module directory relative path
     *
     * @param string $module
     * @return string
     */
    public function getModuleDirectory (string $module) : string
    {
        return $this->getModulesRoot() . "/" . $module;
    }

    /**
     * Get the root modules directory
     *
     * @return string
     */
    public function getModulesRoot () : string
    {
        return base_path(config("modules.root"));
    }

    /**
     * Get the tracker file name
     *
     * @return string
     */
    public function getTrackerFileName () : string
    {
        return "tracker";
    }

    /**
     * Get a collection of your modules
     *
     * @return Collection
     * @throws ConfigFileNotFoundException
     * @throws ModulesNotInitialisedException
     * @throws TrackerFileNotFoundException
     */
    protected function getModules(): Collection
    {
        if (!$this->isInitialised()) {
            throw new ModulesNotInitialisedException("The modules need to be initialised first. You can do this by running the module:init command.");
        }

        return collect($this->getTrackerContent()[$this->getModulesTrackerKey()]);
    }

    /**
     * Save tracker content to the tracker file
     *
     * @param Collection $trackerContent
     * @throws ConfigFileNotFoundException
     */
    protected function save (Collection $trackerContent): void
    {
        // Get the qualified directory to store the tracker file in
        $storageDir = base_path($this->getModuleStorageDir());

        // Get the qualified file name
        $trackerFile = $storageDir . $this->getTrackerFileName();

        // If the directory does not exist, create it with rw rw r access
        if (!is_dir($storageDir)) {
            $this->files->makeDirectory($storageDir, 0755, true);
        }

        // store the tracker content as pretty print json
        file_put_contents($trackerFile, json_encode($trackerContent->toArray(), array_sum($this->getJsonOption())));
    }

    /**
     * Get the contents of the tracker file
     *
     * @return Collection
     * @throws ConfigFileNotFoundException
     * @throws TrackerFileNotFoundException
     */
    protected function getTrackerContent () : Collection
    {
        if (!$this->hasTrackerFile()) {
            throw new TrackerFileNotFoundException("No tracker file has been located.");
        }

        $trackerFile = base_path($this->getModuleStorageDir() . $this->getTrackerFileName());

        return collect(json_decode(file_get_contents($trackerFile), true));
    }

    /**
     * See if a tracker file exists
     *
     * @return bool
     */
    protected function hasTrackerFile () : bool
    {
        try {
            $trackerFile = base_path($this->getModuleStorageDir() .$this->getTrackerFileName());
        } catch (ConfigFileNotFoundException $e) {
            return false;
        }

        return $this->files->isFile($trackerFile);
    }

    /**
     * Get the module storage directory relative path
     *
     * @return string
     * @throws ConfigFileNotFoundException
     */
    protected function getModuleStorageDir () : string
    {
        if (!$this->hasConfig()) {
            throw new ConfigFileNotFoundException("Could not locate modules file in the config directory.");
        }

        return config("modules.root")."/";
    }

    /**
     * Get the json options for storing json data to files
     *
     * @return array
     */
    protected function getJsonOption () : array
    {
        return [
            JSON_PRETTY_PRINT,
            JSON_UNESCAPED_SLASHES,
        ];
    }

    protected function hasConfig () : bool
    {
        return config("modules.root") !== null;
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
     * Get the workbench cache key
     *
     * @return string
     */
    protected function getWorkbenchKey () : string
    {
        return "workBench";
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
     * Delete a directory and its contents
     *
     * @param string $dir
     */
    protected function deleteDirectory (string $dir) : void
    {
        $this->files->delete($dir);
    }

    /**
     * Standardises module name
     *
     * @param string $module
     * @return string
     * @throws ConfigFileNotFoundException
     * @throws ModulesNotInitialisedException
     * @throws TrackerFileNotFoundException
     *
     */
    protected function sanitiseModuleName (string $module) : string
    {
        $lower = $this->getModules()->map(function (string $mod) { return strtolower($mod); });
        $key = array_search(strtolower($module), $lower->toArray());

        return $this->getModules()->get($key);
    }
}
