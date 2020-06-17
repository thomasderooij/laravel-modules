<?php

namespace Thomasderooij\LaravelModules\Console\Commands\Extensions\Make;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Console\TestMakeCommand as OriginalCommand;
use Thomasderooij\LaravelModules\Console\Commands\Extensions\GenerateOverrideTrait;
use Thomasderooij\LaravelModules\Contracts\Services\ModuleManager;

class TestMakeCommand extends OriginalCommand
{
    use GenerateOverrideTrait;

    public function __construct(Filesystem $files, ModuleManager $moduleManager)
    {
        $this->signature.= "{--module= : The module to which to apply this.}";

        $this->moduleManager = $moduleManager;

        // If there is a module in the workbench, change the command description to include the module name
        if (($module = $moduleManager->getWorkBench()) !== null) {
            $this->attachDescriptionSuffix($module);
        }

        parent::__construct($files, $moduleManager);
    }

    /**
     * Get the destination class path.
     *
     * @param  string  $name
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
        if ($module === null || $this->isVanilla($module) || !$this->moduleManager::isInitialised()) {
            return parent::getPath("\\$name");
        }

        $name = str_replace(
            ['\\', '/'], '', $this->argument('name')
        );

        $isUnit = $this->option("unit");

        return $this->moduleManager->getModuleDirectory($module)."/tests/" . ($isUnit ? "Unit" : "Feature") . "/{$name}.php";
    }
}
