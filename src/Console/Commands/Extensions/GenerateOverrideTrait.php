<?php

namespace Thomasderooij\LaravelModules\Console\Commands\Extensions;

use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Input\InputOption;
use Thomasderooij\LaravelModules\ParentCallTrait;
use Thomasderooij\LaravelModules\Contracts\Services\ModuleManager;
use Thomasderooij\LaravelModules\Exceptions\InitExceptions\ConfigFileNotFoundException;

trait GenerateOverrideTrait
{
    use ParentCallTrait;

    /**
     * @var ModuleManager
     */
    protected $moduleManager;

    public function __construct (Filesystem $files, ModuleManager $moduleManager)
    {
        $this->moduleManager = $moduleManager;

        // If there is a module in the workbench, change the command description to include the module name
        if ($moduleManager->isInitialised() && ($module = $moduleManager->getWorkBench()) !== null) {
            $this->attachDescriptionSuffix($module);
        }

        parent::__construct($files);
    }

    /**
     * This provides a namespace as an argument
     *
     * @param $name
     * @return string
     */
    protected function getPath($name)
    {
        // If there is no module, return default values
        $module = $this->option("module");
        if ($module === null) {
            $module = $this->moduleManager->getWorkBench();
        }

        // If there is not module, or the module is vanilla, or the modules are not initialised, go for the default
        if ($module === null || $this->isVanilla($module) || !$this->moduleManager->isInitialised()) {
            return $this->parentCall("getPath", [$name]);
        }

        // Parse the namespace to a directory location
        return base_path().'/'.str_replace('\\', '/', $name).'.php';
    }

    /**
     * Check if the module refers to the vanilla laravel code
     *
     * @param string $module
     * @return bool
     */
    protected function isVanilla (string $module) : bool
    {
        return strtolower($module) === strtolower(config("modules.vanilla"));
    }

    /**
     * Give the base Laravel description a suffix of said module
     *
     * @param string $module
     */
    protected function attachDescriptionSuffix (string $module) : void
    {
        $this->description = $this->description . " for " . ucfirst($module);
    }

    /**
     * Get the root namespace for the class.
     *
     * @return string
     * @throws ConfigFileNotFoundException
     */
    protected function rootNamespace () : string
    {
        // If there is no module option provided, grab the workbench module
        $module = $this->option("module");
        if ($module === null) {
            $module = $this->moduleManager->getWorkBench();
        }

        // If we have modules, and a module can be found, return the module namespace
        if ($this->moduleManager->isInitialised() && $module !== null && !$this->isVanilla($module)) {
            return $this->moduleManager->getModuleNamespace($module);
        }

        // If there is no module, return default namespace
        return $this->parentCall("rootNamespace");
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions () : array
    {
        $options = $this->parentCall("getOptions");
        $options[] = ["module", null, InputOption::VALUE_OPTIONAL, "Apply to this module."];

        return $options;
    }
}
