<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Services;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;
use Thomasderooij\LaravelModules\Exceptions\InitExceptions\ConfigFileNotFoundException;
use Thomasderooij\LaravelModules\Exceptions\InitExceptions\ModulesNotInitialisedException;
use Thomasderooij\LaravelModules\Exceptions\InitExceptions\TrackerFileNotFoundException;

abstract class ModuleStateRepository
{
    /**
     * @var array|null
     */
    protected $tracker;

    /**
     * @var Filesystem
     */
    protected $files;

    public function __construct(Filesystem $files)
    {
        $this->files = $files;
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
     * Check if a module exists
     *
     * @param string $module
     * @return bool
     * @throws ConfigFileNotFoundException
     * @throws ModulesNotInitialisedException
     * @throws TrackerFileNotFoundException|FileNotFoundException
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
     * Get the active modules tracker key
     *
     * @return string
     */
    protected function getActiveModulesTrackerKey () : string
    {
        return "activeModules";
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
        if ($this->tracker !== null) {
            return $this->tracker;
        }

        if (!$this->hasTrackerFile()) {
            throw new TrackerFileNotFoundException("No tracker file has been located.");
        }

        $trackerFile = $this->getModulesDirectory() . "/" . $this->getTrackerFileName();

        $this->tracker = json_decode($this->files->get($trackerFile), true);
        return $this->tracker;
    }

    /**
     * Check if there is a module configuration file
     *
     * @return bool
     */
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

        // If we don't have the module in the modules array, we assume its a new module
        if ($key === false) {
            return $module;
        }

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
        $this->tracker = $trackerContent;
        $this->files->put($trackerFile, json_encode($trackerContent, array_sum($this->getJsonOptions())));
    }
}
