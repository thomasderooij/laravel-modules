<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Console\Commands\Extensions;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;
use Thomasderooij\LaravelModules\ParentCallTrait;
use Thomasderooij\LaravelModules\Contracts\Services\ModuleManager;

trait GenerateOverrideTrait
{
    use ParentCallTrait;

    protected ModuleManager $moduleManager;

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
    protected function getPath($name) : string
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

    /**
     * Qualify the given model class base name.
     *
     * @param  string  $model
     * @return string
     */
    protected function qualifyModel (string $model) : string
    {
        $model = ltrim($model, '\\/');

        $model = str_replace('/', '\\', $model);

        $rootNamespace = $this->rootNamespace();

        if (Str::startsWith($model, $rootNamespace)) {
            return $model;
        }

        return $rootNamespace.config("modules.models_dir")."\\$model";
    }

    /**
     * @return string|null
     */
    protected function getModule () : string|null
    {
        // If there is no module, return default values
        $module = $this->option("module");
        if ($module === null) {
            $module = $this->moduleManager->getWorkBench();
        }

        // If there is not module, or the module is vanilla, or the modules are not initialised, go for the default
        if ($module === null || $this->isVanilla($module) || !$this->moduleManager->isInitialised()) {
            return null;
        }

        return $module;
    }

    protected function replaceClass($stub, $name): string
    {
        // todo: update this to use the original replaceClass functionality while still keeping subdirs for test directories etc in mind
        $parts = explode("\\", $name);
        $class = array_pop($parts);

        return str_replace(['DummyClass', '{{ class }}', '{{class}}'], $class, $stub);
    }
}
